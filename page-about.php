<?php
/*
 * Template Name: About Us
 */

$achievment = [
    "satisfied_client" => [
        'value' => '1,000+',
        "title" => 'Satisfied Clients'
    ],
    "total_cleaning_hr" => [
        'value' => '10,000+',
        "title" => 'Hour Of Cleaning Per Year'
    ],
    "eco_friendly" => [
        'value' => '100%',
        "title" => 'Eco-Friendly Products'
    ],
    "avability" => [
        'value' => '100%',
        "title" => 'Availability'
    ],
];
get_header('about');
?>
<main class="about-main">
    <?php
    $headings   = get_post_meta(get_the_ID(), '_about_krisli_headings', true) ?: '';
    $paragraphs = get_post_meta(get_the_ID(), '_about_krisli_paragraphs', true) ?: '';
    $images     = get_post_meta(get_the_ID(), '_about_krisli_images', true) ?: '';
    $buttons    = get_post_meta(get_the_ID(), '_about_krisli_buttons', true) ?: '';
    $button_links = get_post_meta(get_the_ID(), '_about_krisli_button_links', true) ?: '';
    $image_orintation = get_post_meta(get_the_ID(), '_about_krisli_img_orintation', true) ?: '';
    ?>
    <section class="aboutus-main">

        <article class="feature layout-<?php echo esc_attr($image_orintation ?? 'top'); ?> ">
            <div class="feature_content">
                <?php if (!empty($headings)): ?>
                    <h2 class="content_heading"><?php echo esc_html($headings); ?></h2>
                <?php endif; ?>

                <?php if (!empty($paragraphs)): ?>
                    <p class="content_description"><?php echo esc_html($paragraphs); ?></p>
                <?php endif; ?>

                <?php if (!empty($buttons) && !empty($button_links)): ?>
                    <a class="contact_btn" href="<?php echo esc_url($button_links); ?>">
                        <span contact_btn--text>

                            <?php echo esc_html($buttons); ?>
                        </span> </a>
                <?php endif; ?>
            </div>

            <div class="image-container">
                <?php if (!empty($images)): ?>
                    <img class=" fe" src="<?php echo esc_url($images); ?>" alt="Contact_Image">
                <?php endif; ?>
            </div>
        </article>
    </section>

    <!-- achievment section -->
    <section class="achievement">
        <?php foreach ($achievment as $items): ?>
            <div>
                <h2 class="achievement-value"><?php echo $items['value']; ?></h2>
                <p class="achievement-title"><?php echo $items['title']; ?></p>
            </div>
        <?php endforeach; ?>
    </section>


    <!--   About us  feature -->
</main>
<?php get_footer(); ?>