<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility\handler;

use WC_Product;

/**
 * Product related functionality.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Product_Visibility
 */
final class Product {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_filter( 'woocommerce_cart_item_is_purchasable', [ self::class, 'set_non_purchasable' ], 10, 4 );
		\add_filter( 'woocommerce_product_query_meta_query', [ self::class, 'filter_product_meta_query' ] );
	}
	
	/**
	 * Filter product meta query to check for visibility.
	 * 
	 * @param	mixed[]	$meta_query Meta query to filter
	 * @return	mixed[] Updated meta query
	 */
	public static function filter_product_meta_query( array $meta_query ): array {
		$roles_meta = [];
		$user = \wp_get_current_user();
		
		foreach ( (array) $user->roles as $role ) {
			$roles_meta[] = [
				'key' => 'product_visibility_roles',
				'value' => $role,
			];
		}
		
		// // phpcs:disable SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed.DisallowedPartiallyKeyed, Universal.Arrays.MixedArrayKeyTypes.ImplicitNumericKey, Universal.Arrays.MixedKeyedUnkeyedArray.Found
		$meta_query[] = [
			'relation' => 'OR',
			[
				'relation' => 'OR',
				$roles_meta,
			],
			[
				'compare' => 'IN',
				'key' => 'product_visibility_users',
				'value' => $user->ID,
			],
			[
				'compare' => 'AND',
				[
					'compare' => 'NOT EXISTS',
					'key' => 'product_visibility_roles',
				],
				[
					'compare' => 'NOT EXISTS',
					'key' => 'product_visibility_users',
				],
			],
		];
		// phpcs:enable SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed.DisallowedPartiallyKeyed, Universal.Arrays.MixedArrayKeyTypes.ImplicitNumericKey, Universal.Arrays.MixedKeyedUnkeyedArray.Found
		
		return $meta_query;
	}
	
	/**
	 * Set a product non-purchasable if the user cannot view it.
	 * 
	 * @param	bool		$is_purchasable Whether the product is purchasable
	 * @param	string		$key Product key
	 * @param	mixed[]		$values Product values
	 * @param	\WC_Product	$product Product object
	 * @return	bool Whether the product is purchasable
	 */
	public static function set_non_purchasable( bool $is_purchasable, string $key, array $values, WC_Product $product ): bool {
		return Role::can_view( $product->get_id() ) || User::can_view( $product->get_id() );
	}
}
