<?php
/**
 * 2007-2014 PrestaShop
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
 *  @copyright 2013-2014 Hennes Hervé
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  http://www.h-hennes.fr/blog/
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');

/* Nom du module à installer */
$module_name = Tools::getValue('module_name');
/* Action à effectuer */
$action = Tools::getValue('action', 'install');
/* Actions disponibles pour le module */
$actions_allowed = array('install', 'disable');


if ($module_name)
{

	/* Inclusion de la classe du module */
	if (!is_file(dirname(__FILE__).'/../'.$module_name.'/'.$module_name.'.php'))
		die(' Erreur : Impossible d\'inclure le fichier du module');

	include_once ( dirname(__FILE__).'/../'.$module_name.'/'.$module_name.'.php');

	/* Installation du module */
	try {
		$module = new $module_name();
		$module->$action();
	} catch (PrestashopException $e) {
		echo $e->getMessage();
		exit();
	}

	echo 'Module installé avec succès';
}
else
	echo 'Pas de paramètre de module à installer';
?>