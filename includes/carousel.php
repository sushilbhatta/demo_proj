
<?php 
    $feature_block = get_blocks_by_name_with_class('core/group', 'carousel');

    if (!empty($feature_block)) {

        foreach ($feature_block as $block) {
            echo apply_filters('the_content', render_block($block));
        }
    }
?>