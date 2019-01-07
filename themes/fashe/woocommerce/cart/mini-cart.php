<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
<div class="header-cart header-dropdown">
	<ul class="header-cart-wrapitem" >
		<?php

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<li class="header-cart-item">
                        <div  class="header-cart-item-img">
                            <?=$thumbnail; ?>
                        </div>

                        <div class="header-cart-item-txt">
                            <a href="<?= $product_permalink;?>" class="header-cart-item-name">
                                 <?= $product_name;?>
                            </a>

                            <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity',
                                '<span class="header-cart-item-info">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>',
                                $cart_item, $cart_item_key )
                            ; ?>
                        </div>

						<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
					</li>
					<?php
				}
			}

		?>
	</ul>

	<div class="header-cart-total"><?php _e( 'Total', 'woocommerce' ); ?>:<?php echo WC()->cart->get_cart_subtotal(); ?></div>

    <div class="header-cart-buttons">
        <div class="header-cart-wrapbtn">
            <!-- Button -->
            <a href="<?= esc_url( wc_get_cart_url() )?>" class="flex-c-m size1 bg1 bo-rad-20 hov1 s-text1 trans-0-4"><?= esc_html__( 'View cart', 'woocommerce' )?></a>
        </div>

        <div class="header-cart-wrapbtn">
            <!-- Button -->
            <a href="<?=esc_url( wc_get_checkout_url() )?>" class="flex-c-m size1 bg1 bo-rad-20 hov1 s-text1 trans-0-4"><?= esc_html__( 'Checkout', 'woocommerce' )?></a>
        </div>
    </div>

    <?php endif; ?>
<!--End-->
</div>