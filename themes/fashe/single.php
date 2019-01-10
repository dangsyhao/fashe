<?php get_header();?>

    <div class="container bgwhite p-t-35 p-b-80">
        <div class="flex-w flex-sb">
            <?php if(have_posts()):the_post();?>
            <?php global $product;?>
                <?php do_action('fashe_single_product_left_section')?>
                <?php do_action('fashe_single_product_right_section')?>
            <?php endif;?>

        </div>
    </div>

<?php get_footer();
