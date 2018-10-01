<?php
/*
 * Setup admin notices for when updates are run. Used to announce new functions.
 */
function sbpp04_update_notice__success() {
    $notice_dismissed = get_user_option( SBPP04_ADMIN_NOTIFICATION_HIDDEN_KEY );
    if( $notice_dismissed != 1 ){
        $class = "notice notice-success is-dismissible sbpp04-notice";
        $message = __( "A new admin setting has been added to Simple BuddyPress Profile Privacy. You can now allow members to hide themselves from the directory page. ", 'simple-buddypress-profile-privacy' );
        $link_message = __( "Click here to update the profile settings", 'simple-buddypress-profile-privacy' );
        printf( '<div class="%1s"><p>%2s<a href="' . get_site_url() . '/wp-admin/admin.php?page=bp-settings">%3s</a></p></div>', $class, $message, $link_message );
    }
}
add_action( 'admin_notices', 'sbpp04_update_notice__success' );

/*
 * Dismiss notice only once through AJAX
 */
function sbpp04_dismiss_update_notice() {
    update_user_option( get_current_user_id(), SBPP04_ADMIN_NOTIFICATION_HIDDEN_KEY, 1 );
}
add_action( 'wp_ajax_sbpp04_dismiss_update_notice', 'sbpp04_dismiss_update_notice' );

/**
 * Setup admin option to allow users to hide from member directory.
 */
function sbpp04_plugin_admin_settings() {

    add_settings_field(
        SBPP04_ADMIN_HIDE_DIRECTORY_KEY,
        __( 'Members Hide from Directory', SBPP04_PLUGIN_DOMAIN ),
        'sbpp04_hide_directory_settings',
        'buddypress',
        'bp_xprofile'
    );

    /* This is where you add your setting to BuddyPress ones */
    register_setting(
        'buddypress',
        'sbpp04-hide-directory',
        'sbpp04_hide_directory_validate'
    );

}

add_action( 'bp_register_admin_settings', 'sbpp04_plugin_admin_settings' );

/**
 * This is the display function for your field
 */
function sbpp04_hide_directory_settings() {
    $sbpp04_hide_directory = bp_get_option( SBPP04_ADMIN_HIDE_DIRECTORY_KEY );
    ?>
    <input id="<?php echo SBPP04_ADMIN_HIDE_DIRECTORY_KEY ?>" name="<?php echo SBPP04_ADMIN_HIDE_DIRECTORY_KEY ?>" type="checkbox" value="1" <?php checked( $sbpp04_hide_directory ); ?> />
    <label for="<?php echo SBPP04_ADMIN_HIDE_DIRECTORY_KEY ?>"><?php _e( 'Allow registered members to hide from the directory', 'simple-buddypress-profile-privacy' ); ?></label>
    <?php
}

/**
 * This is validation function for your field
 */
function sbpp04_hide_directory_validate( $option = 0 ) {
    return intval( $option );
}