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

class plgVVisitCounterHelper{

	/*
	 * Query LastTime log from database
	 */
	public static function getLastTime(){
		
		// Don't use SELECT MAX(time) because it is much slower than
		$query	= " SELECT time FROM #__vvisit_counter USE INDEX(time) ORDER BY time DESC LIMIT 1;";
		
		$db	=& JFactory::getDBO();
		$db->setQuery($query);

		$ltime	= $db->loadResult();
		
		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		return $ltime;
	}

	/*
	 * Get Total Visits in the duration from $timestart to $timestop
	 */
	public static function &getVisits($timestart=0, $timestop=0){

		$timestart	= (int) $timestart;
		$timestop	= (int) $timestop;

		$total		= array('visits'=>0, 'guests'=>0, 'bots'=>0, 'members'=>0);

		$query		=	" SELECT * FROM #__vvisit_counter WHERE '1=1'";
		$query		.=	($timestart) ? " AND time > $timestart " : "";
		$query		.=	($timestop) ? " AND time <= $timestop " : "";

		// Get a database object
		$db =& JFactory::getDBO();
		$db->setQuery($query);

		$records = $db->loadObjectList();

		if ( !empty($records) && count($records) ) {
			foreach ( $records as $record ) {
				$total['visits']	+=	(int) $record->visits;
				$total['guests']	+=	(int) $record->guests;
				$total['bots']		+=	(int) $record->bots;
				$total['members']	+=	(int) $record->members;
			}
		}

		return $total;
	}

	/*
	 * Get Total Visits in the duration from $timestart to $timestop
	 */
	public static function &getVisitsSQL($timestart=0, $timestop=0){
		$timestart	= (int) $timestart;
		$timestop	= (int) $timestop;
		
		$query		=	" SELECT SUM(visits) AS visits, SUM(guests) AS guests, SUM(bots) AS bots, SUM(members) AS members FROM #__vvisit_counter WHERE '1=1'";
		$query		.=	($timestart) ? " AND time > $timestart " : "";
		$query		.=	($timestop) ? " AND time <= $timestop " : "";
		
		// Get a database object
		$db =& JFactory::getDBO();
		$db->setQuery($query);

		$total = $db->loadResultArray();
		
		return $total;
	}
	
	/*
	 * Get Visits by Type
	 */
	public static function getVisitsByType($type='visits', $timestart=0, $timestop=0){
		$timestart	= (int) $timestart;
		$timestop	= (int) $timestop;
		
		// Ensure that $type is one of visits/guests/bots/members values
		$type	= (($type == 'guests') || ($type == 'bots') || ($type == 'members')) ? $type : 'visits';

		$query		=	" SELECT SUM($type) AS $type FROM #__vvisit_counter WHERE '1=1'";
		$query		.=	($timestart) ? " AND time > $timestart " : "";
		$query		.=	($timestop) ? " AND time <= $timestop " : "";
		
		// Get a database object
		$db =& JFactory::getDBO();
		$db->setQuery($query);

		$total = $db->loadResult();
		
		return $total;
	}

	/*
	 * Get the Visit Type: member, bot or guest
	 */
	public static function visitType(){
		$type = 'members';
		$user = &JFactory::getUser();

		if ( $user->guest ) {
			$session = &JFactory::getSession();
			$user_agent = strtolower($session->get('session.client.browser'));
			if ( ( strpos($user_agent,"bot") !== false ) || ( strpos($user_agent,"crawler") !== false ) || ( strpos($user_agent,"spider") !== false ) ){
				$type = 'bots';
			}else{
				$type = 'guests';
			}
		}

		return $type;
	}

}
