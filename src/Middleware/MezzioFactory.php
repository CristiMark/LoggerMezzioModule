<?php

declare(strict_types=1);

namespace Logger\Middleware;

use Logger\Handler\Logging;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class MezzioFactory
{


    public function __invoke(ContainerInterface $container): Mezzio
    {
        $configuration = $container->get('config');

        return new Mezzio(
            $configuration['logger'],
            $container->get(Logging::class),
            $container->has(TemplateRendererInterface::class)
                ? $container->get(TemplateRendererInterface::class)
                : null
        );

    }
}
