<?php

namespace AcMarche\MaintenanceShop;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcMarcheMaintenanceShopBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
