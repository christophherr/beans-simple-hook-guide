<?php
/**
 * Beans Simple Hook Guide
 *
 * @package     ChristophHerr\BeansSimpleHookGuide
 * @author      christophherr
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Beans Simple Hook Guide
 * Plugin URI: https://github.com/christophherr/beans-simple-hook-guide
 * Description: Find Beans action hooks easily and select them with a single click at their actual locations in your Beans theme.
 * Version: 1.0.0
 * Author: Christoph Herr
 * Author: Sridhar Katakam
 * Author URI: https://www.christophherr.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: christophherr/beans-simple-hook-guide
 * Text Domain: beans-simple-hook-guide
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

namespace ChristophHerr\BeansSimpleHookGuide;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Nothing to see here.' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_script_and_stylesheet' );
/**
 * Load script and stylesheet.
 */
function enqueue_script_and_stylesheet() {
		wp_enqueue_style( 'bshg-styles', plugins_url( 'style.css', __FILE__ ) );
		wp_enqueue_script( 'bshg-scripts', plugins_url( 'general.js', __FILE__ ) );
}

/**
 * Check if the current page is a login page.
 *
 * @since 1.0.0
 *
 * @return void
 */
function is_login_page() {
	return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true );
}

add_action( 'all', __NAMESPACE__ . '\print_hooks_on_page' );
/**
 * Print the hooks on the page.
 *
 * @since 1.0.0
 *
 * @return void
 */
function print_hooks_on_page() {
	// Don't do anything when in the admin or on a login page.
	if ( is_admin() || is_login_page() ) {
		return;
	}

	global $wp_actions;
	$filter = current_filter();

	if ( 'beans_' === substr( $filter, 0, 6 ) ) {
		if ( 'beans_loaded_api_' === substr( $filter, 0, 17 ) ) {
			echo '';
		} elseif ( isset( $wp_actions[ $filter ] ) ) {
			printf( '<div id="%1$s" class="beans-hook"><input type="text" readonly value="%1$s" /></div>', $filter );
		}
	}
}
