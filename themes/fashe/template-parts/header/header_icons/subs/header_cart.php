
<div class="header-wrapicon2">
    <img src="<?= ASSETS_PATH;?>images/icons/icon-header-02.png" class="header-icon1 js-show-header-dropdown" alt="ICON">
    <span class="header-icons-noti"><?php echo wp_kses_post(WC()->cart->get_cart_contents_count()); ?></span>

    <?php do_action( 'fashe_mini_cart_header'); ?>

</div>