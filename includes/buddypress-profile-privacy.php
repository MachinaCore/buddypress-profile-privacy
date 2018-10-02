<?php

/**
 * Adds a blank hidden tab for when the content is hidden by privacy settings.
 */
class SBPP04_Profile_Privacy extends BP_Component {
    /**
     * Initial component setup.
     */
    public function __construct() {
        parent::start(
        // Unique component ID
            'bpp',

            // Used by BP when listing components (eg in the Dashboard)
            __( 'BP Profile Privacy', 'simple-buddypress-profile-privacy' )
        );
    }

    /**
     * Set up component data, as required by BP.
     */
    public function setup_globals( $args = array() ) {
        parent::setup_globals( array(
            'slug'          => 'profile-privacy',
            'has_directory' => false,
        ) );
    }

    /**
     * Set up component navigation, and register display callbacks.
     */
    public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
        $main_nav = array(
            'name'                => __( 'Private', 'simple-buddypress-profile-privacy' ),
            'slug'                => $this->slug,
            'screen_function' => array( $this, 'screen_function_main' ),
            'default_subnav_slug' => 'sbpp-hidden',
            'position'            => 10000,
        );

        $sub_nav[] = array(
            'name'            => __( 'Friends Only', 'simple-buddypress-profile-privacy' ),
            'slug'            => 'sbpp-hidden',
            'parent_slug'     => 'profile-privacy',
            'parent_url'      => bp_displayed_user_domain() . 'profile-privacy/',
            'screen_function' => array( $this, 'screen_function_main' ),
        );

        parent::setup_nav( $main_nav, $sub_nav );
    }

    /**
     * Set up display screen logic for friend's only message.
     */
    public function screen_function_main() {
        add_action( 'bp_template_content', array( $this, 'main_content' ) );
        bp_core_load_template( 'members/single/plugins' );
    }

    /**
     * Markup for the only content area that will display hidden message.
     */
    public function main_content()
    {
        echo "<p>" . bp_core_get_user_displayname( bp_displayed_user_id() ) . " has chosen to limit profile access to friends only.";
		if( is_user_logged_in() ) {
			printf( __( " Use the button below to send a friend request to %s", 'simple-buddypress-profile-privacy' ), bp_core_get_user_displayname( bp_displayed_user_id() ) );
			echo bp_add_friend_button();
		}
		echo "</p>";
    }
}

/**
 * Bootstrap the component.
 */
function sbpp04_init() {
    buddypress()->bpp = new SBPP04_Profile_Privacy();
}
add_action( 'bp_loaded', 'sbpp04_init' );

//Add privacy settings screen to profile administration
function sbpp04_profile_settings_nav() {
	global $bp;

	bp_core_new_subnav_item( array(
		'name' => __( 'Privacy Settings', 'simple-buddypress-profile-privacy' ),
		'slug' => 'privacy-settings',
		'position' => 30,
		'screen_function' => 'sbpp04_privacy_screen',
		'show_for_displayed_user' => true,
		'parent_url'          => trailingslashit( $bp->loggedin_user->domain . $bp->slug . "settings" ),
		'parent_slug'         => 'settings',
		'user_has_access' => bp_core_can_edit_settings()
	) );

}
add_action( 'bp_setup_nav', 'sbpp04_profile_settings_nav', 99 );

