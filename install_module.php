<?php

/**
 * 2007-2014 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Hennes Hervé <contact@h-hennes.fr>
 *  @copyright 2013-2015 Hennes Hervé
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  http://www.h-hennes.fr/blog/
 */
//EN CLI on part du principe que c'est du dev selon la méthodologie des articles du blog
if (php_sapi_name() === 'cli') {

    $baseCliIncludePath = '/var/www/public/prestashop/';
    $psVersionFlag = false;
    //On récupére le paramètre ps_version depuis les arguments
    foreach ( $argv as $arg){
        if ( preg_match('#^ps_version=#',$arg)) {
            $psVersionFlag = true;
            $basepath = $baseCliIncludePath.str_replace('ps_version=','',$arg).'/';
        }
    }

    if ( !$psVersionFlag ){
        die("Erreur : Dans la version symlink du module , il faut preciser la version prestashop ciblee pour les actions \n via un parametre par ex : ps_version=prestashop_1-6-1-1 \n");
    }

}
else {
    //Gestion du chemin d'inclusion si le module est installé via un lien symbolique ( mode url )
    $basepath = dirname(dirname($_SERVER['SCRIPT_FILENAME'])).'/../';
}

include_once  $basepath.'config/config.inc.php';
require_once dirname(__FILE__).'/eiinstallmodulescli.php';

//Lancemement des actions d'installation
eiinstallmodulescli::process();
