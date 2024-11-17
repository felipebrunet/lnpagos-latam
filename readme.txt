=== LNPagos Latam ===
Contributors: sandbeach123
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.8.1
License: GPL v3
License URI: https://raw.githubusercontent.com/felipebrunet/lnpagos-latam/master/LICENSE
Tags: bitcoin, lightning, buda, lightning network, accept bitcoin, accept lightning, instant bitcoin, bitcoin processor, bitcoin gateway, payment gateway, payment module, bitcoin module, bitcoin woocommerce, btc

This plugin is a fork of the Phaedros's plugin for LNBits payments. All credit goes to him for this awesome project!

Accept Bitcoin on your WooCommerce store, instantly over Lightning, and without extra fees.

== Description ==

Accept Bitcoin on your WooCommerce store, instantly over Lightning, and without extra fees.

== Issues and Development ==

This plugin does not support woocommerce blocks. Use legacy shortcodes.
[URL tutorial](https://themenectar.com/docs/salient/using-legacy-woocommerce-cart-checkout/)

Step by step to go back to legacy shortcodes:
    1. Edit your checkout page created by WooCommerce
    2. Delete the Checkout block by using the element navigator
    3. Add a shortcode block to the page
    4. Add [woocommerce_checkout] as your shortcode
    5. Save the page

If you find a bug, or have an idea for improvement, please [file an issue](https://github.com/felipebrunet/btc_buda_woocommerce/issues/new) or send a pull request.

== External services used by this plugin ==

== Changelog ==

= v1.8.1 =
* New QR url generator (google failed to us)
* Width adjustment for Fixedfloat iframe (onchain payment swap to lightning)

= v1.8.0 =
* Minor Fixes before publishing. 

= v1.7.0 =
* New options added for onchain payments

= v1.0.0 =
* Initial release

= Google Charts =
This plugin uses Google Charts API for generating the QR code.
https://developers.google.com/chart

== Donate link to Phaedros's original project ==

If you find this plugin useful and would like to donate few sats to support the development, [send some using LNBits](https://legend.lnbits.com/paywall/YHNaeBc4nG2U4u6zyoHmjv)!
