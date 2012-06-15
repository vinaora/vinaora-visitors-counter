<?php
/**
 * @version		$Id: helper.php 2012-04-27 vinaora $
 * @package		VINAORA VISITORS COUNTER
 * @subpackage	mod_vvisit_counter
 * @copyright	Copyright (C) 2007-2012 VINAORA. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @website		http://vinaora.com
 * @twitter		http://twitter.com/vinaora
 * @facebook	http://facebook.com/vinaora
 * @google+		https://plus.google.com/111142324019789502653
 *
 * @warning		Don't EDIT or DELETE link http://vinaora.com on the footer of module. Please see details at http://vinaora.com/vinaora-visitors-counter/
 *
 */

// no direct access
defined('_JEXEC') or die;

$enabled = JPluginHelper::isEnabled('system', 'vvisit_counter');
if ( !$enabled ){
	echo JText::_("MOD_VVISIT_COUNTER_VERROR_101");
	return;
}

// Turn on log file
if( JRequest::getInt('vvisit_counter_log', 0) ){
	jimport('joomla.error.log');
	$datelog = date('Y_m_d');
	$log = &JLog::getInstance("mod_vvisit_counter.log.$datelog.php", null, JPATH_BASE.'/logs/vvisit_counter');
	// $log->addEntry(array('category' => 'params', 'message' => $params->get('nowis')));
}

// Require the base helper class only once
require_once dirname(__FILE__).DS.'helper.php';

// Get basic parameters
$mode	= $params->get('mode', 'full');

$show	= array();
switch($mode){
	case 'simple':
		$show["stats"]	= false;
		$show["online"]	= false;
		break;
	case 'standard':
		$show["stats"]	= true;
		$show["online"]	= false;
		break;
	case 'full':
		$show["stats"]	= true;
		$show["online"]	= true;
		break;
	case 'custom':
		break;
}

$digit_type				= $params->get('digit_type', 'default');
$stats_type				= $params->get('stats_type', 'default');

$show["digit"]			= modVVisitCounterHelper::isEnabled( $digit_type );
$show["stats"]			= modVVisitCounterHelper::isEnabled( $stats_type );

$titles["today"]		= $params->get('today', 'Today');
$titles["yesterday"]	= $params->get('yesterday', 'Yesterday');
$titles["xweek"]		= $params->get('xweek', 'This Week');
$titles["lweek"]		= $params->get('lweek', 'Last Week');
$titles["xmonth"]		= $params->get('xmonth', 'This Month');
$titles["lmonth"]		= $params->get('lmonth', 'Last Month');
$titles["all"]			= $params->get('all', 'All days');

if ( $show["stats"] ){
	$show["today"]		= modVVisitCounterHelper::isEnabled( $titles["today"] );
	$show["yesterday"]	= modVVisitCounterHelper::isEnabled( $titles["yesterday"] );
	$show["xweek"]		= modVVisitCounterHelper::isEnabled( $titles["xweek"] );
	$show["lweek"]		= modVVisitCounterHelper::isEnabled( $titles["lweek"] );
	$show["xmonth"]		= modVVisitCounterHelper::isEnabled( $titles["xmonth"] );
	$show["lmonth"]		= modVVisitCounterHelper::isEnabled( $titles["lmonth"] );
	$show["all"]		= modVVisitCounterHelper::isEnabled( $titles["all"] );
}else{
	$show["today"]		= false;
	$show["yesterday"]	= false;
	$show["xweek"]		= false;
	$show["lweek"]		= false;
	$show["xmonth"]		= false;
	$show["lmonth"]		= false;
	$show["all"]		= false;
}

$number_digits	= (int) $params->get('number_digits', 7);
$firstday		= (int) $params->get('firstday', 0);

// Add Digit Counter and Stats Icon stylesheets to <head> tag
if( $show["digit"] ) JHtml::stylesheet("media/mod_vvisit_counter/digit_counter/$digit_type.css");
if( $show["stats"] ) JHtml::stylesheet("media/mod_vvisit_counter/stats/$stats_type.css");

