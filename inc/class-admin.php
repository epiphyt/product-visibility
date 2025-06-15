<?php
declare(strict_types = 1);

namespace epiphyt\Product_Visibility;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use epiphyt\Product_Visibility\handler\Role;
use epiphyt\Product_Visibility\handler\User;
use WP_Screen;

/**
 * Admin related functionality.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Product_Visibility
 */
final class Admin {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'add_meta_boxes', [ self::class, 'register_meta_boxes' ] );
		\add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue_assets' ] );
	}
	
	/**
	 * Enqueue assets.
	 */
	public static function enqueue_assets(): void {
		$screen = \get_current_screen();
		
		if ( ! $screen instanceof WP_Screen ) {
			return;
		}
		
		if ( $screen->base !== 'post' || $screen->id !== 'product' ) {
			return;
		}
		
		$is_debug = \defined( 'WP_DEBUG' ) && \WP_DEBUG || \defined( 'SCRIPT_DEBUG' ) && \SCRIPT_DEBUG;
		$suffix = $is_debug ? '' : '.min';
		$scripts = [
			'admin-meta-box-tabs' => [
				'dependencies' => [],
				'path' => 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'admin-meta-box-tabs' . $suffix . '.js',
			],
		];
		$styles = [
			'admin' => 'assets/style/build/admin' . $suffix . '.css',
		];
		
		foreach ( $scripts as $handle => $data ) {
			\wp_enqueue_script( 'product-visibility-' . $handle, \EPI_PRODUCT_VISIBILITY_URL . $data['path'], $data['dependencies'], (string) \filemtime( \EPI_PRODUCT_VISIBILITY_BASE . $data['path'] ) ); // @phpstan-ignore constant.notFound
		}
		
		foreach ( $styles as $handle => $path ) {
			\wp_enqueue_style( 'product-visibility' . $handle, \EPI_PRODUCT_VISIBILITY_URL . $path, [], (string) \filemtime( \EPI_PRODUCT_VISIBILITY_BASE . $path ) ); // @phpstan-ignore constant.notFound
		}
	}
	
	/**
	 * Display the meta box content.
	 */
	public static function get_meta_box_content(): void {
		$roles = Role::get_meta();
		$users = User::get_meta();
		?>
		<div id="product-visibility__meta" class="product-visibility__meta categorydiv">
			<ul class="category-tabs">
				<li class="tabs"><a href="#product-visibility__roles" class="tab"><?php \esc_html_e( 'Roles', 'product-visibility' ); ?></a></li>
				<li><a href="#product-visibility__users" class="tab"><?php \esc_html_e( 'Users', 'product-visibility' ); ?></a></li>
			</ul>
			
			<div id="product-visibility__roles" class="tabs-panel">
				<ul class="product-visibility__role-list">
					<?php foreach ( Role::get_list() as $role ) : ?>
					<li class="product-visibility__role-item">
						<label class="selectit">
							<input value="<?php echo \esc_attr( $role['name'] ); ?>" type="checkbox" name="product_visibility_roles[]" id="product-visibility__user-<?php echo \esc_attr( $role['name'] ); ?>"<?php \checked( \in_array( $role['name'], $roles, true ) ); ?>>
							<?php echo \esc_html( $role['title'] ); ?>
						</label>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			
			<div id="product-visibility__users" class="tabs-panel" style="display: none;">
				<ul class="product-visibility__user-list">
					<?php foreach ( User::get_list() as $user ) : ?>
					<li class="product-visibility__user-item">
						<label class="selectit">
							<input value="<?php echo \esc_attr( (string) $user->ID ); ?>" type="checkbox" name="product_visibility_users[]" id="product-visibility__user-<?php echo \esc_attr( (string) $user->ID ); ?>"<?php \checked( \in_array( (string) $user->ID, $users, true ) ); ?>>
							<?php echo \esc_html( $user->display_name ); ?>
						</label>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		
		<p class="howto"><?php \esc_html_e( 'If you select users or roles here, only these users or users with one of these roles can access this product.', 'product-visibility' ); ?></p>
		<?php
	}
	
	/**
	 * Register meta boxes.
	 */
	public static function register_meta_boxes(): void {
		$screen = 'product';
		
		if (
			\class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' )
			&& \wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() // @phpstan-ignore method.nonObject
		) {
			$screen = \wc_get_page_screen_id( 'product' );
		}
		
		\add_meta_box(
			'product-visibility',
			\__( 'Product Visibility', 'product-visibility' ),
			[ self::class, 'get_meta_box_content' ],
			$screen,
			'side'
		);
	}
}
