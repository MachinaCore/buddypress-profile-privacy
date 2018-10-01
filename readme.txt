=== Simple BuddyPress Profile Privacy ===
Contributors: fencer04
Tags: buddypress, privacy, profile privacy
Requires at least: 4.0
Tested up to: 4.9.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

Allow your members to select additional privacy settings for who can view their profile and it's visibility on the directory page.

== Description ==

Allow your members to select additional privacy settings for who can view their profile and it's visibility on the directory page.

1. Allow users to hide their profile from the directory page. There is an admin setting to allow or deny this setting site wide.
2. Allow each Buddypress member to decide which members can see any of their profile tabs.

The options for profile viewing privacy are:

1. "Only logged in users": any visitor that isn't logged in will be redirected to the registration page.
2. "Only friends": a visitor who is not a friend views a profile they will see a new tab named private giving them an opporunity to become friends with the member.
3. "Everyone": Buddypress functions as if the plugin is not installed.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/buddy-press-profile-privacy` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Are the profiles hidden from site administrators? =

No site administrators or super admins can view all profiles regardless of their settings.

= Does this plugin affect hidden profile fields? =

No, any profile fields that are hidden will be hidden no matter what the settings are. This plugin is for overall profile privacy.

== Screenshots ==

Message shown when user only wants friends to see their profile.
Profile setting for who can see your profile.

== Changelog ==

= 0.7.8 =
1. Updated to fix a bug where users could view other users privacy settings. Users couldn't save the settings, only view. (Props to @jomisica for the patch and @harry74 for the push to get out an update)

= 0.7.7 =
1. Setup the plugin to be translated.
2. Plugin translated into Spanish (Spain) and Lao - Props to margarit0 and topsan123

= 0.7.6 =
1. Replace hardcoded default register URL for wp_register_url(). (Props to @crazycoolcam)

= 0.7.5 =
1. Fix bug that caused 404 page when limiting profile views to friends only.

= 0.7.4 =
1. Check to make sure it's working with Buddypress 2.7.4
2. Update to stop possible conflict with plugins or themes using a CSS class .hidden.

= 0.7.3 =
1. Resolve bug where search and sort on directory page would display users that should be hidden.

= 0.7.2 =
1. Resolve bug where user couldn't save admin setting that allows users to hide their profiles from directory. Props to chanew!

= 0.7.1 =
1. Add admin notification for new hide from directory admin option.

= 0.7 =
1. Added setting for members to hide their profile from directory.
2. Added admin setting to disable the new setting for members to hide from directory.

= 0.6 =
1. Only offer friends only option if Friends Component is active.
2. Fix issues related to Friends Component being made inactive after member selects Friend's only option.

= 0.5 =
1. This is the initial version that was uploaded.