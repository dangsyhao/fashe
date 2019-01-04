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
        'orderby'      => 'date',
        'order'        => 'DESC',
        'paginate'      =>true,
    ), $atts, 'product' );

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
    ) );

    //--Count result--//

    $args = array(
        'total'    => wc_get_loop_prop( 'total' ),
        'per_page' => wc_get_loop_prop( 'per_page' ),
        'current'  => wc_get_loop_prop( 'current_page' ),
    );

    wc_get_template( 'woocommerce/loop/result-count.php', $args );

    ?>

<?php

}