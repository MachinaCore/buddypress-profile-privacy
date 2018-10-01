<?php
/*
Plugin Name: Simple Buddypress Profile Privacy
Plugin URI: http://justin-hansen.com/buddypress-profile-privacy
Description: Allow each user to decide if everyone, only logged in users or only friends can see their profile tabs.
Version: 0.7.8
Author: Justin Hansen
Author URI: http://justin-hansen.com
Text Domain: simple-buddypress-profile-privacy
Domain Path: /languages
License: GPL2
*/

/**
 * Load files
 */
function sbpp04_include() {
    //Define constants used in plugin.
    define( 'SBPP04_PLUGIN_DIR', dirname( __FILE__ ) );
    define( 'SBPP04_PLUGIN_DOMAIN', 'sbpp04-profile-privacy' );
    define( "SBPP04_VIEW_LOGGED_IN", "logged-in" );
    define( "SBPP04_VIEW_FRIENDS", "friends" );
    define( "SBPP04_VIEW_EVERYONE", "everyone" );
    define( "SBPP04_PRIVACY_SETTING_KEY", "bpp_profile_privacy");
    define( "SBPP04_HIDE_DIRECTORY_KEY", "bpp_hide_directory" );
    define( "SBPP04_ADMIN_HIDE_DIRECTORY_KEY", "sbpp04-hide-directory" );
    define( "SBPP04_ADMIN_NOTIFICATION_HIDDEN_KEY", "sbpp04-update-notice-hidden" );
    define( "SBPP04_FRIENDS_ACTIVE", bp_is_active( 'friends' ) );

    if( is_admin() ){
        require( dirname( __FILE__ ) . '/includes/buddypress-profile-privacy-admin.php' );
    }else{
        require( dirname( __FILE__ ) . '/includes/buddypress-profile-privacy.php' );
    }
}
add_action( 'bp_include', 'sbpp04_include' );

function sbpp04_load_plugin_textdomain() {
	load_plugin_textdomain( 'simple-buddypress-profile-privacy', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'sbpp04_load_plugin_textdomain' );

/*
 * Enqueue admin plugin js
 */
function sbpp04_admin_enqueue(){
    wp_enqueue_script( 'simple-buddypress-profile-privacy-admin', plugins_url( 'js/simple-buddypress-profile-privacy-admin.js', __FILE__), array( 'jquery' ), '0.7', true );
}
add_action( 'admin_enqueue_scripts', 'sbpp04_admin_enqueue' );

/*
 * The below functions are here because they need access to AJAX.
 */

/*
 * Hide members from directory and search.
 */
function sbpp04_hide_from_search( $query_string = false, $object = false ){
    if( bp_get_option( SBPP04_ADMIN_HIDE_DIRECTORY_KEY ) ) {

        if( 'members' != $object && 'friends' != $object ) {
            return $query_string;
        }

        $args = wp_parse_args( $query_string );
        if( !empty( $args['user_id'] ) ){
            return $query_string;
        }

        //sbpp04_get_hidden_members() is located in /includes/buddypress-profile-privacy.php
        $exclude_query = sbpp04_get_hidden_members();

        if (!empty($exclude_query->results)) {
            foreach ($exclude_query->results as $user) {
                if( !empty( $args['exclude'] ) ){
                    $args['exclude'] = $args['exclude'] . ',' . $user->ID;
                }else{
                    $args['exclude'] = $user->ID;
                }
            }
        }

        $query_string = build_query( $args );
    }
    return $query_string;
}
add_filter( 'bp_ajax_querystring', 'sbpp04_hide_from_search', 20, 2 );

/*
 * Reusable function to query members that want to be hidden from directory and search
 */
function sbpp04_get_hidden_members(){
    //Get list of users who want their profile hidden from the directory.
    $args = array(
        'meta_query' => array(
            array(
                'key' => SBPP04_HIDE_DIRECTORY_KEY,
                'value' => 'Yes'
            )
        )
    );
    $exclude_query = new WP_User_Query( $args );

    return $exclude_query;
}

/*
 * End of AJAX functions.
 */