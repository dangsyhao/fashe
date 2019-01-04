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