// Get TimeZone from Global Configuration
$app	= &JFactory::getApplication();
$tz		= $app->getCfg('offset');

// Get Now time and Offset (hours)
$date	= &JFactory::getDate('now', $tz);
$now	= $date->toUnix();
$offset = $date->getOffsetFromGMT(true);

// Get TimeStart: Today, Yesterday, This week, Last week, This month, Last month
require_once JPATH_PLUGINS.'/system/vvisit_counter/helper/vvisit_counter_timestart.php';

$timeHelper	= new plgVVisitCounterTimeStartHelper($offset, $firstday);
$time		= $timeHelper->getTimeStart();

require_once JPATH_PLUGINS.'/system/vvisit_counter/helper/vvisit_counter.php';

$visits		= 0;
$totals		= array();

$visit_type	= $params->get('visit_type', 'visits');

// Get a reference to the global cache object.
$cache_time		= (int) $params->get( 'cache_time', 900 );
$cache_enabled	= modVVisitCounterHelper::isEnabled($cache_time) && $params->get( 'cache', 0 );

$cache	= &JFactory::getCache('mod_vvisit_counter');
$cache->setCaching( $cache_enabled );
$cache->setLifeTime( $cache_time );

// Count Today's Visits
$visits				= plgVVisitCounterHelper::getVisitsByType($visit_type, $time["local_todaystart"] );
$totals["today"]	= $visits;

// Count Yesterday's Visits
if ( $show["yesterday"] ){
	if ( $cache_enabled ){
		$visits	= $cache->call( array( 'plgVVisitCounterHelper', 'getVisitsByType' ), $visit_type, $time["local_yesterdaystart"], $time["local_todaystart"] );
	}else{
		$visits	= plgVVisitCounterHelper::getVisitsByType($visit_type, $time["local_yesterdaystart"], $time["local_todaystart"]);
	}
	$totals["yesterday"]	= (int) $visits;
}

// Count This Week's Visits
if ( $show["xweek"] ){
	if ( $cache_enabled ){
		$visits	= $cache->call( array( 'plgVVisitCounterHelper', 'getVisitsByType' ), $visit_type, $time["local_xweekstart"], $time["local_todaystart"] );
	}else{
		$visits	= plgVVisitCounterHelper::getVisitsByType($visit_type, $time["local_xweekstart"], $time["local_todaystart"]);
	}
	$totals["xweek"]	= (int) $visits + $totals["today"];
}

// Count Last Week's Visits
if ( $show["lweek"] ){
	if ( $cache_enabled ){
		$visits	= $cache->call( array( 'plgVVisitCounterHelper', 'getVisitsByType' ), $visit_type, $visit_type, $time["local_lweekstart"], $time["local_xweekstart"] );
	}else{
		$visits	= plgVVisitCounterHelper::getVisitsByType($visit_type, $time["local_lweekstart"], $time["local_xweekstart"]);
	}
	$totals["lweek"]	= (int) $visits;
}

// Count This Month's Visits
if ( $show["xmonth"] ){
	if ( $cache_enabled ){
		$visits	= $cache->call( array( 'plgVVisitCounterHelper', 'getVisitsByType' ), $visit_type, $time["local_xmonthstart"], $time["local_todaystart"] );
	}else{
		$visits				= plgVVisitCounterHelper::getVisitsByType($visit_type, $time["local_xmonthstart"], $time["local_todaystart"]);
	}
	$totals["xmonth"]	= (int) $visits + $totals["today"];
}

