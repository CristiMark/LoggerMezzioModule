<?php

declare(strict_types=1);

namespace Logger\Handler\Writer\Checker;

use Closure;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Log\Writer\Db as DbWriter;
use function date;
use function strtotime;

class Db
{
    /** @var DbWriter */
    private $dbWriter;

    /** @var array */
    private $configLoggingSettings;

    /** @var array */
    private $logWritersConfig;

    public function __construct(
        DbWriter $dbWriter,
        array $configLoggingSettings,
        array $logWritersConfig
    ) {
        $this->dbWriter              = $dbWriter;
        $this->configLoggingSettings = $configLoggingSettings;
        $this->logWritersConfig      = $logWritersConfig;
    }

    public function isExists(
        // string $errorFile,
        string $errorTrace,
        int $errorLine,
        string $errorMessage,
        string $errorUrl,
        string $errorType
    ): bool
    {
        // db definition
        $db = Closure::bind(static function ($dbWriter) {
            return $dbWriter->db;
        }, null, $this->dbWriter)($this->dbWriter);

        foreach ($this->logWritersConfig as $writerConfig) {
            if ($writerConfig['name'] === 'db') {
                // table definition
                $table = $writerConfig['options']['table'];

                // columns definition
                $timestamp = $writerConfig['options']['column']['timestamp'];
                $message = $writerConfig['options']['column']['message'];
                $trace = $writerConfig['options']['column']['extra']['trace'];
                $file = $writerConfig['options']['column']['extra']['file'] ?? null;
                $line = $writerConfig['options']['column']['extra']['line'] ?? null;
                $url = $writerConfig['options']['column']['extra']['url'] ?? null;
                $error_type = $writerConfig['options']['column']['extra']['error_type'] ?? null;

                $tableGateway = new TableGateway($table, $db, null, new ResultSet());
                $select = $tableGateway->getSql()->select();
                $select->columns([$timestamp]);
                $select->where([
                    $message => $errorMessage,
                    $trace => $errorTrace,
                    // $line       => $errorLine,
                    // $url        => $errorUrl,
                    //  $file       => $errorFile,
                    //   $error_type => $errorType,

                ]);
                $select->order($timestamp . ' DESC');
                $select->limit(1);

                /** @var ResultSet $result */
                $result = $tableGateway->selectWith($select);

                if (! ($current = $result->current())) {
                    return false;
                }

                $first = $current[$timestamp];
                $last  = date('Y-m-d H:i:s');

                $diff = strtotime($last) - strtotime($first);
                if ($diff <= $this->configLoggingSettings['same-error-log-time-range']) {
                    return true;
                }
                break;
            }
        }

        return false;
    }
}
