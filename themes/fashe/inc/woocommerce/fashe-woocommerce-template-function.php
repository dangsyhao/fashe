<?php

/*
 *
 */
function feshe_recent_product_loop_home( $atts ){
    $atts = array_merge( array(
        'limit'        => 12,
        'columns'      => 4,
        'orderby'      => 'date',
        'order'        => 'DESC',
        'category'     => '',
        'cat_operator' => 'IN',
    ), (array) $atts );

    $shortcode = new fashe_product_shortcode_class( $atts, 'recent_products' );
    return $shortcode->fashe_get_content();
}

/*
 *
 */

function fashe_product_categories_loop_home( $atts ) {
    if ( isset( $atts['number'] ) ) {
        $atts['limit'] = $atts['number'];
    }

    $atts = shortcode_atts( array(
        'limit'      => '-1',
        'orderby'    => 'term_id',
        'order'      => 'DESC',
        'columns'    => '4',
        'hide_empty' => 1,
        'parent'     => '',
        'ids'        => '',
    ), $atts, 'product_categories' );

    $ids        = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
    $hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

    // Get terms and workaround WP bug with parents/pad counts.
    $args = array(
        'orderby'    => $atts['orderby'],
        'order'      => $atts['order'],
        'hide_empty' => $hide_empty,
        'include'    => $ids,
        'pad_counts' => true,
        'child_of'   => $atts['parent'],
    );

    $product_categories = get_terms( 'product_cat', $args );

    if ( '' !== $atts['parent'] ) {
        $product_categories = wp_list_filter( $product_categories, array(
            'parent' => $atts['parent'],
        ) );
    }

    if ( $hide_empty ) {
        foreach ( $product_categories as $key => $category ) {
            if ( 0 === $category->count ) {
                unset( $product_categories[ $key ] );
            }
        }
    }

    $atts['limit'] = '-1' === $atts['limit'] ? null : intval( $atts['limit'] );
    if ( $atts['limit'] ) {
        $product_categories = array_slice( $product_categories, 0, $atts['limit'] );
    }

    $columns = absint( $atts['columns'] );

    wc_set_loop_prop( 'columns', $columns );
    wc_set_loop_prop( 'is_shortcode', true );

    ob_start();

    if ( $product_categories ) {
        woocommerce_product_loop_start();


        wc_get_template( 'content-product_cat.php', array(
            'category' => $product_categories,
        ) );

        woocommerce_product_loop_end();
    }

    woocommerce_reset_loop();

    return ob_get_clean();
}

/**
 * Show subcategory thumbnails.
 *
 * @param mixed $category Category.
 */

function fashe_woocommerce_subcategory_thumbnail( $category ) {

    $term_id=$category->term_id;

    $thumbnail=get_field('category_display_thumbnail','product_cat_'.$term_id);

    if($thumbnail=='short_thumbnail')
    {
        $thumbnail_display='fashe-loop-category-short-thumbnail';
    }else{

        $thumbnail_display='fashe-loop-category-long-thumbnail';
    }

    $small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size',$thumbnail_display);
    $thumbnail_id         = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );

    if ( $thumbnail_id ) {
        $image        = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
        $image        = $image[0];
    } else {
        $image        = wc_placeholder_img_src();
    }

    if ( $image ) $image = str_replace( ' ', '%20', $image );

    echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ). '" />';

}

/**
 * Show Short Code Product.
 *
 * @param mixed $category Category.
 */

function fashe_woocommerce_short_code_shop($atts) {


    $atts = shortcode_atts( array(
        'per_page'     => 6,
        'paginate'      =>true,
    ), $atts);

    $shortcode = new fashe_product_shortcode_class( $atts);

    return $shortcode->fashe_get_content();
}


