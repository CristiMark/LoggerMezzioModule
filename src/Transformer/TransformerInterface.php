<?php

declare(strict_types=1);

namespace Logger\Transformer;

use Psr\Container\ContainerInterface;

interface TransformerInterface
{
    public static function transform(ContainerInterface $container, array $configuration): ContainerInterface;
}
