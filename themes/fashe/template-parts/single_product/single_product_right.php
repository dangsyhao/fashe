
<?php global $product;?>

<div class="w-size14 p-t-30 respon5">
    <h4 class="product-detail-name m-text16 p-b-13"><?php the_title();?></h4>

    <span class="m-text17"><?= '$'.$product->price;?></span>

    <p class="s-text8 p-t-10">
        <?= esc_html(get_the_excerpt());?>
    </p>

    <!--  -->
    <div class="p-t-33 p-b-60">

        <?php do_action('fashe_single_product_summary')?>

    </div>
        <?php do_action('fashe_meta_single_product')?>

    <!--  -->
    <div class="wrap-dropdown-content bo6 p-t-15 p-b-14 active-dropdown-content">
        <h5 class="js-toggle-dropdown-content flex-sb-m cs-pointer m-text19 color0-hov trans-0-4">
            Description
            <i class="down-mark fs-12 color1 fa fa-minus dis-none" aria-hidden="true"></i>
            <i class="up-mark fs-12 color1 fa fa-plus" aria-hidden="true"></i>
        </h5>

        <div class="dropdown-content dis-none p-t-15 p-b-23" style="display: none;">
            <p class="s-text8">
                <?= esc_html(get_the_content());?>
            </p>
        </div>
    </div>

    <div class="wrap-dropdown-content bo7 p-t-15 p-b-14">
        <h5 class="js-toggle-dropdown-content flex-sb-m cs-pointer m-text19 color0-hov trans-0-4">
            Additional information
            <i class="down-mark fs-12 color1 fa fa-minus dis-none" aria-hidden="true"></i>
            <i class="up-mark fs-12 color1 fa fa-plus" aria-hidden="true"></i>
        </h5>

        <div class="dropdown-content dis-none p-t-15 p-b-23">
            <p class="s-text8">
                <?php do_action( 'fashe_display_product_attributes', $product ); ?>
            </p>
        </div>
    </div>

    <div class="wrap-dropdown-content bo7 p-t-15 p-b-14">
        <h5 class="js-toggle-dropdown-content flex-sb-m cs-pointer m-text19 color0-hov trans-0-4">
            Reviews (0)
            <i class="down-mark fs-12 color1 fa fa-minus dis-none" aria-hidden="true"></i>
            <i class="up-mark fs-12 color1 fa fa-plus" aria-hidden="true"></i>
        </h5>

        <div class="dropdown-content dis-none p-t-15 p-b-23">
            <p class="s-text8">
                Fusce ornare mi vel risus porttitor dignissim. Nunc eget risus at ipsum blandit ornare vel sed velit. Proin gravida arcu nisl, a dignissim mauris placerat
            </p>
        </div>
    </div>
</div>