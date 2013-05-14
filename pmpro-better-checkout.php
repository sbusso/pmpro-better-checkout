<?php
	/*
	Plugin Name: PMPro Better Checkout
	Plugin URI: http://opensource.collidercreative.com/wp/pmpro-better-checkout/
	Description: Discounts for multiple years, 2 step checkout, and more options at checkout for Paid Memberships Pro
	Version: .1
	Author: Collider Creative
	Author URI: http://www.collidercreative.com
	*/

	/*
		Collider Creative Custom Code for Multi Year Options on Checkout Page.
		This code assumes you have an annual recurring cost.
	*/


	define("PMPBC_PATH", plugin_dir_path(__FILE__) );
	define("PMPBC_DIR", dirname(__FILE__));
	define("PMPBC_LOC", plugin_basename(__FILE__));
	
	require_once(PMPBC_DIR . "/inc/functions.php");

	if ( is_admin() ) {
		require_once(PMPBC_DIR . "/admin/settings.php");
		require_once(PMPBC_DIR . "/admin/help.php");
	}

	?>