<?php
/**
 * @version		$Id: vvisit_counter_timestart.php 2012-04-27 vinaora $
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

class plgVVisitCounterTimeStartHelper{
	public $offset=0;
	public $sunday=0;
	public $now;

	public function __construct($offset=0, $sunday=0, $now=0){
		$this->offset = (float) $offset;
		$this->sunday = (int) $sunday;

		$now = (int) $now;
		$now = ($now) ? $now : mktime();
		$this->now = $now;
	}

	/*
	 * Determine Starting Date/Time of Today, Yesterday, This Week, Last Week, This Month, Last Month
	 * Return Unix Time Array
	 */
	public function getTimeStart(){
		$now = $this->now;

		/* ------------------------------------------------------------------------------------------------ */
		// Determine GMT Time (UTC+00:00)
		// Determine this minute, this hour, this day, this month, this year
		// Don't use strftime()
		$minute			= (int) gmstrftime( "%M", $now );
		$hour			= (int) gmstrftime( "%H", $now );
		$day			= (int) gmstrftime( "%d", $now );
		$month			= (int) gmstrftime( "%m", $now );
		$year			= (int) gmstrftime( "%Y", $now );

		// Determine Starting GMT Time and Local Time of Today
		$todaystart			= gmmktime( 0,0,0,$month,$day,$year );
		$local_todaystart	= $this->localTimeStart( $todaystart, "day");

		// Determine Starting GMT Time and Local Time of Yesterday
		$yesterdaystart			= $todaystart - 86400;
		$local_yesterdaystart	= $local_todaystart - 86400;

		// Determine Starting GMT Time and Local Time of This Week
		// If Sunday is starting day of week then Sunday = 0 ... Saturday = 6
		// If Monday is starting day of week then Monday = 0 ... Sunday = 6
		$weekday			= (int) gmstrftime("%w", $now );
		$wk	= $weekday - $this->sunday;
		$weekday = ($wk > 0) ? $wk : 7+$wk;

		$xweekstart			=	$todaystart - $weekday*86400;
		$local_xweekstart	=	$this->localTimeStart( $xweekstart, "week");

		// Determine Starting GMT Time and Local Time of Last Week
		$lweekstart			=	$xweekstart - 7*86400;
		$local_lweekstart	=	$local_xweekstart - 7*86400;

		// Determine Starting GMT Time and Local Time of This Month
		$xmonthstart		=	gmmktime( 0,0,0,$month,1,$year );
		$local_xmonthstart	=	$this->localTimeStart( $xmonthstart, "month");

		// Determine Starting GMT Time and Local Time of Last Month
		// $days_lmonth: Number days of the last month (28/29, 30 or 31)
		$days_lmonth		=	(int) gmstrftime("%d", $xmonthstart - 86400 );
		$lmonthstart		=	$xmonthstart - $days_lmonth*86400;
		$local_lmonthstart	=	$local_xmonthstart - $days_lmonth*86400;
		
		$datetime	=	array();

		$datetime["todaystart"]				=	$todaystart;
		$datetime["local_todaystart"]		=	$local_todaystart;
		$datetime["yesterdaystart"]			=	$yesterdaystart;
		$datetime["local_yesterdaystart"]	=	$local_yesterdaystart;
		$datetime["xweekstart"]				=	$xweekstart;
		$datetime["local_xweekstart"]		=	$local_xweekstart;
		$datetime["lweekstart"]				=	$lweekstart;
		$datetime["local_lweekstart"]		=	$local_lweekstart;
		$datetime["xmonthstart"]			=	$xmonthstart;
		$datetime["local_xmonthstart"]		=	$local_xmonthstart;
		$datetime["lmonthstart"]			=	$lmonthstart;
		$datetime["local_lmonthstart"]		=	$local_lmonthstart;

		return $datetime;
	}

	/*
	 * Determine Local Starting Time
	 * Return Unix Time
	 * Example: If Global Time (GMT+00:00) = 1182124800 (2007/06/18 - 00:00:00)
	 *			then Local Time (GMT+07:00) = 1182124800 - 7*3600 = 1182099600,
	 *			Local Time (GMT-05:00) = 1182124800 + 5*3600 = 1182142800
	 */
	public function localTimeStart($timestart, $type="day"){
		$now 	= $this->now;
		$offset = $this->offset;

		$timestart = (int) $timestart;

		$type = strtolower(trim($type));
		$type = (($type == "week") || ($type == "month")) ? $type : "day";
		$nexttimestart = strtotime("+1 $type", $timestart);
		$lasttimestart = strtotime("-1 $type", $timestart);

		if ($offset > 0){
			$timestart = ($now > ($nexttimestart - $offset*3600)) ? $nexttimestart : $timestart;
		}
		elseif($offset < 0){
			$timestart = ($now < ($timestart - $offset*3600)) ? $lasttimestart : $timestart;
		}
		$timestart -= $offset*3600;
		
		return $timestart;
	}
}
