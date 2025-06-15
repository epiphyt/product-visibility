<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility;

use epiphyt\Product_Visibility\handler\Post;
use epiphyt\Product_Visibility\handler\Product;
use epiphyt\Product_Visibility\handler\Role;
use epiphyt\Product_Visibility\handler\User;

/**
 * The main plugin class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Product_Visibility
 */
final class Plugin {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'register_post_meta' ] );
		
		Admin::init();
		Post::init();
		Product::init();
	}
	
	/**
	 * Register post meta fields.
	 */
	public static function register_post_meta(): void {
		\register_post_meta(
			'product',
			'product_visibility_roles',
			[
				'default' => [],
				'description' => \__( 'Roles that can access the product.', 'product-visibility' ),
				'sanitize_callback' => [ Role::class, 'sanitize' ],
				'show_in_rest' => [
					'schema' => [
						'items' => [
							'type' => 'string',
						],
						'type' => 'array',
					],
				],
				'single' => false,
				'type' => 'array',
			]
		);
		\register_post_meta(
			'product',
			'product_visibility_users',
			[
				'default' => [],
				'description' => \__( 'Users that can access the product.', 'product-visibility' ),
				'sanitize_callback' => [ User::class, 'sanitize' ],
				'show_in_rest' => [
					'schema' => [
						'items' => [
							'type' => 'string',
						],
						'type' => 'array',
					],
				],
				'single' => false,
				'type' => 'array',
			]
		);
	}
}
