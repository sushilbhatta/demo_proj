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
?>