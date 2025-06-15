=== Product Visibility ===
Contributors: epiphyt, kittmedia
Tags: woocommerce, product, visibility, conditional
Requires at least: 6.1
Stable tag: 1.0.0
Tested up to: 6.8
Requires PHP: 7.4
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Set the visibility of products to allow only certain users/roles to access them.

== Description ==

Product Visibility allows you to define roles and users, who are able to view (and purchase) a product. This way you can create products just for a set of users.

The plugin checks both the product list as well as the cart and doesn’t display the product in any product list if a user is not allowed to view it and also makes it impossible for the user to add it to their cart (while technically, the product is added to the cart and removed directly after it). If a user opens the link to a product, which is not viewable by this user, the user will be redirected to the shop page.

**Note: This plugins requires the plugin [WooCommerce](https://wordpress.org/plugins/woocommerce/).**


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/product-visibility` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.


== Frequently Asked Questions ==

= Can I change the redirect URL? =

There is no option to change the redirect URL. You can however use the filter `product_visibility_redirect_url` to set a custom redirect URL programmatically.

= Who are you, folks? =

We are [Epiphyt](https://epiph.yt/), your friendly neighborhood WordPress plugin shop from southern Germany.


== Changelog ==

= 1.0.0 =
* Initial release


== Upgrade Notice ==


== Screenshots ==
