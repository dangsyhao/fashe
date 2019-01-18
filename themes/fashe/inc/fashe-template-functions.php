
<?php

function fashe_product_loop_shop(){
    ?>
    <?php global $product;?>
<div class="col-sm-12 col-md-6 col-lg-4 p-b-50">
    <div class="block2">
        <div class="block2-img wrap-pic-w of-hidden pos-relative block2-labelnew">
            <?= woocommerce_get_product_thumbnail();?>
            <div class="block2-overlay trans-0-4">
                <a href="#" class="block2-btn-addwishlist hov-pointer trans-0-4" tabindex="0">
                    <i class="icon-wishlist icon_heart_alt" aria-hidden="true"></i>
                    <i class="icon-wishlist icon_heart dis-none" aria-hidden="true"></i>
                </a>
                <div class="block2-btn-addcart w-size1 trans-0-4">
                        <?php do_action('fashe_product_loop_add_to_cart');?>
                </div>
            </div>
        </div>
        <div class="block2-txt p-t-20">
            <a href="<?php the_permalink($product->term_ID)?>" class="block2-name dis-block s-text3 p-b-5" tabindex="0">
                <?= get_the_title();?>
            </a>
            <?= $product->regular_price ?'<span class="block2-oldprice m-text7 p-r-5">$'.$product->regular_price.'</span>':''; ?>
            <?= $product->sale_price ?'<span class="block2-newprice m-text8 p-r-5">$'.$product->sale_price.'</span>':''; ?>
        </div>
    </div>
</div>

    <?php
}

/*
 *
 */

function fashe_product_loop_home(){
    ?>
    <?php global $product;?>
    <div class="col-sm-12 col-md-6 col-lg-4 p-b-50">
        <div class="block2">
            <div class="block2-img wrap-pic-w of-hidden pos-relative block2-labelnew">

                <img src="<?= get_the_post_thumbnail_url($product->term_ID)?>" alt="IMG-PRODUCT" height="360">

                <div class="block2-overlay trans-0-4">
                    <a href="#" class="block2-btn-addwishlist hov-pointer trans-0-4" tabindex="0">
                        <i class="icon-wishlist icon_heart_alt" aria-hidden="true"></i>
                        <i class="icon-wishlist icon_heart dis-none" aria-hidden="true"></i>
                    </a>
                    <div class="block2-btn-addcart w-size1 trans-0-4">
                            <?php do_action('fashe_product_loop_add_to_cart');?>
                    </div>
                </div>

            </div>

            <div class="block2-txt p-t-20">
                <a href="<?php the_permalink($product->term_ID)?>" class="block2-name dis-block s-text3 p-b-5" tabindex="0">
                    <?= get_the_title();?>
                </a>
                <?= $product->regular_price ?'<span class="block2-oldprice m-text7 p-r-5">$'.$product->regular_price.'</span>':''; ?>
                <?= $product->sale_price ?'<span class="block2-newprice m-text8 p-r-5">$'.$product->sale_price.'</span>':''; ?>
            </div>

        </div>
    </div>
    <?php
}

/*
 *
 */

function fashe_categories_template_home($category){

    $total_category=count($category);
    $cat_per_section=2;
    $total_section=($total_category % $cat_per_section) ==0 ?intval($total_category / $cat_per_section):intval($total_category / $cat_per_section)+1;
    ?>
    <?php for($section=1;$section<=$total_section;$section++):?>
        <div class="col-sm-10 col-md-8 col-lg-4 m-l-r-auto">
        <?php $item_start=($section-1)*$cat_per_section?>
        <?php for($j=$item_start;$j<$item_start+$cat_per_section;$j++):?>
            <?php if($category[$j]->term_id):?>
            <!-- Blog -->
            <div class="block1 hov-img-zoom pos-relative m-b-30">
                <?php $item_category=$category[$j];
                    do_action('fashe_woocommerce_subcategory_thumbnail',$item_category);
                ?>
                <div class="block1-wrapbtn w-size2">
                    <!-- Button -->
                    <a href="<?= get_category_link($category[$j]->term_id)?>" class="flex-c-m size2 m-text2 bg3 hov1 trans-0-4">
                        <?= $category[$j]->name; ?>
                    </a>
                </div>
            </div>
            <!-- Blog -->
            <?php else:?>

                <div class="block2 wrap-pic-w pos-relative m-b-30">
                    <img src="<?= ASSETS_PATH;?>images/icons/bg-01.jpg" alt="IMG">

                    <div class="block2-content sizefull ab-t-l flex-col-c-m">
                        <h4 class="m-text4 t-center w-size3 p-b-8">
                            Sign up &amp; get 20% off
                        </h4>

                        <p class="t-center w-size4">
                            Be the frist to know about the latest fashion news and get exclu-sive offers
                        </p>

                        <div class="w-size2 p-t-25">
                            <!-- Button -->
                            <a href="#" class="flex-c-m size2 bg4 bo-rad-23 hov1 m-text3 trans-0-4">
                                Sign Up
                            </a>
                        </div>
                    </div>
                </div>

            <?php endif;?>
        <?php endfor;?>

        </div>
    <?php endfor;?>

    <?php
}

/*
 * Main Banner Function ... .
 */

function fashe_main_banner(){

    $args=array(
        'orderby' => 'ID',
        'order' => 'DESC',
        'post_type' => 'main_banner'
    );

    $the_query= new WP_Query($args);
    $posts=$the_query->posts;
    $banner_items=array();

    foreach ($posts as $post){

        $banner_items[]=get_field('main_banners',$post->ID);
    }

return $banner_items;

}
 add_filter('fashe_main_banner','fashe_main_banner');

/*
 * Main Banner Function ... .
 */

function fashe_news_posts(){

    $args=array(
        'posts_per_page' =>  3,
        'orderby'       => 'post_date',
        'order'         => 'DESC',
        'post_type'     => 'post',
        'post_status'   =>  'publish'
    );

    $the_query= new WP_Query($args);
    $posts=$the_query->posts;

    return $posts;

}
add_filter('fashe_news_posts','fashe_news_posts');

/*
 * Main Banner Function ... .
 */

function fashe_mini_cart_header(){

    wc_get_template( 'woocommerce/cart/mini-cart.php');
}
add_action('fashe_mini_cart_header','fashe_mini_cart_header');

/*
 * Main Banner Function ... .
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

