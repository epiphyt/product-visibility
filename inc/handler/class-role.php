<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility\handler;

use WP_User;

/**
 * Role related functionality.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Product_Visibility
 */
final class Role {
	/**
	 * Check whether a user has an allowed role to view a post.
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
		$user = \get_user_by( 'ID', $user_id );
		
		if ( ! $user instanceof WP_User ) {
			return false;
		}
		
		if ( empty( $meta ) ) {
			return false;
		}
		
		return ! empty( \array_intersect( $meta, (array) $user->roles ) );
	}
	
	/**
	 * Get a list of roles.
	 * 
	 * @return	array<int<0, max>, array{name: string, title: string}> List of roles
	 */
	public static function get_list(): array {
		$roles = [];
		$wp_roles = \wp_roles();
		
		foreach ( $wp_roles->roles as $role => $data ) {
			$roles[] = [
				'name' => (string) $role,
				'title' => (string) $data['name'],
			];
		}
		
		return $roles;
	}
	
	/**
	 * Get roles from post meta.
	 * 
	 * @param	int		$post_id Optional post ID
	 * @return	string[] Roles from post meta
	 */
	public static function get_meta( int $post_id = 0 ): array {
		$post_id = $post_id ?: \get_the_ID();
		
		if ( ! $post_id ) {
			return [];
		}
		
		$meta = \get_post_meta( $post_id, 'product_visibility_roles' );
		
		if ( ! \is_array( $meta ) ) {
			return [];
		}
		
		return \array_filter( $meta );
	}
	
	/**
	 * Sanitize a role.
	 * 
	 * @param	mixed	$value Current role value
	 * @return	mixed Updated role value
	 */
	public static function sanitize( mixed $value ): mixed {
		$available_roles = self::get_list();
		
		if ( ! \in_array( $value, \array_column( $available_roles, 'name' ), true ) ) {
			return null;
		}
		
		return $value;
	}
}