function fashe_woocommerce_pagination(){


    $total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
    $current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
    $base    = esc_url_raw( add_query_arg( 'product-page', '%#%', false ) );
    $format  = '?product-page=%#%';

    if ( $total <= 1 ) {
        return;
    }
    ?>
    <?php
    paginate_links( apply_filters( 'woocommerce_pagination_args', array( // WPCS: XSS ok.
        'base'         => $base,
        'format'       => $format,
        'add_args'     => false,
        'current'      => $current,
        'total'        => $total,
    ) ) );
    ?>

    <?php ob_start();?>
    <div class="pagination flex-m flex-w p-t-26">
        <?php for($i=1;$i<=$total;$i++):?>
            <a href="<?= get_page_link().'?product-page='.$i;?>" class="item-pagination flex-c-m trans-0-4 <?= ($current==$i)?'active-pagination':'';?>"><?= $i;?></a>
        <?php endfor;?>
    </div>

    <?php
    $result=ob_get_contents();
    ob_end_clean();

    echo $result;
}

/*
 * Fashe Sort Product
 */

function fashe_woocommerce_orderby(){

    $show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
        'menu_order' => __( 'Default sorting', 'woocommerce' ),
        'popularity' => __( 'Sort by popularity', 'woocommerce' ),
        'rating'     => __( 'Sort by average rating', 'woocommerce' ),
        'date'       => __( 'Sort by latest', 'woocommerce' ),
        'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
        'price-desc' => __( 'Sort by price: high to low', 'woocommerce' ),
    ) );

    $default_orderby = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', '' ) );
    $orderby         = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

    if ( wc_get_loop_prop( 'is_search' ) ) {
        $catalog_orderby_options = array_merge( array( 'relevance' => __( 'Relevance', 'woocommerce' ) ), $catalog_orderby_options );

        unset( $catalog_orderby_options['menu_order'] );
    }

    if ( ! $show_default_orderby ) {
        unset( $catalog_orderby_options['menu_order'] );
    }

    if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
        unset( $catalog_orderby_options['rating'] );
    }

    if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
        $orderby = current( array_keys( $catalog_orderby_options ) );
    }

    //--Filter Order--//

    wc_get_template( 'woocommerce/loop/orderby.php', array(
        'catalog_orderby_options' => $catalog_orderby_options,
        'orderby'                 => $orderby,
        'show_default_orderby'    => $show_default_orderby,
        'total'    => wc_get_loop_prop( 'total' ),
        'per_page' => wc_get_loop_prop( 'per_page' ),
        'current'  => wc_get_loop_prop( 'current_page' ),
    ) );

    ?>

    <?php

}

/*
 *
 */


function fashe_add_to_cart_single_product()
{
    wc_get_template('template-parts/single_product/add-to-cart/simple.php');
}

add_action('fashe_add_to_cart_single_product','fashe_add_to_cart_single_product');

/*
*
*/


function fashe_meta_single_product()
{
    wc_get_template('template-parts/single_product/_meta.php');
}

add_action('fashe_meta_single_product','fashe_meta_single_product');

/*
*
*/


function fashe_display_product_attributes( $product ) {
    wc_get_template( 'template-parts/single_product/_product-attributes.php', array(
        'product'            => $product,
        'attributes'         => array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' ),
        'display_dimensions' => apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ),
    ) );
}

add_action('fashe_display_product_attributes','fashe_display_product_attributes');

/*
 *
 */


function fashe_single_product_left_section()
{
    global $product;
    $attachment_ids = $product->get_gallery_image_ids();
    $attachment_ids[] = $product->get_image_id();

    if(!empty($attachment_ids)){

        wc_get_template('template-parts/single_product/single_product_left.php', array('attachment_ids' => $attachment_ids));

    }
}

add_action('fashe_single_product_left_section','fashe_single_product_left_section');

/*
 *
 */

function fashe_single_product_right_section()
{
    global $product;
    $result=array(
        'price'=>$product->price,
    );

    wc_get_template('template-parts/single_product/single_product_right.php',$result);
}

add_action('fashe_single_product_right_section','fashe_single_product_right_section');


/**
 * Trigger the single product add to cart action.
 */
