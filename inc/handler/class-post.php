<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility\handler;

use WC_Product;

/**
 * Post related functionality.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Product_Visibility
 */
final class Post {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'save_post_product', [ self::class, 'save_post_meta' ] );
		\add_action( 'template_redirect', [ self::class, 'maybe_redirect' ] );
	}
	
	/**
	 * Maybe redirect product page to another page.
	 */
	public static function maybe_redirect(): void {
		if ( \get_post_type() !== 'product' ) {
			return;
		}
		
		if ( ! \is_singular() ) {
			return;
		}
		
		if ( Role::can_view() || User::can_view() ) {
			return;
		}
		
		$url = (string) \get_permalink( \wc_get_page_id( 'shop' ) );
		
		/**
		 * Filter the redirect URL.
		 * 
		 * @param	string	$url Redirect URL
		 */
		$url = \apply_filters( 'product_visibility_redirect_url', $url );
		
		if ( empty( $url ) ) {
			$url = \home_url();
		}
		
		\wp_safe_redirect( $url, 307 );
		exit;
	}
	
	/**
	 * Save post metadata.
	 * 
	 * @param	int		$post_id Post ID
	 */
	public static function save_post_meta( int $post_id ): void {
		if ( ! \current_user_can( 'edit_product', $post_id ) ) {
			return;
		}
		
		$data = [
			'roles' => Role::get_meta( $post_id ),
			'users' => User::get_meta( $post_id ),
		];
		$product = \wc_get_product( $post_id );
		
		if ( ! $product instanceof WC_Product ) {
			return;
		}
		
		foreach ( [ 'roles', 'users' ] as $type ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if (
				empty( $_POST[ 'product_visibility_' . $type ] )
				|| ! \is_array( $_POST[ 'product_visibility_' . $type ] )
			) {
				$product->delete_meta_data( 'product_visibility_' . $type );
			}
			else {
				$deletable = \array_diff( $data[ $type ], \wp_unslash( $_POST[ 'product_visibility_' . $type ] ) );
				
				foreach ( $deletable as $meta ) {
					$product->delete_meta_data_value( 'product_visibility_' . $type, $meta );
				}
				
				foreach ( \wp_unslash( $_POST[ 'product_visibility_' . $type ] ) as $meta ) {
					if ( ! \in_array( $meta, $data[ $type ], true ) ) {
						$product->add_meta_data( 'product_visibility_' . $type , $meta );
					}
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			
			$product->save();
		}
	}
}
