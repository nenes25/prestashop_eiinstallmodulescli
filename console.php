#!/usr/bin/env php
<?php

//Autoload Composer
require_once 'vendor/autoload.php';

//Autoload Prestashop
require_once '/var/www/public/prestashop/prestashop_1-6-1-1/config/config.inc.php';

use Hhennes\PrestashopConsole\PrestashopConsoleApplication;

$app = new PrestashopConsoleApplication('PrestashopConsole','0.1.0');
//@ToDo : Faire un autoloader des commandes
$app->addCommands(array(
    //Modules
    new Hhennes\PrestashopConsole\Command\Module\EnableCommand(),
    new Hhennes\PrestashopConsole\Command\Module\DisableCommand(),
    new Hhennes\PrestashopConsole\Command\Module\ListCommand(),
    //Cache
    new Hhennes\PrestashopConsole\Command\Cache\ClearCommand(),
    /**
     * Smarty: vider le cache / activer/desactiver / forcer la compilation
     * Thèmes : purger css / js
     */
));
$app->run();
