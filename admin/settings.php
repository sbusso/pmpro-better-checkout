<?php
global $pmpbc_options;
$pmpbc_options = get_option( 'pmpbc_options' );
// $pmpbc_admin = get_option( 'pmpbc_admin_options' );
// $pmpbc_auth = get_option( 'pmpbc_auth' );

// $pmpbc_errors = array();

add_action('admin_menu', 'pmpro_better_checkout_menu_pages');

function pmpro_better_checkout_menu_pages() {
    // Add the top-level admin menu
    $page_title = 'PMPro Better Checkout Settings';
    $menu_title = 'PMPro Better Checkout';
    $capability = 'manage_options';
    $menu_slug = 'pmpbc';
    $function = 'pmpbc_option_page';
    add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);

    // Add submenu page with same slug as parent to ensure no duplicates
    $sub_menu_title = 'Settings';
    add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);

    // Now add the submenu page for Help
    $submenu_page_title = 'PMPro Better Checkout Help';
    $submenu_title = 'Help';
    $submenu_slug = 'pmpbc-help';
    $submenu_function = 'pmpbc_help_page';
    add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
}

function pmpbc_option_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    global $pmpbc_options;
    $pmpbc_options = get_option( 'pmpbc_options' );

    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Paid Memberships Pro - Better Checkout</h2>
    	<form id="pmpbc_options" action="options.php" method="post">
    		<?php
    			settings_fields('pmpbc_options');
    			do_settings_sections('pmpbc');    			
			?>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'pmpbc'); ?>" />
            </p>
		</form>
	</div>

	<?php
    // Render the HTML for the Settings page or include a file that does
}

add_action('admin_init', 'pmpbc_admin_init');

function pmpbc_admin_init(){

    register_setting(
        'pmpbc_options',
        'pmpbc_options',
        'pmpbc_validate_options'
    );
    
    add_settings_section(
        'pmpbc_settings',
        __('Paid Memberships Pro Better Checkout Options', 'pmpbc'),
        'pmpbc_section_text',
        'pmpbc'
    );

                
    add_settings_field(
        'pmpbc_freemember',
        __( 'Free Membership Level<br /><span style="font-size:0.85em">The ID of the Paid Memberships Pro member level that you want for your free level.</span>', 'pmpbc' ),
        'pmpbc_freemember_input',
        'pmpbc',
        'pmpbc_settings'
    );

    add_settings_field(
        'pmpbc_paidmember',
        __( 'Paid Membership Level<br /><span style="font-size:0.85em">The ID of the Paid Memberships Pro member level that you want for your paid level.</span>', 'pmpbc' ),
        'pmpbc_paidmember_input',
        'pmpbc',
        'pmpbc_settings'
    );

    add_settings_field(
        'pmpbc_discount',
        __( 'Multi-Year Discount<br /><span style="font-size:0.85em">The Discount you want to offer people who register for a multiple year membership</span>', 'pmpbc' ),
        'pmpbc_discount_input',
        'pmpbc',
        'pmpbc_settings'
    );

    add_settings_field(
        'pmpbc_discounttoggle',
        __( 'Discount Each Year<br /><span style="font-size:0.85em">Applies the discount for each year of a membership. Leaving unchecked will make the discount a flat discount regardless of the length of registration.</span>', 'pmpbc' ),
        'pmpbc_discounttoggle_input',
        'pmpbc',
        'pmpbc_settings'
    );

    add_settings_field(
        'pmpbc_multiyear0',
        __( 'Registration Length 1<br /><span style="font-size:0.85em">Eg. "2" will make a 2 year membership option. Enter -1 to turn this option off. A single year membership is included automatically.</span>', 'pmpbc' ),
        'pmpbc_multiyear0_input',
        'pmpbc',
        'pmpbc_settings'
    );

    add_settings_field(
        'pmpbc_multiyear1',
        __( 'Registration Length 2<br /><span style="font-size:0.85em">Eg. "3" will make a 3 year membership option. Enter -1 to turn this option off. A single year membership is included automatically.</span>', 'pmpbc' ),
        'pmpbc_multiyear1_input',
        'pmpbc',
        'pmpbc_settings'
    );


    add_settings_field(
        'pmpbc_multiyear2',
        __( 'Registration Length 3<br /><span style="font-size:0.85em">Eg. "5" will make a 5 year membership option. Enter -1 to turn this option off. A single year membership is included automatically.</span>', 'pmpbc' ),
        'pmpbc_multiyear2_input',
        'pmpbc',
        'pmpbc_settings'
    );
}

// Draw the section header
function pmpbc_section_text() {

}


// Draw and fill the form fields

function pmpbc_freemember_input() {
    $pmpbc_options = get_option( 'pmpbc_options' );
    
    echo "<input name='pmpbc_options[freemember]' type='text' size='3' value='$pmpbc_options[freemember]' />";
}

function pmpbc_paidmember_input() {
    $pmpbc_options = get_option( 'pmpbc_options' );
    
    echo "<input name='pmpbc_options[paidmember]' type='text' size='3' value='$pmpbc_options[paidmember]' />";
}

function pmpbc_discount_input() {
    $pmpbc_options = get_option( 'pmpbc_options' );
    
    echo "<input name='pmpbc_options[discount]' type='text' size='3' value='$pmpbc_options[discount]' />";
}

function pmpbc_discounttoggle_input() {
    $pmpbc_options = get_option( 'pmpbc_options' ); ?>

    <input type="checkbox" name="pmpbc_options[discounttoggle]" value="true" <?php checked( "true", $pmpbc_options['discounttoggle'] ); ?> />
<?php }


function pmpbc_multiyear0_input() {
    $pmpbc_options = get_option( 'pmpbc_options' );
    
    echo "<input name='pmpbc_options[multiyear0]' type='text' size='3' value='$pmpbc_options[multiyear0]' />";
}

function pmpbc_multiyear1_input() {
    $pmpbc_options = get_option( 'pmpbc_options' );
    
    echo "<input name='pmpbc_options[multiyear1]' type='text' size='3' value='$pmpbc_options[multiyear1]' />";
}

function pmpbc_multiyear2_input() {
    $pmpbc_options = get_option( 'pmpbc_options' );
    
    echo "<input name='pmpbc_options[multiyear2]' type='text' size='3' value='$pmpbc_options[multiyear2]' />";
}

function pmpbc_validate_options( $input ) {
    global $pmpbc_options;

    if( !$input['freemember'] )
        $input['freemember'] = '0';

    if( !$input['paidmember'] )
        $input['paidmember'] = '1';

    if( !$input['discount'] )
        $input['discount'] = '0';

    if ( ! isset( $input['discounttoggle'] ) )
        $input['discounttoggle'] = null;
    $input['discounttoggle'] = ( $input['discounttoggle'] == "true" ? "true" : "false" );

    if( !$input['multiyear0'] )
        $input['multiyear0'] = '2';

    if( !$input['multiyear1'] )
        $input['multiyear1'] = '3';

    if( !$input['multiyear2'] )
        $input['multiyear2'] = '5'; 

    return $input;


}

// Add a link to the plugins page 
function pmpro_better_checkout_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = PMPBC_LOC;
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=pmpbc">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

add_filter('plugin_action_links', 'pmpro_better_checkout_action_links', 10, 2);

?>
