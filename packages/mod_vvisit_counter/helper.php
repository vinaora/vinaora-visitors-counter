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
 */

// no direct access
defined('_JEXEC') or die;

class modVVisitCounterHelper{
	/*
	 * Check Parameter
	 * Result: False if the Parameter equal to "0" (zero) or "No" or "-1" or Empty
	 */
	public static function isEnabled($param){
		$param = strtolower( trim($param) );
		if (($param == "") || ($param == "0") || ($param == "no") || ($param == "-1")) return false;
		return true;
	}
	
	/*
	 * Get Digits of Digital Counter
	 * Result: String of Digits with Leading Zero Numbers
	 * Input: $number = 123, $length = 6
	 * Output: String = "000123"
	 */
	public static function getDigits( $number, $length=0){
		$length	= (int) $length;
		$number	= ($length > strlen($number)) ? substr('000000000'.$number, -$length) : $number;
		return $number;
	}
	
	/*
	 * Show Digit Counter
	 */
	public static function showDigits($digits){
		$ret = "";
		$digits = str_split($digits);
		foreach($digits as $digit){
			$ret .= "<span class=\"vdigit-$digit\" title=\"Vinaora Visitors Counter\">$digit</span>";
		}
		return $ret;
	}
	
	/*
	 * Show Statistic Icons
	 */
	public static function showStatsIcons($show){
		$ret = "";
		if ($show["today"])		$ret .= "<div class=\"vrow vstats-vtoday\"></div><div class=\"vfclear\"></div>";
		if ($show["yesterday"])	$ret .= "<div class=\"vrow vstats-vyesterday\"></div><div class=\"vfclear\"></div>";
		if ($show["xweek"])		$ret .= "<div class=\"vrow vstats-vxweek\"></div><div class=\"vfclear\"></div>";
		if ($show["lweek"])		$ret .= "<div class=\"vrow vstats-vlweek\"></div><div class=\"vfclear\"></div>";
		if ($show["xmonth"])	$ret .= "<div class=\"vrow vstats-vxmonth\"></div><div class=\"vfclear\"></div>";
		if ($show["lmonth"])	$ret .= "<div class=\"vrow vstats-vlmonth\"></div><div class=\"vfclear\"></div>";
		if ($show["all"])		$ret .= "<div class=\"vrow vstats-vall\"></div><div class=\"vfclear\"></div>";
		
		return $ret;
	}
	
	/*
	 * Show Statistic Titles
	 */
	public static function showStatsTitles($show, $title){
		$ret = "";
		if ($show["today"])		$ret .= "<div class=\"vrow\">" . $title["today"] . "</div><div class=\"vfclear\"></div>";
		if ($show["yesterday"])	$ret .= "<div class=\"vrow\">" . $title["yesterday"] . "</div><div class=\"vfclear\"></div>";
		if ($show["xweek"])		$ret .= "<div class=\"vrow\">" . $title["xweek"] . "</div><div class=\"vfclear\"></div>";
		if ($show["lweek"])		$ret .= "<div class=\"vrow\">" . $title["lweek"] . "</div><div class=\"vfclear\"></div>";
		if ($show["xmonth"])	$ret .= "<div class=\"vrow\">" . $title["xmonth"] . "</div><div class=\"vfclear\"></div>";
		if ($show["lmonth"])	$ret .= "<div class=\"vrow\">" . $title["lmonth"] . "</div><div class=\"vfclear\"></div>";
		if ($show["all"])		$ret .= "<div class=\"vrow\">" . $title["all"] . "</div><div class=\"vfclear\"></div>";
		
		return $ret;
	}
	
	/*
	 * Show Statistic
	 */
	public static function showStats($show, $titles, $totals, $timestart, $formattime="%Y-%m-%d"){
		$ret	= array("icons"=>"", "titles"=>"", "totals"=>"");
		$time	= array("today", "yesterday", "xweek", "lweek", "xmonth", "lmonth", "all");
		
		foreach($time as $duration){
			if ( $show[$duration] ){
				$str = ($duration == "all") ? "" : gmstrftime($formattime, $timestart[$duration."start"] );
				$ret["icons"]	.= "<div class=\"vrow vstats-v$duration\" title=\"$str\"></div><div class=\"vfclear\"></div>";
				$ret["titles"]	.= "<div class=\"vrow\" title=\"\">" . $titles[$duration] . "</div><div class=\"vfclear\"></div>";
				$ret["totals"]	.= "<div class=\"vrow\" title=\"\">" . $totals[$duration] . "</div>";
			}
		}
		$ret["show"] = ($ret["icons"] == "") ? false : true;
		
		return $ret;
	}
}