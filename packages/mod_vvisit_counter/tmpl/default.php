<?php
/**
 * @version		$Id: default.php 2012-04-27 vinaora $
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
?>
<!-- Vinaora Visitors Counter >> http://vinaora.com/ -->
<style type="text/css">
	.vfleft{float:left;}.vfright{float:right;}.vfclear{clear:both;}.valeft{text-align:left;}.varight{text-align:right;}.vacenter{text-align:center;}
<?php if($width != "") {?>
	#vvisit_counter<?php echo $mid; ?>{width:<?php echo $width; ?>;}
<?php } ?>
<?php if($bg) {?>
	#vvisit_counter<?php echo $mid; ?>{background-color:<?php echo $bgcolor; ?>}
<?php } ?>
<?php if($bd) {?>
	#vvisit_counter<?php echo $mid; ?>{border:<?php echo $bdwidth; ?>px solid <?php echo $bdcolor; ?>;}
<?php if($bdrounded){ ?>
	#vvisit_counter<?php echo $mid; ?>{
		-moz-border-radius: 8px 8px 8px 8px;
		-webkit-border-radius: 8px 8px 8px 8px;
		border-radius: 8px 8px 8px 8px;
	}
<?php } ?>
<?php if($bdshadow){ ?>
	#vvisit_counter<?php echo $mid; ?>{
		-webkit-box-shadow: 0px 1px 5px 0px #4a4a4a;
		-moz-box-shadow: 0px 1px 5px 0px #4a4a4a;
		box-shadow: 0px 1px 5px 0px #4a4a4a;
	}
<?php } ?>
<?php } ?>
	#vvisit_counter<?php echo $mid; ?> .vstats_counter{margin-top: 5px;}
	#vvisit_counter<?php echo $mid; ?> .vrow{height:24px;}
	#vvisit_counter<?php echo $mid; ?> .vstats_icon{margin-right:5px;}
<?php if($customcss) { ?>
	<?php echo $customcss; ?>
<?php } ?>
</style>
<div id="vvisit_counter<?php echo $mid; ?>" class="vvisit_counter<?php echo $moduleclass_sfx;?> vacenter">
<?php if ( $show["digit"] ) { ?>
	<div class="vdigit_counter"><?php echo $digits; ?></div>
<?php } ?>
<?php if ( $show["stats"] && $stats["show"] ) { ?>
	<div class="vstats_counter">
		<div class="vstats_icon vfleft varight">
			<?php echo $stats["icons"]; ?>
		</div>
		<div class="vstats_title vfleft valeft">
			<?php echo $stats["titles"]; ?>
		</div>
		<div class="vstats_number varight">
			<?php echo $stats["totals"]; ?>
		</div>
		<div class="vfclear"></div>
	</div>
<?php } ?>
<?php if ( $hrfooter ) { ?>
	<hr style="margin-bottom: 5px;"/>
<?php } ?>
<?php if ( $show["ip"] ) { ?>
	<div style="margin-bottom: 5px;"><?php echo $ip; ?></div>
<?php } ?>
<?php if ( $show["nowis"] ) { ?>
	<div><?php echo $nowis; ?></div>
<?php } ?>