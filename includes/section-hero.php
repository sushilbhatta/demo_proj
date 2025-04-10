
<section id="hero">
    <?php 
        $id=47; 
        $post = get_post($id); 
        $content = apply_filters('the_content', $post->post_content); 
        echo $content;  
    ?>
</section>