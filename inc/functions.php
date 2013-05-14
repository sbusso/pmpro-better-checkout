<?php

	$pmpbc_options = get_option( 'pmpbc_options' );

	// USER EDITABLE OPTIONS FOR PMPRO BETTER CHECKOUT PLUGIN - Edit on the settings page in the admin menu
	
	$free_membership_level = $pmpbc_options['freemember'];		//The id of the membership level that is FREE. 5
	$paid_membership_level = $pmpbc_options['paidmember'];		//The id of the membership level that is PAID.
	$discount_per_year = $pmpbc_options['discount'];			//The amount in dollars that you offer per year as a discount for a multi-year subscription
	$mult0 = $pmpbc_options['multiyear0'];
	$mult1 = $pmpbc_options['multiyear1'];
	$mult2 = $pmpbc_options['multiyear2'];

	$multi_year_options = array($mult0, $mult1, $mult2);
						
	// BYPASS PMPro Level Selection Page
	// Forces users to create a free/inactive account first and then upgrade after
	define("PMPRO_DEFAULT_LEVEL", $free_membership_level);


	/*
		function my_getMultiYearPriceFromAnnualPrice
			converts your annual price into multi year prices with a fixed discount per year.

		int $price				price of annual membership, taken from annual membership cost
		int $num_years			number of years for membership
		int $discout_per_year	discount in dollars you offer per year
	*/
	function my_getMultiYearPriceFromAnnualPrice($price, $num_years)
	{
		global $discount_per_year, $pmpbc_options;
		if($pmpbc_options['discounttoggle'] !== "true"){
			$discount_years = 1;
		} else {
			$discount_years = $num_years;
		}
			
		//Multiply price by num_years and minus by the discount_per_year times num_years 
		//return the price rounded down to the nearest whole dollar
		//return floor(($price * $num_years)/($discount_per_year * $num_years));
		return floor(($price * $num_years) - ( $discount_years * $discount_per_year));
	}

	function my_pmpro_checkout_after_level_cost()
	{
		global $wpdb, $pmpro_currency_symbol, $multi_year_options;

		if(empty($_REQUEST['level']))
			return false;
		
		//get the original level info in case it was overriden
		$original_pmpro_level = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '" . $wpdb->escape($_REQUEST['level']) . "' AND allow_signups = 1 LIMIT 1");
		
		if(empty($original_pmpro_level))
			return false;
		
		//get the option which will be annual payment or multi-year payment
		if(!empty($_REQUEST['option']))
			$option = $_REQUEST['option'];
		else
			$option = NULL;


		//setup level objects for the multi-year versions
		if(isset($multi_year_options[0]) && $multi_year_options[0] > 0) {
			$multi_year_option_0 = wp_clone($original_pmpro_level);
			$multi_year_option_0->cycle_period = "Year";
			$multi_year_option_0->cycle_number = $multi_year_options[0];
			$multi_year_option_0->billing_amount = my_getMultiYearPriceFromAnnualPrice($original_pmpro_level->billing_amount, $multi_year_options[0]);
		}
		if(isset($multi_year_options[1]) && $multi_year_options[1] > 0) {
			$multi_year_option_1 = wp_clone($original_pmpro_level);
			$multi_year_option_1->cycle_period = "Year";
			$multi_year_option_1->cycle_number = $multi_year_options[1];
			$multi_year_option_1->billing_amount = my_getMultiYearPriceFromAnnualPrice($original_pmpro_level->billing_amount, $multi_year_options[1]);
		}
		if(isset($multi_year_options[2]) && $multi_year_options[2] > 0) {
			$multi_year_option_2 = wp_clone($original_pmpro_level);
			$multi_year_option_2->cycle_period = "Year";
			$multi_year_option_2->cycle_number = $multi_year_options[2];
			$multi_year_option_2->billing_amount = my_getMultiYearPriceFromAnnualPrice($original_pmpro_level->billing_amount, $multi_year_options[2]);
		}

		//show the option radio buttons
		if(!empty($_REQUEST['review']))
		{
			if($option)
			{
			?>
				<input type="hidden" name="option" value="<?php echo esc_attr($option); ?>" />
			<?php
			}
		}
		else
		{
		?>
		<div style="margin-bottom: 1em;">
			<p style="margin-bottom: 0;">Choose a payment plan.</p>
			
			<input type="radio" name="option" value="annual" <?php if(!$option || $option == "annual") { ?>checked="checked"<?php } ?> />
			<?php echo $pmpro_currency_symbol; ?><?php echo round($original_pmpro_level->billing_amount); ?> Annually &nbsp;
			
			
			<?php if(isset($multi_year_options[0]) && $multi_year_options[0] > 0) { ?>
				<br />
				<input type="radio" name="option" value="option_0" <?php if($option == "option_0") { ?>checked="checked"<?php } ?> />
				<?php echo $pmpro_currency_symbol; ?><?php echo round($multi_year_option_0->billing_amount); ?> Every <?php echo $multi_year_options[0] ?> Years
			<?php } ?>

			
			<?php if(isset($multi_year_options[1])  && $multi_year_options[1] > 0) { ?>
				<br />
				<input type="radio" name="option" value="option_1" <?php if($option == "option_1") { ?>checked="checked"<?php } ?> />
				<?php echo $pmpro_currency_symbol; ?><?php echo round($multi_year_option_1->billing_amount); ?> Every <?php echo $multi_year_options[1] ?> Years
			<?php } ?>

			
			<?php if(isset($multi_year_options[2])  && $multi_year_options[2] > 0) { ?>
				<br />
				<input type="radio" name="option" value="option_2" <?php if($option == "option_2") { ?>checked="checked"<?php } ?> />
				<?php echo $pmpro_currency_symbol; ?><?php echo round($multi_year_option_2->billing_amount); ?> Every <?php echo $multi_year_options[2] ?> Years
			<?php } ?>

		</div>		
		<?php
		}
	}


	function my_pmpro_paypal_express_return_url_parameters($params)
	{
		if(!empty($_REQUEST['option']))
			$params['option'] = $_REQUEST['option'];
		
		return $params;
	}
	

	function my_pmpro_level_cost_text($text, $level)
	{
		//blank out the text on the checkout page
		if(!empty($_REQUEST['level']))
		{
			$text = "";
		}

		return $text;
	}


	function my_pmpro_checkout_level($level)
	{
		//need this in case $level = false or something else
		if(!$level->id)
			return $level;

		//check the option and adjust the level
		if(!empty($_REQUEST['option']))
		{
			$option = $_REQUEST['option'];
			if($option == "option_0")
			{
				//adjust the price and billing period
				$level->initial_payment = my_getMultiYearPriceFromAnnualPrice($level->initial_payment, $multi_year_options[0]);
				$level->billing_amount = my_getMultiYearPriceFromAnnualPrice($level->billing_amount, $multi_year_options[0]);
				$level->cycle_period = "Year";
				$level->cycle_number = $multi_year_options[0];
			} elseif($option == "option_1") {
				//adjust the price and billing period
				$level->initial_payment = my_getMultiYearPriceFromAnnualPrice($level->initial_payment, $multi_year_options[1]);
				$level->billing_amount = my_getMultiYearPriceFromAnnualPrice($level->billing_amount, $multi_year_options[1]);
				$level->cycle_period = "Year";
				$level->cycle_number = $multi_year_options[1];
			} elseif($option == "option_2") {
				//adjust the price and billing period
				$level->initial_payment = my_getMultiYearPriceFromAnnualPrice($level->initial_payment, $multi_year_options[2]);
				$level->billing_amount = my_getMultiYearPriceFromAnnualPrice($level->billing_amount, $multi_year_options[2]);
				$level->cycle_period = "Year";
				$level->cycle_number = $multi_year_options[2];
			}
			else
			{
				//keep it the same
			}		
		}
		
		return $level;
	}

	// function my_pmpro_free_text($text, $level){
	// 	if(pmpro_isLevelFree($level))
	// 		return "";	
	// 	else
	// 		return $text;
	// }

	function my_pmpro_pages_shortcode_checkout($content)
	{
		ob_start();
		include(PMPBC_PATH . "templates/checkout.php");
		$temp_content = ob_get_contents();
		ob_end_clean();
		return $temp_content;
	}

	function my_pmpro_pages_shortcode_confirmation($content)
	{
		ob_start();
		include(PMPBC_PATH . "templates/confirmation.php");
		$temp_content = ob_get_contents();
		ob_end_clean();
		return $temp_content;
	}	

	function my_pmpro_pages_shortcode_account($content)
	{
		ob_start();
		include(PMPBC_PATH . "templates/account.php");
		$temp_content = ob_get_contents();
		ob_end_clean();
		return $temp_content;
	}	





		add_action("pmpro_checkout_after_level_cost", "my_pmpro_checkout_after_level_cost");
		add_filter("pmpro_paypal_express_return_url_parameters", "my_pmpro_paypal_express_return_url_parameters");
		add_filter("pmpro_level_cost_text", "my_pmpro_level_cost_text", 10, 2);
		add_filter("pmpro_checkout_level", "my_pmpro_checkout_level");


		add_filter("pmpro_pages_shortcode_checkout", "my_pmpro_pages_shortcode_checkout");
		add_filter("pmpro_pages_shortcode_confirmation", "my_pmpro_pages_shortcode_confirmation");
		add_filter("pmpro_pages_shortcode_account", "my_pmpro_pages_shortcode_account");

?>