//Load page with privacy options
function sbpp04_privacy_screen() {
	add_action( 'bp_template_title', 'sbpp04_privacy_screen_title' );
	add_action( 'bp_template_content', 'sbpp04_privacy_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function sbpp04_privacy_screen_title() {
	_e( 'Privacy Settings', 'simple-buddypress-profile-privacy' );
	echo '<br/>';
}

function sbpp04_privacy_screen_content() {
    //Make sure that the input is from the settings dropdown.
    $valid_view_array = array( SBPP04_VIEW_LOGGED_IN, SBPP04_VIEW_FRIENDS, SBPP04_VIEW_EVERYONE );
	$valid_hide_dir_array = array ( "Yes", "No" );

	$curr_user = get_current_user_id();
	$curr_privacy = get_user_meta( $curr_user, SBPP04_PRIVACY_SETTING_KEY, true );
	$hide_dir = get_user_meta( $curr_user, SBPP04_HIDE_DIRECTORY_KEY, true );
	if( isset( $_POST['can_view_profile'] ) ) {
		check_admin_referer( 'sbpp04_save_settings', 'sbpp04_nonce' );
		$sanitize_view_profile = sanitize_text_field( $_POST['can_view_profile'] );
		if( !empty( $curr_privacy ) && in_array( $sanitize_view_profile, $valid_view_array ) ){
			update_user_meta( $curr_user, SBPP04_PRIVACY_SETTING_KEY, $sanitize_view_profile, $curr_privacy );
		}else {
			add_user_meta( $curr_user, SBPP04_PRIVACY_SETTING_KEY, $sanitize_view_profile );
		}
		$curr_privacy = $_POST['can_view_profile'];

		$sanitize_hide_directory = sanitize_text_field( $_POST['hide_directory'] );
		if( !empty( $hide_dir ) && in_array( $sanitize_hide_directory, $valid_hide_dir_array ) ){
			update_user_meta( $curr_user, SBPP04_HIDE_DIRECTORY_KEY, $sanitize_hide_directory, $hide_dir );
		}else{
			add_user_meta( $curr_user, SBPP04_HIDE_DIRECTORY_KEY, $sanitize_hide_directory );
		}
		$hide_dir = $_POST['hide_directory'];
	}
	?>
		<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
			<p><label for="can_view_proile"><?php _e ('Who can view your profile?', 'simple-buddypress-profile-privacy'); ?></label>
			<select name="can_view_profile">
				<option value="<?php echo esc_attr( SBPP04_VIEW_EVERYONE ); ?>"<?php if( $curr_privacy == SBPP04_VIEW_EVERYONE ) echo ' selected="selected"' ?>><?php _e ('Everyone', 'simple-buddypress-profile-privacy'); ?></option>
				<option value="<?php echo esc_attr( SBPP04_VIEW_LOGGED_IN ); ?>"<?php if( $curr_privacy == SBPP04_VIEW_LOGGED_IN ) echo ' selected="selected"' ?>><?php _e ('Only Logged In Users', 'simple-buddypress-profile-privacy'); ?></option>
                <?php if( SBPP04_FRIENDS_ACTIVE ): ?>
				    <option value="<?php echo esc_attr( SBPP04_VIEW_FRIENDS ); ?>"<?php if( $curr_privacy == SBPP04_VIEW_FRIENDS ) echo ' selected="selected"' ?>><?php _e ('Only Friends', 'simple-buddypress-profile-privacy'); ?></option>
                <?php endif; ?>
			</select></p>
			<?php if( bp_get_option( SBPP04_ADMIN_HIDE_DIRECTORY_KEY ) == 1 ) : ?>
			<label for="hide_directory"><?php _e ( 'Hide My Profile from Member Directory', 'simple-buddypress-profile-privacy' ); ?></label>
			<p><select name="hide_directory">
				<option value="Yes"<?php if( $hide_dir == "Yes" ) echo ' selected="selected"' ?>><?php _e ( 'Yes', 'simple-buddypress-profile-privacy' ); ?></option>
				<option value="No"<?php if( $hide_dir == "No" ) echo ' selected="selected"' ?>><?php _e ( 'No', 'simple-buddypress-profile-privacy' ); ?></option>
			</select></p>
			<?php endif; ?>
			<input type="submit" value="<?php _e( 'Save', 'simple-buddypress-profile-privacy' ); ?>" />
			<?php wp_nonce_field( 'sbpp04_save_settings', 'sbpp04_nonce' ); ?>
		</form>
	<?php
}

//Check privacy settings before loading profile
function sbpp04_privacy_check(){
	//If site admin then the profile will display no matter what the settings are.
	if( !is_super_admin() ){
        $curr_privacy = get_user_meta( bp_displayed_user_id(), SBPP04_PRIVACY_SETTING_KEY, true);
        //If not manually set use SBPP04_VIEW_FRIENDS as default
        if ($curr_privacy == NULL){
            $curr_privacy = bp_get_option(SBPP04_ADMIN_HIDE_PROFILE_DEFAULT_KEY);
        }
		switch( $curr_privacy ) {
			//Only show profile if user is friends with profile being displayed and the privacy template isn't already showing.
			case SBPP04_VIEW_FRIENDS:
			    //Check to see if friends component is active. If admin turns it off after member sets to friend only it will be blocked no matter what.
                if( SBPP04_FRIENDS_ACTIVE && is_user_logged_in() ){
                    $is_friend = bp_is_friend( bp_displayed_user_id() );
                    if( $is_friend != 'is_friend' && !bp_is_my_profile() && !bp_is_current_component( 'bpp' ) ) {
                        sbpp04_privacy_forbidden_redirect("Only%20Friends%20can%20view%20this&20Profile");
                    }else{
                        sbpp04_privacy_redirect( $is_friend );
                    }
                }else{
                    sbpp04_privacy_forbidden_redirect("This%20Profile%20is%20private");
                }
				break;
			//Only show profile if the user is logged in. Otherwise redirect to register page.
			case SBPP04_VIEW_LOGGED_IN:
				if( !is_user_logged_in() && bp_is_user() ) {
                    sbpp04_privacy_forbidden_redirect("This%20Profile%20is%20private");
                    exit();
				}else{
					sbpp04_privacy_redirect();
                }
                break;
            default:
                sbpp04_privacy_redirect();
                break;
		}
	}else{
		sbpp04_privacy_redirect();
    }
}
add_action( 'template_redirect', 'sbpp04_privacy_check' );

/*
 * Update member count to ensure that those hidden from directory are not listed.
 */
function sbpp04_update_member_count( $member_count ){
    if( bp_get_option( SBPP04_ADMIN_HIDE_DIRECTORY_KEY ) ){
        $exclude_query = sbpp04_get_hidden_members();

        return $member_count - $exclude_query->get_total();
    }
}
add_filter( 'bp_get_total_member_count', 'sbpp04_update_member_count' );

//Setup reusable code for when users don't need to see privacy page.
function sbpp04_privacy_redirect( $is_friend = '' ){
    if ( !bp_is_current_component( 'bpp' ) ) {
        bp_core_remove_nav_item( 'profile-privacy' );
    }elseif ( $is_friend == 'is_friend' || !SBPP04_FRIENDS_ACTIVE ) {
        bp_core_remove_nav_item( 'profile-privacy' );
        wp_redirect( bp_displayed_user_domain() );
        exit();
    }
}

//Redirect to to current page with error - Not elegant but working
function sbpp04_privacy_forbidden_redirect( $msg = '' ){
    if ( !bp_is_current_component( 'bpp' ) ) {
        bp_core_remove_nav_item( 'profile-privacy' );
        if (bp_displayed_user_id()!=0 ){
            wp_redirect("/members/?msg=".$msg ); 
            exit;
        }
    } else {
        exit;
    }
}