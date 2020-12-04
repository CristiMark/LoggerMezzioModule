<?php

declare(strict_types=1);

namespace Logger;

use Doctrine\ORM\EntityManager;
use Laminas\ModuleManager\Listener\ConfigListener;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;

class Module
{
    public function init(ModuleManager $moduleManager): void
    {
        $eventManager = $moduleManager->getEventManager();
        $eventManager->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, [$this, 'doctrineTransform']);
        $eventManager->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'errorPreviewPageHandler'], 101);
    }

    public function doctrineTransform(ModuleEvent $event): void
    {
        $container = $event->getParam('ServiceManager');
        if (! $container->has(EntityManager::class)) {
            return;
        }

        $configuration = $container->get('config');
        $configuration['db'] ?? Transformer\Doctrine::transform($container, $configuration);
    }

    public function errorPreviewPageHandler(ModuleEvent $event): void
    {
        /** @var ConfigListener $configListener */
        $configListener = $event->getConfigListener();
        $configuration  = $configListener->getMergedConfig(false);

        if (! isset($configuration['logger']['enable-error-preview-page'])) {
            return;
        }

        if ($configuration['logger']['enable-error-preview-page']) {
            return;
        }

        unset(
            $configuration['controllers']['factories'][Controller\ErrorPreviewController::class],
            $configuration['controllers']['factories'][Controller\ErrorPreviewConsoleController::class],
            $configuration['router']['routes']['error-preview'],
            $configuration['console']['router']['routes']['error-preview-console']
        );

        $configListener->setMergedConfig($configuration);
    }

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
