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

class EiInstallModulesCli extends Module
{

	public function __construct()
	{
		$this->name = 'eiinstallmodulescli';
		$this->tab = 'others';
		$this->author = 'administration';
		$this->version = '0.1.3';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Ei Install Modules Cli');
		$this->description = $this->l('This module allow you to install others modules using the command line');
	}

	public function install()
	{
		if (!parent::install())
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}
	
	
	/**
	 * Code exécuté dans la console
	 * Le module n'a pas besoin d'être installé pour fonctionner
	 * 
	 */
	public static function process(){
	
		//Pour la ligne de commandes
		global $argv;
		
		/* Caractère de fin de ligne */
		$endOfLine = '<br />';
	
		/* Nom du module à installer */
		$module_name = Tools::getValue('module_name');
		
		/* Action à effectuer : Par défaut installation */
		$action = Tools::getValue('action', 'install');
		
		/* Flag pour permettre d'installer le module via github */
		$github = Tools::getValue('github',false);
		
		/* Actions disponibles pour le module */
		$actions_allowed = array('install', 'disable' ,'reset','delete');
		
		//Gestion via la ligne de commande
		if ( $argv ) {
		
			$endOfLine = "\n";
			$allowsKeys = array('module_name','action','github');

			foreach ( $argv as $arg ) {
				$arguments = explode('=',$arg);
				if ( in_array($arguments[0],$allowsKeys) ) {
					${$arguments[0]}= $arguments[1];
				}
			}
			
			echo "Lancement de l'installation via la ligne de commande ".$endOfLine;
		}

		if ($module_name)
		{
			/** Si le module est disponible sur github **/
			if ( $github ) {
				echo "Tentative de récupération du module depuis github".$endOfLine;;
				echo "Url du dépôt : ".$github.$endOfLine;
				//@ToDO : Récupérer les messages d'erreur + vérifier que shell_exec est autorisé
				shell_exec("git clone ".$github." "._PS_MODULE_DIR_.$module_name);
			}

			if ( $module = Module::getInstanceByName($module_name) ) {
				/* Installation du module */
				try {
					$module->install();
				} catch (PrestashopException $e) {
					echo $e->getMessage();
					exit();
				}

				echo 'Module installé avec succès'.$endOfLine;;
			
			}
			else {
				echo 'Erreur le module '.$module_name.' n\'existe pas'.$endOfLine;;
			}
		}
		else
			echo 'Pas de paramètre de module à installer'.$endOfLine;;
	}
}
