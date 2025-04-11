<?php get_header(); ?>
<?php

    $hide_title = get_post_meta( get_the_ID(), '_hide_page_title', true );
    if ( $hide_title !== 'yes' ) {
        the_title( '<h1>', '</h1>' );
    }
    ?>
    <main class="container">    
            <?php get_template_part( '/includes/section', 'hero' ); ?>
    </main>
<?php get_footer(); ?>
    