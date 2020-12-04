<?php

declare(strict_types=1);

namespace Logger;

use Laminas\Log;

/**
 * The configuration provider for the Logger module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
                Log\LoggerAbstractServiceFactory::class => Log\LoggerAbstractServiceFactory::class,
            ],
            'factories'  => [
                Handler\Logging::class => Handler\LoggingFactory::class,
                Middleware\Mezzio::class => Middleware\MezzioFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'logger'    => [__DIR__ . '/../templates/'],
            ],
        ];
    }
}
