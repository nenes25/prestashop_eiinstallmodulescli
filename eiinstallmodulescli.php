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
class eiinstallmodulescli extends Module
{
    /** Caractères de fin de ligne */
    public static $endOfLine = '<br />';

    /** Actions Autorisées pour un module */
    public static $modulesActionsAllowed = array('install', 'uninstall' ,'enable','disable' ,'status');

    /** Actions autorisées pour une configuration */
    public static $configurationActionsAllowed = array('update','delete','get');
	
	/** Flag pour mode verbeux ou non */
	public static $verbose = true;

    public function __construct()
    {
        $this->name = 'eiinstallmodulescli';
        $this->tab = 'others';
        $this->author = 'administration';
        $this->version = '0.2.3';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Ei Install Modules Cli');
        $this->description = $this->l('This module allow you to install others modules, or to update shop configuration using the command line');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    /**
     * Code exécuté dans la console
     * Le module n'a pas besoin d'être installé pour fonctionner.
     *
     * @ToDO : Gestion des traductions + Refactoring
     */
    public static function process()
    {

        //Pour la ligne de commandes
        global $argv;

        $mode = Tools::getValue('mode', 'module');
		$verbose = Tools::getValue('verbose',true);
		
		if ( $verbose == 0 )
			self::$verbose = false;
		
        if ($argv) {
            self::$endOfLine = "\n";
        }

        if ($mode == 'configuration') {
            self::_processConfiguration();
        } else {
            self::_processModule();
        }
    }

    /**
     * Actions sur les modules.
     */
    protected static function _processModule()
    {
        //Pour la ligne de commandes
        global $argv;
		
        // Nom du module à installer
        $module_name = Tools::getValue('module_name');

        // Action à effectuer : Par défaut installation
        $action = Tools::getValue('action', 'install');

        // Flag pour permettre d'installer le module via github
        $github = Tools::getValue('github', false);

        //Gestion via la ligne de commande
        if ($argv) {
            $endOfLine = "\n";
            $allowsKeys = array('module_name','action','github');

            foreach ($argv as $arg) {
                $arguments = explode('=', $arg);
                if (in_array($arguments[0], $allowsKeys)) {
                    ${$arguments[0]} = $arguments[1];
                }
            }
			
			if ( self::$verbose )
				echo 'Lancement via la ligne de commande '.self::$endOfLine;
        }

        // Si l'action demandéé n'est pas autorisée , on affiche un message d'erreur
        if (!in_array($action, self::$modulesActionsAllowed)) {
            exit('Erreur : action demandée non autorisée'.self::$endOfLine);
        }

        //Si le module est disponible sur github
        if ($github) {
            echo 'Tentative de récupération du module depuis github'.self::$endOfLine;
            echo 'Url du dépôt : '.$github.$endOfLine;
            //@ToDO : Récupérer les messages d'erreur + vérifier que shell_exec est autorisé
            shell_exec('git clone '.$github.' '._PS_MODULE_DIR_.$module_name);
        }

        if ($module = Module::getInstanceByName($module_name)) {

            // Pour les actions enable / disable : il faut s'assurer que le module est installé
            if (($action == 'enable' || $action == 'disable') && !Module::isInstalled($module->name)) {
                exit('Erreur : le module '.$module_name.' n\'est pas installé. Il ne peut pas être activé / désactivé '.self::$endOfLine);
            }
			
			//Affichage du statut du module (Installé ou non )
			if ( $action =='status' ) {
				if ( Module::isInstalled($module->name) )
				{
					if ( self::$verbose )
						echo 'Le module '.$module->name.' est bien installé'.self::$endOfLine;
					else
						echo 1;
				} else {
					if ( self::$verbose )
						echo 'Le module '.$module->name.' n\'est pas installé'.self::$endOfLine;
					else
						echo 0;
				}
				exit();	
			}

            // Exécution de l'action du module
            try {
                $module->$action();
            } catch (PrestashopException $e) {
                echo $e->getMessage();
                exit();
            }
			
			if ( self::$verbose )
				echo 'Module '.$module_name.' action : '.$action.' effectuée avec succès'.self::$endOfLine;
				
        } else {
            echo 'Erreur le module '.$module_name.' n\'existe pas'.self::$endOfLine;
        }
    }

     /**
      * Actions sur la configuration.
      */
     protected static function _processConfiguration()
     {

        //Pour la ligne de commandes
        global $argv;

        //Récupération des valeurs
        $key = Tools::getValue('key');
        $value = Tools::getValue('value',-1);
        $action_conf = Tools::getValue('action_conf', 'update');
				
        //Gestion via la ligne de commande
        if ($argv) {
            $allowsKeys = array('key','value','action_conf');

            foreach ($argv as $arg) {
                $arguments = explode('=', $arg);
                if (in_array($arguments[0], $allowsKeys)) {
                    ${$arguments[0]} = $arguments[1];
                }
            }
			
			if ( self::$verbose )
				echo 'Lancement via la ligne de commande '.self::$endOfLine;
        }

         if (!$key && $value == -1 ) {
             exit('Erreur Pas de clé et de valeur définie pour la configuration'.self::$endOfLine);
         }

         if (!in_array($action_conf, self::$configurationActionsAllowed)) {
             exit('Erreur action non autorisée pour la configuration '.self::$endOfLine);
         }

         if ($action_conf == 'update') {
			echo 'On est bien dans update';
			echo 'value '.$value;
			if ( $value == -1 )
				exit('Erreur Impossible de mettre à jour la configuration, pas de valeur défine');
             Configuration::UpdateValue($key, $value);
         }
		 elseif ( $action_conf == 'get'){
			if ( self::$verbose )
				echo 'Valeur de la configuration'.$key.' '.Configuration::get($key).self::$endOfLine;
			else	
				echo Configuration::get($key);
		 }else {
            Configuration::deleteByName($key);
         }

		 if ( self::$verbose )
			echo $action_conf.' effectuee pour la cle '.$key.' '.self::$endOfLine;
     }
}
