<section id="off">
    <?php
    $offer_blocks = get_blocks_by_name_with_class('core/group', 'offers');

    if (!empty($offer_blocks)) {
        foreach ($hero_blocks as $block) {
            echo apply_filters('the_content', render_block($block));
        }



        get_template_part('template-parts/offer/head-section');
        get_template_part('template-parts/offer/item-section');
    }
    ?>
</section>