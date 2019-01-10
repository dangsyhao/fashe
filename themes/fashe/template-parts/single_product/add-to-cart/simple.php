
<?php

global $product;

if ( ! $product->is_purchasable() ) {
    return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) : ?>
    <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

    <form  action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>

        <div class="flex-r-m flex-w p-t-10">
            <div class="w-size16 flex-m flex-w">
                <?php
                do_action( 'woocommerce_before_add_to_cart_quantity' );

                woocommerce_quantity_input( array(
                    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                    'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                ) );

                do_action( 'woocommerce_after_add_to_cart_quantity' );

                ?>

                <div class="btn-addcart-product-detail size9 trans-0-4 m-t-10 m-b-10">
                    <!-- Button -->
                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="flex-c-m sizefull bg1 bo-rad-23 hov1 s-text1 trans-0-4">
                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                    </button>
                </div>
                <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

            </div>
        </div>
    </form>
    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
