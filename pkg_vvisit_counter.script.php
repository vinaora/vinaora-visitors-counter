<?php
/**
 * @version		$Id: pkg_vvisit_counter.script.php 2012-04-27 vinaora $
 * @package		VINAORA VISITORS COUNTER
 * @subpackage	pkg_vvisit_counter
 * @copyright	Copyright (C) 2007-2012 VINAORA. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @website		http://vinaora.com
 * @twitter		http://twitter.com/vinaora
 * @facebook	http://facebook.com/vinaora
 * @google+		https://plus.google.com/111142324019789502653
 */

// no direct access
defined('_JEXEC') or die;

class pkg_VVisit_CounterInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __constructor(JAdapterInstance $adapter){
	}
 
	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter){
	}
 
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter){
	}
 
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter){
		
		$db	=& JFactory::getDBO();
		
		// Check if table exists. When not, create it
		$query	=	" CREATE TABLE IF NOT EXISTS `#__vvisit_counter` (
						`time` INT(10) UNSIGNED NOT NULL,
						`visits` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						`guests` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						`bots` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						`members` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						UNIQUE INDEX `time` (`time`),
						PRIMARY KEY (`time`)
					);";
		
		$db->setQuery($query);
		$db->query();

		// Search the lastime log
		$query	= 	" SELECT time FROM #__vvisit_counter USE INDEX(time) ORDER BY time DESC LIMIT 1;";

		$db->setQuery($query);
		$ltime	= $db->loadResult();

		// Insert the first visitor - yourself
		if (!$ltime){
			$query	=	" INSERT INTO `#__vvisit_counter` (`time`, `visits`, `members`) 
							VALUES( UNIX_TIMESTAMP(), 1, 1 ) ON DUPLICATE KEY 
							UPDATE `visits`=`visits`+1, `members`=`members`+1;";
			
			$db->setQuery($query);
			$db->query();
		}
		
		// Enabled plugin Vinaora Visitors Counter
		$query = " UPDATE `#__extensions` SET `enabled`=1 WHERE `type`='plugin' AND `element`='vvisit_counter' LIMIT 1;";
		
		$db->setQuery($query);
		$db->query();

	}
 
	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter){
	}
 
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter){
		
		// Drop table if exits
		$query	=	" DROP TABLE IF EXISTS `#__vvisit_counter`;";
		
		$db	=& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}
}
