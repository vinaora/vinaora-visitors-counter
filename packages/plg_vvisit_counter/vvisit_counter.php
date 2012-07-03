<?php
/**
 * @version		$Id: vvisit_counter.php 2012-04-27 vinaora $
 * @package		VINAORA VISITORS COUNTER
 * @subpackage	plg_vvisit_counter
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

require_once( dirname(__FILE__).DS.'helper'.DS.'vvisit_counter.php' );

class plgSystemVVisit_Counter extends JPlugin
{
	function __construct( &$subject, $config ){
		parent::__construct( $subject, $config );
	}
	
	function onAfterInitialise(){
	
		// Don't run on back-end
		$onbackend = $this->params->get('onbackend', 0);
		if ( !$onbackend && (JPATH_BASE !== JPATH_ROOT) ) return;

		$now		= time();
		$lasttime	= plgVVisitCounterHelper::getLastTime();
		$visit_type	= plgVVisitCounterHelper::visitType();
		

		if ($now > $lasttime){
			$config		= &JFactory::getConfig();
			$lifetime	= (int) $config->getValue('config.lifetime')*60;
			
			$time	= ( intval($now/$lifetime)+1 ) * $lifetime;
			self::_insertRecord($time, $visit_type);
			return;
		}

		$session	= &JFactory::getSession();
		if ( $session->isNew() ){
			self::_updateRecord($lasttime, $visit_type);
			return;
		}

	}
	
	/*
	 * Create table #__vvisit_counter
	 */
	private static function _createTable($drop = false){

		// Drop old table if exits
		$query	=	($drop) ? " DROP TABLE IF EXISTS `#__vvisit_counter`;" : "";

		// Check if table exists. When not, create it
		$query	.=	" CREATE TABLE IF NOT EXISTS `#__vvisit_counter` (
						`time` INT(10) UNSIGNED NOT NULL,
						`visits` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						`guests` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						`bots` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						`members` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
						UNIQUE INDEX `time` (`time`),
						PRIMARY KEY (`time`)
					);";

		$db	=& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();

		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}

	}

	/*
	 * Insert New Record
	 */
	private static function _insertRecord($time=0, $visit_type='guests'){
		$time	= 	(int) $time;

		$query	=	" INSERT INTO #__vvisit_counter (time, visits, $visit_type)" .
					" VALUES ($time, 1, 1) ON DUPLICATE KEY UPDATE visits=visits+1, $visit_type=$visit_type+1;";

		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	/*
	 * Update Record
	 */
	private static function _updateRecord($time=0, $visit_type='guests'){
		$time	= 	(int) $time;

		$query	=	" UPDATE #__vvisit_counter" .
					" SET visits=visits+1, $visit_type=$visit_type+1 WHERE time=$time;";

		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

}
