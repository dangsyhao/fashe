<?php get_header();?>
    <?php get_header( 'shop' ); ?>


    <div class="container bgwhite p-t-35 p-b-80">
        <div class="flex-w flex-sb wc_product_class()">

                <?php while(have_posts()):the_post();?>
                    <?php global $product;?>
                    <?php do_action('fashe_single_product_left_section')?>
                    <?php do_action('fashe_single_product_right_section')?>
                <?php endwhile;?>
        </div>
    </div>
<?php get_footer( 'shop' );?>

<?php get_footer();