// Count Last Month's Visits
if ( $show["lmonth"] ){
	if ( $cache_enabled ){
		$visits	= $cache->call( array( 'plgVVisitCounterHelper', 'getVisitsByType' ), $visit_type, $time["local_lmonthstart"], $time["local_xmonthstart"] );
	}else{
		$visits	= plgVVisitCounterHelper::getVisitsByType($visit_type, $visit_type, $time["local_lmonthstart"], $time["local_xmonthstart"]);
	}
	$totals["lmonth"]	= (int) $visits;
}

// Count All Visits
if ( $show["all"] || $show["digit"] ){
	if ( $cache_enabled ){
		$visits	= $cache->call( array( 'plgVVisitCounterHelper', 'getVisitsByType' ), $visit_type, 0, $time["local_todaystart"] );
	}else{
		$visits	= plgVVisitCounterHelper::getVisitsByType($visit_type, 0, $time["local_todaystart"]);
	}
	$totals["all"]	= (int) $visits + $totals["today"];
}

$totals["all"] += (int) $params->get('initialvalue', 0);

// Show non-zero statistic or not
$autohide = $params->get('autohide', 0);

if ( $autohide && empty($totals["today"]) )		$show["today"]		= false;
if ( $autohide && empty($totals["yesterday"]) )	$show["yesterday"]	= false;
if ( $autohide && empty($totals["xweek"]) )		$show["xweek"]		= false;
if ( $autohide && empty($totals["lweek"]) )		$show["lweek"]		= false;
if ( $autohide && empty($totals["xmonth"]) )	$show["xmonth"]		= false;
if ( $autohide && empty($totals["lmonth"]) )	$show["lmonth"]		= false;
if ( $autohide && empty($totals["all"]) )		$show["all"]		= false;

// Show Digital Counter
if ( $show["digit"] ){
	$digits	= modVVisitCounterHelper::getDigits($totals["all"], $number_digits);
	$digits	= modVVisitCounterHelper::showDigits($digits);
}else{
	$digits	= "";
}

// Show Statistics Table
$formattime = $params->get('formattime', "%Y-%m-%d");
$stats		= ( $show["stats"] ) ? modVVisitCounterHelper::showStats($show, $titles, $totals, $time, $formattime) : "";

$hrfooter	= $params->get('hrfooter', 1);

$width		= $params->get('width', '');
$width		= preg_replace("/\s/", "", $width);
$width		= preg_replace("/([0-9]+)$/", "$1px", $width);

$bg			= $params->get('bg', 0);
$bgcolor	= $params->get('bgcolor', '#ffffff');

$bd			= $params->get('bd', 0);
$bdcolor	= $params->get('bdcolor', '#ffffff');
$bdwidth	= $params->get('bdwidth', 10);
$bdrounded	= $params->get('bdrounded', 1);
$bdshadow	= $params->get('bdshadow', 1);

// Detect Guest's IP Address
$ip			= $params->get('showip', 'Your IP:');
$show["ip"]	= modVVisitCounterHelper::isEnabled( $ip );
$ip = ( $show["ip"] && !empty($_SERVER['REMOTE_ADDR']) ) ? $ip." ".$_SERVER['REMOTE_ADDR'] : "";

$nowis			= $params->get('nowis', 'Now is: %Y-%m-%d %H:%M:%S');

$show["nowis"]	= modVVisitCounterHelper::isEnabled( $nowis );
$nowis	= ( $show["nowis"] ) ? $date->toFormat($nowis, true) : "";

// $duration	= $params->get('duration', 7);
// $visits		= plgVVisitCounterHelper::getVisitsByType($visit_type, $time["local_todaystart"] - $duration*86400, $time["local_todaystart"]);

// $xonline	= $params->get('xonline', 'In %s days ago has %s visits(s) online');
// $xonline	= sprintf($xonline, $duration, $visits);

$mid		= $module->id;
$customcss	= strip_tags($params->get('customcss'));
$customcss	= str_replace("#vvisit_counter", "#vvisit_counter".$mid, $customcss);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_vvisit_counter', $params->get('layout', 'default'));
echo base64_decode($params->get('home'));
