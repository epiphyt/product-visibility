<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility\handler;

use WC_Product;

/**
 * User related functionality.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Product_Visibility
 */
final class User {
	/**
	 * Check whether a user is allowed to view a post.
	 * 
	 * @param	int		$post_id Optional ID of the post to check
	 * @param	int		$user_id Optional ID of the user to check
	 * @return	bool Whether a user can view a post
	 */
	public static function can_view( int $post_id = 0, int $user_id = 0 ): bool {
		$post_id = $post_id ?: \get_the_ID();
		
		if ( $post_id === false ) {
			return false;
		}
		
		$user_id = $user_id ?: \get_current_user_id();
		$meta = self::get_meta( $post_id );
		
		if ( empty( $meta ) ) {
			return true;
		}
		
		return \in_array( (string) $user_id, $meta, true );
	}
	
	/**
	 * Get a list of users.
	 * 
	 * @return	\WP_User[] List of users
	 */
	public static function get_list(): array {
		return \get_users( [
			'fields' => [
				'ID',
				'display_name',
			],
		] );
	}
	
	/**
	 * Get users from post meta.
	 * 
	 * @param	int		$post_id Optional post ID
	 * @return	string[] Users from post meta
	 */
	public static function get_meta( int $post_id = 0 ): array {
		$post_id = $post_id ?: \get_the_ID();
		
		if ( ! $post_id ) {
			return [];
		}
		
		$product = \wc_get_product( $post_id );
		
		if ( ! $product instanceof WC_Product ) {
			return [];
		}
		
		/** @var \WC_Meta_Data[] $meta */
		$meta = $product->get_meta( 'product_visibility_users', false );
		$metadata = [];
		
		foreach ( $meta as $meta_data ) {
			$metadata[] = $meta_data->get_data()['value'];
		}
		
		return $metadata;
	}
	
	/**
	 * Sanitize a user.
	 * 
	 * @param	mixed	$value Current user value
	 * @return	mixed Updated user value
	 */
	public static function sanitize( mixed $value ): mixed {
		$available_users = self::get_list();
		
		if ( ! \in_array( $value, \array_column( $available_users, 'ID' ), true ) ) {
			return null;
		}
		
		return $value;
	}
}
