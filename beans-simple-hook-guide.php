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

register_activation_hook( __FILE__, __NAMESPACE__ . '\maybe_activate_plugin' );
/**
 * This function runs on plugin activation. It checks to make sure the
 * Beans Framework is active. If not, it deactivates the plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function maybe_activate_plugin() {
	if ( ! function_exists( '\beans_define_constants' ) ) {
		// Deactivate.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice_message' );
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\maybe_deactivate_plugin' );
add_action( 'switch_theme', __NAMESPACE__ . '\maybe_deactivate_plugin' );
/**
 * This function is triggered when the WordPress theme is changed.
 * It checks if the Beans Framework is active. If not, it deactivates the plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function maybe_deactivate_plugin() {
	if ( ! function_exists( '\beans_define_constants' ) ) {
		// Deactivate.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice_message' );
	}
}
/**
 * Error message if you're not using the Beans Framework.
 *
 * @since 1.0.0
 *
 * @return void
 */
function admin_notice_message() {
	// phpcs:disable WordPress.XSS.EscapeOutput -- Need to build the link.
	$error = sprintf(
		// translators: Link to the Beans website.
		__( 'Sorry, you can\'t use the Beans Simple Hook Guide Plugin unless the <a href="%s">Beans Framework</a> is active. The plugin has been deactivated.', 'beans-header-footer-fields' ),
		'https://getbeans.io'
	);
	printf( '<div class="error"><p>%s</p></div>', $error );
	// phpcs:enable
	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification -- Internal usage
		unset( $_GET['activate'] );
	}
}

add_action( 'admin_bar_menu', __NAMESPACE__ . '\display_admin_bar_links', 100 );
/**
 * Add admin bar links.
 *
 * @since 1.0.0
 *
 * @return void
 */
function display_admin_bar_links() {
	global $wp_admin_bar;

	if ( is_admin() ) {
		return;
	}

	$wp_admin_bar->add_menu(
		array(
			'id'       => 'bshg',
			'title'    => __( 'Beans Hooks', 'beans-simple-hook-guide' ),
			'href'     => '',
			'position' => 0,
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'id'       => 'bshg_action',
			'parent'   => 'bshg',
			'title'    => __( 'Action Hooks', 'beans-simple-hook-guide' ),
			'href'     => esc_url( add_query_arg( 'bshg_hooks', 'show' ) ),
			'position' => 10,
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'id'       => 'bshg_clear',
			'parent'   => 'bshg',
			'title'    => __( 'Clear', 'beans-simple-hook-guide' ),
			'href'     => esc_url( remove_query_arg( [ 'bshg_hooks' ] ) ),
			'position' => 10,
		)
	);

}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_script_and_stylesheet' );
/**
 * Load script and stylesheet.
 */
function enqueue_script_and_stylesheet() {
	if ( 'show' === filter_input( INPUT_GET, 'bshg_hooks', FILTER_SANITIZE_STRING ) ) {
		wp_enqueue_style( 'bshg-styles', plugins_url( 'style.css', __FILE__ ) );
		wp_enqueue_script( 'bshg-scripts', plugins_url( 'general.js', __FILE__ ) );
	}
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
	// Don't do anything when in the admin or if nothing is displayed.
	if ( is_admin() || ! ( 'show' === filter_input( INPUT_GET, 'bshg_hooks', FILTER_SANITIZE_STRING ) ) ) {
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
