<?php

//
//remove_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail',10);
//
add_action('woocommerce_before_shop_loop_item_title','fashe_template_loop_product_thumbnail',10);
//
add_action('fashe_product_loop_add_to_cart','woocommerce_template_loop_add_to_cart',10);

//
add_filter('feshe_recent_product_loop_home','feshe_recent_product_loop_home',10);
//
add_filter('fashe_product_categories_loop_home','fashe_product_categories_loop_home',10);
//
add_action('fashe_woocommerce_subcategory_thumbnail','fashe_woocommerce_subcategory_thumbnail',10);
//
add_shortcode('fashe_woocommerce_short_code_shop','fashe_woocommerce_short_code_shop');


/**
* Product Add to cart.
 *
 * @see woocommerce_template_single_add_to_cart()
* @see woocommerce_simple_add_to_cart()
* @see woocommerce_grouped_add_to_cart()
* @see woocommerce_variable_add_to_cart()
* @see woocommerce_external_add_to_cart()
* @see woocommerce_single_variation()
* @see woocommerce_single_variation_add_to_cart_button()
*/

add_action( 'fashe_single_product_summary', 'fashe_template_single_add_to_cart', 30 );
add_action( 'fashe_simple_add_to_cart', 'fashe_simple_add_to_cart', 30 );
add_action( 'fashe_grouped_add_to_cart', 'fashe_grouped_add_to_cart', 30 );
add_action( 'fashe_variable_add_to_cart', 'fashe_variable_add_to_cart', 30 );
add_action( 'fashe_external_add_to_cart', 'fashe_external_add_to_cart', 30 );
add_action( 'fashe_single_variation', 'fashe_single_variation', 10 );
add_action( 'fashe_single_variation', 'fashe_single_variation_add_to_cart_button', 20 );