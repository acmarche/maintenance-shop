<?php

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine) {

    $emMda = $doctrine->orm()->entityManager('default');
    $emMda->mapping('AcMarche\MaintenanceShop')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/MaintenanceShop/src/Entity')
        ->prefix('AcMarche\MaintenanceShop')
        ->alias('AcMarcheMaintenanceShop');
};
