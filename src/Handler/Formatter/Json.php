<?php

declare(strict_types=1);

namespace Logger\Handler\Formatter;

use DateTime;
use Laminas\Log\Formatter\FormatterInterface;
use Laminas\Log\Formatter\Json as BaseJson;

use function json_encode;
use function str_replace;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const PHP_EOL;

class Json extends BaseJson implements FormatterInterface
{
    /**
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event): string
    {
        static $timestamp;

        if (! $timestamp && isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $timestamp = $event['timestamp']->format($this->getDateTimeFormat());
        }
        $event['timestamp'] = $timestamp;

        return str_replace(
            '\n',
            PHP_EOL,
            (string) json_encode($event, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
