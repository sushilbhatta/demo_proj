<section id="contact-section">
    <?php
    $offer_blocks = get_blocks_by_name_with_class('core/group', 'contact-us');

    if (!empty($offer_blocks)) {
        foreach ($hero_blocks as $block) {
            echo apply_filters('the_content', render_block($block));
        }



        get_template_part('template-parts/contact/contact-section');
    }
    ?>
</section>