<?php
// theme options
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
	 add_theme_support( 'menus' );
    add_theme_support( 'html5' );
    add_theme_support( 'custom-header' );
	add_theme_support( 'custom-background' );
	
	 function kris_li_enqueue_styles(){
		wp_register_style( 'main-css', get_template_directory_uri() . '/style.css', [], time(), 'all' );	
		wp_enqueue_style('main-css');
	 }

	 add_action( 'wp_enqueue_scripts', 'kris_li_enqueue_styles' );

	//  load js
	function kris_li_enqueue_scripts(){
		wp_register_script( 'main-js',get_template_directory_uri() . '/js/scripts.js', [], time(), true );
		wp_enqueue_script('main-js');
	}

	add_action('wp_enqueue_scripts','kris_li_enqueue_scripts');

	// Menus
	register_nav_menus( [
			'top-menu' => esc_html__('Top Menu Location', 'kris_li'),
			'mobile-menu' => esc_html__('Mobile Menu Location', 'kris_li')
		] );

	


	function sk_add_custom_box() {
		$screens = [ 'post', 'page', 'wporg_cpt' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wporg_box_id',                 // Unique ID
				'Custom Meta Box Title',      // Box title
				'sk_custom_box_html',  // Content callback, must be of type callable
				$screen                            // Post type
			);
		}
	}
	add_action( 'add_meta_boxes', 'sk_add_custom_box' );


	function sk_custom_box_html( $post ) {
		$value = get_post_meta( $post->ID, '_hide_page_title', true );

		echo $value;
	?>
	<label for="sk-field">Description for this field</label>
	<select name="sk_hide_title_field" id="sk-field" class="postbox">
	<option value=""><?php esc_html_e( 'Select', 'kris_li' ); ?></option>
        <option value="yes" <?php selected( $value, 'yes' ); ?>>
            <?php esc_html_e( 'Yes', 'kris_li' ); ?>
        </option>
        <option value="no" <?php selected( $value, 'no' ); ?>>
            <?php esc_html_e( 'No', 'kris_li' ); ?>
        </option>
		</select>
	<?php
	}

	function save_post_meta_data( $post_id ) {

		 // Security check
		if ( array_key_exists( 'sk_hide_title_field', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_hide_page_title',
				$_POST['sk_hide_title_field']
			);
		}
	}
	add_action( 'save_post', 'save_post_meta_data' );
	
?>