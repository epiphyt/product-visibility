<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility;

/*
Plugin Name:		Product Visibility
Description:		Set the visibility of products to allow only certain users/roles to access them.
Author:				Epiphyt
Author URI:			https://epiph.yt/en/
Version:			1.0.0-dev
License:			GPL2
License URI:		https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:		product-visibility
Domain Path:		/languages
Requires Plugins:	woocommerce

WC requires at least:	7.0
WC tested up to:		9.9

Product Visibility is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Product Visibility is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Product Visibility. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
\defined( 'ABSPATH' ) || exit;

if ( ! \defined( 'EPI_PRODUCT_VISIBILITY_BASE' ) ) {
	if ( \file_exists( \WP_PLUGIN_DIR . '/product-visibility/' ) ) {
		\define( 'EPI_PRODUCT_VISIBILITY_BASE', \WP_PLUGIN_DIR . '/product-visibility/' );
	}
	else if ( \file_exists( \WPMU_PLUGIN_DIR . '/product-visibility/' ) ) {
		\define( 'EPI_PRODUCT_VISIBILITY_BASE', \WPMU_PLUGIN_DIR . '/product-visibility/' );
	}
	else {
		\define( 'EPI_PRODUCT_VISIBILITY_BASE', \plugin_dir_path( __FILE__ ) );
	}
}

\define( 'EPI_PRODUCT_VISIBILITY_FILE', \EPI_PRODUCT_VISIBILITY_BASE . \basename( __FILE__ ) );
\define( 'EPI_PRODUCT_VISIBILITY_URL', \plugin_dir_url( \EPI_PRODUCT_VISIBILITY_FILE ) );
\define( 'EPI_PRODUCT_VISIBILITY_VERSION', '1.0.0-dev' );

/**
 * Autoload all necessary classes.
 * 
 * @param	string	$class_name The class name of the auto-loaded class
 */
\spl_autoload_register( static function( string $class_name ): void {
	$path = \explode( '\\', $class_name );
	$filename = \str_replace( '_', '-', \strtolower( \array_pop( $path ) ) );
	
	if ( \strpos( $class_name, __NAMESPACE__ ) !== 0 ) {
		return;
	}
	
	$namespace = \strtolower( __NAMESPACE__ . '\\' );
	$class_name = \str_replace(
		[ $namespace, '\\', '_' ],
		[ '', '/', '-' ],
		\strtolower( $class_name )
	);
	$string_position = \strrpos( $class_name, $filename );
	
	if ( $string_position !== false ) {
		$class_name = \substr_replace( $class_name, 'class-' . $filename, $string_position, \strlen( $filename ) );
	}
	
	$maybe_file = __DIR__ . '/inc/' . $class_name . '.php';
	
	if ( \file_exists( $maybe_file ) ) {
		require_once $maybe_file;
	}
} );

\add_action( 'plugins_loaded', [ Plugin::class, 'init' ] );
