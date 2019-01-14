<?php get_header();?>

<?php
get_header( 'shop' ); ?>


<?php
/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action( 'woocommerce_before_main_content' );
?>

<?php while ( have_posts() ) : the_post(); ?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
    <div class="container bgwhite p-t-35 p-b-80">
        <div class="flex-w flex-sb">

            <?php do_action('fashe_single_product_left_section')?>
            <?php do_action('fashe_single_product_right_section')?>
        </div>

    </div>

</div>
<?php endwhile; // end of the loop. ?>

<?php
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<?php
/**
 * woocommerce_sidebar hook.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
//do_action( 'woocommerce_sidebar' );
?>

<?php get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

 get_footer();

