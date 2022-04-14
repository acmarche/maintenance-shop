<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\MaintenanceShop' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/AcMarche/MaintenanceShop/src/Entity',
                        'prefix' => 'AcMarche\MaintenanceShop',
                        'alias' => 'AcMarche\MaintenanceShop',
                    ],
                ],
            ],
        ]
    );
};
