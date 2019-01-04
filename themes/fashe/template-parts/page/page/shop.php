<section class="bgwhite p-t-55 p-b-65">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-50">

                <?php get_template_part('template-parts/page/page/sub/page-content/slide-bar');?>

            </div>

            <!-- Product -->
            <div class="col-sm-6 col-md-8 col-lg-9 p-b-50">

                <?php //get_template_part('template-parts/page/page/sub/page-content/filter');?>
                <?php while (have_posts()):the_post();?>
                    <?php the_content(); ?>
                <?php endwhile;?>

            </div>

            </div>

        </div>
    </div>
</section>