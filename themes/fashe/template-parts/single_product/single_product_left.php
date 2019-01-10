
    <div class="w-size13 p-t-30 respon5">
        <div class="wrap-slick3 flex-sb flex-w">
            <div class="wrap-slick3-dots"></div>
            <div class="slick3">
                <?php foreach (array_reverse($attachment_ids) as $attachment_id):?>
                    <?php $image_gallery=wp_get_attachment_image_src( $attachment_id,'fashe-single-product-thumbnail');?>

                    <div class="item-slick3" data-thumb="<?= $image_gallery[0];?>">
                        <div class="wrap-pic-w">
                            <img src="<?= $image_gallery[0];?>" alt="IMG-PRODUCT">
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>




