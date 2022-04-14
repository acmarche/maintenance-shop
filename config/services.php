<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('acmarche_maintenance_shop.to1', '%env(MAINTENANCE_SHOP_TO1)%');
    $parameters->set('acmarche_maintenance_shop.to2', '%env(MAINTENANCE_SHOP_TO2)%');
    $parameters->set('acmarche_maintenanceshop.email', '%env(MAINTENANCE_SHOP_EMAIL)%');
    $parameters->set('bootcdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\MaintenanceShop\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests}']);


};