function fashe_template_single_add_to_cart() {
    global $product;
    do_action( 'fashe_' . $product->get_type() . '_add_to_cart' );
}

/**
 * Output the simple product add to cart area.
 */
function fashe_simple_add_to_cart() {
    wc_get_template( 'template-parts/single_product/add-to-cart/variable.php' );
}

/**
 * Output the grouped product add to cart area.
 */
function fashe_grouped_add_to_cart() {
    global $product;

    $products = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

    if ( $products ) {
        wc_get_template( 'template-parts/single_product/add-to-cart/grouped.php', array(
            'grouped_product'    => $product,
            'grouped_products'   => $products,
            'quantites_required' => false,
        ) );
    }
}

/**
 * Output the variable product add to cart area.
 */
function fashe_variable_add_to_cart() {
    global $product;

    // Enqueue variation scripts.
    wp_enqueue_script( 'wc-add-to-cart-variation' );

    // Get Available variations?
    $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

    // Load the template.
    wc_get_template( 'template-parts/single_product/add-to-cart/variable.php', array(
        'available_variations' => $get_variations ? $product->get_available_variations() : false,
        'attributes'           => $product->get_variation_attributes(),
        'selected_attributes'  => $product->get_default_attributes(),
    ) );
}

/**
 * Output the external product add to cart area.
 */
function fashe_external_add_to_cart() {
    global $product;

    if ( ! $product->add_to_cart_url() ) {
        return;
    }

    wc_get_template( 'template-parts/single_product/add-to-cart/external.php', array(
        'product_url' => $product->add_to_cart_url(),
        'button_text' => $product->single_add_to_cart_text(),
    ) );
}

/**
 * Output placeholders for the single variation.
 */

//function fashe_single_variation() {
//    echo '<div class="woocommerce-variation single_variation"></div>';
//}


/**
 * Output the add to cart button for variations.
 */
function fashe_single_variation_add_to_cart_button() {
    wc_get_template( 'template-parts/single_product/add-to-cart/variation-add-to-cart-button.php' );
}

function fashe_wc_dropdown_variation_attribute_options( $args = array() ) {
    $args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
        'options'          => false,
        'attribute'        => false,
        'product'          => false,
        'selected'         => false,
        'name'             => '',
        'id'               => '',
        'class'            => '',
        'show_option_none' => __( 'Choose an option', 'woocommerce' ),
    ) );

    // Get selected value.
    if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
        $selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
        $args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
    }

    $options               = $args['options'];
    $product               = $args['product'];
    $attribute             = $args['attribute'];
    $name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
    $id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
    $class                 = $args['class'];
    $show_option_none      = (bool) $args['show_option_none'];
    $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

    if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
        $attributes = $product->get_variation_attributes();
        $options    = $attributes[ $attribute ];
    }

    $html  = '<select id="' . esc_attr( $id ) . '" class="selection-2 select2-hidden-accessible" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '" tabindex="-1" aria-hidden="true">';
    $html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

    if ( ! empty( $options ) ) {
        if ( $product && taxonomy_exists( $attribute ) ) {
            // Get terms if this is a taxonomy - ordered. We need the names too.
            $terms = wc_get_product_terms( $product->get_id(), $attribute, array(
                'fields' => 'all',
            ) );

            foreach ( $terms as $term ) {
                if ( in_array( $term->slug, $options, true ) ) {
                    $html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                }
            }
        } else {
            foreach ( $options as $option ) {
                // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                $html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
            }
        }
    }

    $html .= '</select>';

    echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
}




    /**
     * Output the product image before the single product summary.
     */
    function fashe_show_product_images() {
        wc_get_template( 'template-parts/single_product/image/product-image.php' );
    }

    add_action('fashe_show_product_images','fashe_show_product_images');

    /**
     * Output the product thumbnails.
     */
    function fashe_show_product_thumbnails() {
        wc_get_template( 'template-parts/single_product/image/product-thumbnails.php' );
    }

    add_action('fashe_show_product_thumbnails','fashe_show_product_images');
