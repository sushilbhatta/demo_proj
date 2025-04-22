<?php
// theme options
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support('menus');
add_theme_support('html5');
add_theme_support('custom-header');
add_theme_support('custom-background');
add_theme_support('post-formats', array('gallary'));

function kris_li_enqueue_styles()
{
	wp_register_style('main-css', get_template_directory_uri() . '/style.css', [], time(), 'all');
	wp_enqueue_style('main-css');
}

add_action('wp_enqueue_scripts', 'kris_li_enqueue_styles');

//  load js
function kris_li_enqueue_scripts()
{
	wp_register_script('main-js', get_template_directory_uri() . '/js/scripts.js', [], time(), true);
	wp_enqueue_script('main-js');
}

add_action('wp_enqueue_scripts', 'kris_li_enqueue_scripts');

// Menus
register_nav_menus([
	'top-menu' => esc_html__('Top Menu Location', 'kris_li'),
	'mobile-menu' => esc_html__('Mobile Menu Location', 'kris_li'),
	'footer-menu-quick-link' => esc_html__('Footer Menu Quick Link Location', 'kris_li'),
	'footer-menu-services' => esc_html__('Footer Menu Services Location', 'kris_li'),
	'footer-menu-custom-link' => esc_html__('Footer Menu Custom Link Location', 'kris_li'),
	'footer-menu-end-link' => esc_html__('Footer Menu End Location', 'kris_li'),

]);


function add_dropdown_toggles_to_menu($item_output, $item, $depth, $args)
{
	if (in_array('menu-item-has-children', $item->classes)) {

		$item_output .= '<button class="dropdown-toggle" aria-expanded="false"><span class="screen-reader-text">Toggle submenu</span></button>';
	};
	return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_dropdown_toggles_to_menu', 10, 4);



function get_blocks_by_name_with_class($block_name, $class_name, $blocks = null)
{
	global $post;

	if (is_null($blocks)) {
		if (!isset($post)) return [];
		$blocks = parse_blocks($post->post_content);
	}

	$matched_blocks = [];

	foreach ($blocks as $block) {
		if (
			isset($block['blockName']) &&
			$block['blockName'] === $block_name &&
			isset($block['attrs']['className']) &&
			in_array($class_name, explode(' ', $block['attrs']['className']))
		) {
			$matched_blocks[] = $block;
		}

		// Check inner blocks recursively
		if (!empty($block['innerBlocks'])) {
			$inner_matches = get_blocks_by_name_with_class($block_name, $class_name, $block['innerBlocks']);
			$matched_blocks = array_merge($matched_blocks, $inner_matches);
		}
	}

	return $matched_blocks;
}



// hero section metaboxes

function hero_section_meta_box()
{
	add_meta_box(
		'hero_section_meta',              // ID
		'Hero Section',                   // Title
		'hero_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
}
add_action('add_meta_boxes', 'hero_section_meta_box');

function hero_section_meta_box_callback($post)
{
	wp_nonce_field('hero_section_nonce_action', 'hero_section_nonce');

	$labels     = get_post_meta($post->ID, '_hero_labels', true) ?: [];
	$headings   = get_post_meta($post->ID, '_hero_title', true) ?: [];
	$paragraphs = get_post_meta($post->ID, '_hero_description', true) ?: [];
	$images     = get_post_meta($post->ID, '_hero_bg_images', true) ?: [];
	$buttons    = get_post_meta($post->ID, '_hero_buttons', true) ?: [];
	$button_links = get_post_meta($post->ID, '_hero_button_links', true) ?: [];
	// $image_orintation = get_post_meta($post->ID, '_hero_img_orintation', true) ?: [];
	// $image_container_color = get_post_meta($post->ID, '_hero_container_color', true) ?: [];


	for ($i = 0; $i < 4; $i++) {
?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Hero <?php echo $i + 1; ?></h4>
			<p><label>Hero Label:</label><br>
				<input type="text" name="hero_labels[]" value="<?php echo esc_attr($labels[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Hero Title :</label><br>
				<input type="text" name="hero_title[]" value="<?php echo esc_attr($headings[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Hero Description:</label><br>
				<textarea name="hero_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Image URL:</label><br>
				<input type="text" name="hero_images[]" id="hero_image_<?php echo $i; ?>" value="<?php echo esc_attr($images[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-image" data-target="hero_image_<?php echo $i; ?>">Upload</button>
			</p>
			<p><label>Button Label:</label><br>
				<input type="text" name="hero_buttons[]" value="<?php echo esc_attr($buttons[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Button Link:</label><br>
				<input type="text" name="hero_button_links[]" value="<?php echo esc_attr($button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
	<?php
}




function save_hero_section_meta_box($post_id)
{
	if (
		!isset($_POST['hero_section_nonce']) ||
		!wp_verify_nonce($_POST['hero_section_nonce'], 'hero_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_hero_labels', array_map('sanitize_text_field', $_POST['hero_labels'] ?? []));
	update_post_meta($post_id, '_hero_title', array_map('sanitize_text_field', $_POST['hero_title'] ?? []));
	update_post_meta($post_id, '_hero_description', array_map('sanitize_textarea_field', $_POST['hero_description'] ?? []));
	update_post_meta($post_id, '_hero_bg_images', array_map('esc_url_raw', $_POST['hero_images'] ?? []));
	update_post_meta($post_id, '_hero_buttons', array_map('sanitize_text_field', $_POST['hero_buttons'] ?? []));
	update_post_meta($post_id, '_hero_button_links', array_map('esc_url_raw', $_POST['hero_button_links'] ?? []));
	// update_post_meta($post_id, '_hero_img_orintation', array_map('sanitize_text_field', $_POST['hero_img_orintation'] ?? []));
	// update_post_meta($post_id, '_hero_container_color', array_map('sanitize_text_field', $_POST['hero_img_container_color'] ?? []));
}


add_action('save_post', 'save_hero_section_meta_box');






















//! feature section metaboxes

function feature_section_meta_box()
{
	add_meta_box(
		'feature_section_meta',              // ID
		'Feature Section',                   // Title
		'feature_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
}
add_action('add_meta_boxes', 'feature_section_meta_box');

function feature_section_meta_box_callback($post)
{
	wp_nonce_field('feature_section_nonce_action', 'feature_section_nonce');

	$labels     = get_post_meta($post->ID, '_feature_labels', true) ?: [];
	$headings   = get_post_meta($post->ID, '_feature_headings', true) ?: [];
	$paragraphs = get_post_meta($post->ID, '_feature_paragraphs', true) ?: [];
	$images     = get_post_meta($post->ID, '_feature_images', true) ?: [];
	$buttons    = get_post_meta($post->ID, '_feature_buttons', true) ?: [];
	$button_links = get_post_meta($post->ID, '_feature_button_links', true) ?: [];
	$image_orintation = get_post_meta($post->ID, '_feature_img_orintation', true) ?: [];
	$image_container_color = get_post_meta($post->ID, '_feature_container_color', true) ?: [];


	for ($i = 0; $i < 3; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Feature <?php echo $i + 1; ?></h4>
			<p><label>Label:</label><br>
				<input type="text" name="feature_labels[]" value="<?php echo esc_attr($labels[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Heading:</label><br>
				<input type="text" name="feature_headings[]" value="<?php echo esc_attr($headings[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Paragraph:</label><br>
				<textarea name="feature_paragraphs[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Image URL:</label><br>
				<input type="text" name="feature_images[]" id="feature_image_<?php echo $i; ?>" value="<?php echo esc_attr($images[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-image" data-target="feature_image_<?php echo $i; ?>">Upload</button>
			</p>
			<p><label>Button Label:</label><br>
				<input type="text" name="feature_buttons[]" value="<?php echo esc_attr($buttons[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Button Link:</label><br>
				<input type="text" name="feature_button_links[]" value="<?php echo esc_attr($button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p>
				<label>Image Orintation</label><br>
				<select name="feature_img_orintation[]" id="feature_img_orintation<?php echo $i; ?>">
					<option value="normal" <?php selected($image_orintation[$i], 'top'); ?>>Top</option>
					<option value="right" <?php selected($image_orintation[$i], 'right'); ?>>Right</option>
					<option value="bottom" <?php selected($image_orintation[$i], 'bottom'); ?>>Bottom</option>
					<option value="left" <?php selected($image_orintation[$i], 'left'); ?>>Left</option>
				</select>

			</p>

			<p>
				<label>Image Orintation</label><br>
				<select name="feature_img_container_color[]" id="feature_img_container_color<?php echo $i; ?>">
					<option value="orange" <?php selected($image_container_color[$i], 'orange'); ?>>Light Orange</option>
					<option value="blue" <?php selected($image_container_color[$i], 'blue'); ?>> Light Blue</option>
					<option value="violet" <?php selected($image_container_color[$i], 'violet'); ?>>Violet</option>

				</select>

			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}




function save_feature_section_meta_box($post_id)
{
	if (
		!isset($_POST['feature_section_nonce']) ||
		!wp_verify_nonce($_POST['feature_section_nonce'], 'feature_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_feature_labels', array_map('sanitize_text_field', $_POST['feature_labels'] ?? []));
	update_post_meta($post_id, '_feature_headings', array_map('sanitize_text_field', $_POST['feature_headings'] ?? []));
	update_post_meta($post_id, '_feature_paragraphs', array_map('sanitize_textarea_field', $_POST['feature_paragraphs'] ?? []));
	update_post_meta($post_id, '_feature_images', array_map('esc_url_raw', $_POST['feature_images'] ?? []));
	update_post_meta($post_id, '_feature_buttons', array_map('sanitize_text_field', $_POST['feature_buttons'] ?? []));
	update_post_meta($post_id, '_feature_button_links', array_map('esc_url_raw', $_POST['feature_button_links'] ?? []));
	update_post_meta($post_id, '_feature_img_orintation', array_map('sanitize_text_field', $_POST['feature_img_orintation'] ?? []));
	update_post_meta($post_id, '_feature_container_color', array_map('sanitize_text_field', $_POST['feature_img_container_color'] ?? []));
}


add_action('save_post', 'save_feature_section_meta_box');


function offer_section_meta_box()
{
	add_meta_box(
		'offer_section_meta',
		'Offer Section',
		'offer_section_meta_box_callback',
		['post', 'page'],
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'offer_section_meta_box');

function offer_section_meta_box_callback($post)
{
	$offer_main_heading = get_post_meta($post->ID, '_offer_main_heading', true);
	$offer_main_paragraph = get_post_meta($post->ID, '_offer_main_paragraph', true);

	wp_nonce_field('feature_section_nonce_action', 'feature_section_nonce');
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Offer Heading</h4>
		<p><label>Heading:</label><br>
			<input type="text" name="offer_main_heading" value="<?php echo esc_attr($offer_main_heading); ?>" style="width:100%;">
		</p>
		<p><label>Paragraph:</label><br>
			<textarea name="offer_main_paragraph" rows="4" style="width:100%;"><?php echo esc_textarea($offer_main_paragraph); ?></textarea>
		</p>
	</div>
	<?php
}

function save_offer_section_meta_box($post_id)
{
	if (
		!isset($_POST['feature_section_nonce']) ||
		!wp_verify_nonce($_POST['feature_section_nonce'], 'feature_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_offer_main_heading', sanitize_text_field($_POST['offer_main_heading'] ?? ''));
	update_post_meta($post_id, '_offer_main_paragraph', sanitize_textarea_field($_POST['offer_main_paragraph'] ?? ''));
}
add_action('save_post', 'save_offer_section_meta_box');

//! Offer list MetaBox


// Register the meta_box

function offer_meta_box()
{
	add_meta_box(
		'offer_meta',              // ID
		'Offer Section',                   // Title
		'offer_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
}
add_action('add_meta_boxes', 'offer_meta_box');

function offer_meta_box_callback($post)
{
	wp_nonce_field('offer_nonce_action', 'offer_nonce');

	$offer_icon     = get_post_meta($post->ID, '_offer_icon', true) ?: [];
	$offer_title   = get_post_meta($post->ID, '_offer_title', true) ?: [];
	$offer_description = get_post_meta($post->ID, '_offer_description', true) ?: [];
	$offer_button    = get_post_meta($post->ID, '_offer_button', true) ?: [];
	$offer_button_links = get_post_meta($post->ID, '_offer_button_links', true) ?: [];


	for ($i = 0; $i < 6; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Offers <?php echo $i + 1; ?></h4>


			<p>
				<label>Add Icon URL:</label><br>
				<input type="text" name="offer_image[]" id="offer_image_<?php echo $i; ?>" value="<?php echo esc_attr($offer_icon[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-icon" data-target="offer_image_<?php echo $i; ?>">Upload</button>

			</p>
			<p><label>Title:</label><br>
				<input type="text" name="offer_title[]" value="<?php echo esc_attr($offer_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="offer_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($offer_description[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Button Label:</label><br>
				<input type="text" name="offer_button[]" value="<?php echo esc_attr($offer_button[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p>
				<label>Button Link:</label><br>
				<input type="text" name="offer_button_links[]" value="<?php echo esc_attr($offer_button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-icon').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}




function save_offer_meta_box($post_id)
{
	if (
		!isset($_POST['offer_nonce']) ||
		!wp_verify_nonce($_POST['offer_nonce'], 'offer_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_offer_icon', array_map('esc_url_raw', $_POST['offer_image'] ?? []));
	update_post_meta($post_id, '_offer_title', array_map('sanitize_text_field', $_POST['offer_title'] ?? []));
	update_post_meta($post_id, '_offer_description', array_map('sanitize_textarea_field', $_POST['offer_description'] ?? []));
	update_post_meta($post_id, '_offer_button', array_map('sanitize_text_field', $_POST['offer_button'] ?? []));
	update_post_meta($post_id, '_offer_button_links', array_map('esc_url_raw', $_POST['offer_button_links'] ?? []));
}


add_action('save_post', 'save_offer_meta_box');

// svg Support 
function enable_svg_upload($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'enable_svg_upload');



// !Contact Us metabox


function contact_section_meta_box()
{
	add_meta_box(
		'contact_section_meta',              // ID
		'Contact Section',                   // Title
		'contact_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
}
add_action('add_meta_boxes', 'contact_section_meta_box');

function contact_section_meta_box_callback($post)
{
	wp_nonce_field('contact_section_nonce_action', 'contact_section_nonce');

	$labels     = get_post_meta($post->ID, '_contact_labels', true) ?: '';
	$headings   = get_post_meta($post->ID, '_contact_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_contact_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_contact_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_contact_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_contact_button_links', true) ?: '';
	$image_orintation = get_post_meta($post->ID, '_contact_img_orintation', true) ?: '';

?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Contact Information </h4>
		<p><label>Label:</label><br>
			<input type="text" name="contact_labels" value="<?php echo esc_attr($labels ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Heading:</label><br>
			<input type="text" name="contact_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="contact_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="contact_images" id="contact_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="contact_image">Upload</button>
		</p>
		<p><label>Button Label:</label><br>
			<input type="text" name="contact_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="contact_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>

		<p>
			<label>Image Orintation</label><br>
			<select name="contact_img_orintation" id="contact_img_orintation">
				<option value="normal" <?php selected($image_orintation, 'top'); ?>>Top</option>
				<option value="right" <?php selected($image_orintation, 'right'); ?>>Right</option>
				<option value="bottom" <?php selected($image_orintation, 'bottom'); ?>>Bottom</option>
				<option value="left" <?php selected($image_orintation, 'left'); ?>>Left</option>
			</select>

		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}




function save_contact_section_meta_box($post_id)
{
	if (
		!isset($_POST['contact_section_nonce']) ||
		!wp_verify_nonce($_POST['contact_section_nonce'], 'contact_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_contact_labels', sanitize_text_field($_POST['contact_labels']));
	update_post_meta($post_id, '_contact_headings', sanitize_text_field($_POST['contact_headings']));
	update_post_meta($post_id, '_contact_paragraphs', sanitize_textarea_field($_POST['contact_paragraphs']));
	update_post_meta($post_id, '_contact_images', esc_url_raw($_POST['contact_images']));
	update_post_meta($post_id, '_contact_buttons', sanitize_text_field($_POST['contact_buttons']));
	update_post_meta($post_id, '_contact_button_links', esc_url_raw($_POST['contact_button_links']));
	update_post_meta($post_id, '_contact_img_orintation', sanitize_text_field($_POST['contact_img_orintation']));
}


add_action('save_post', 'save_contact_section_meta_box');


// Testimonial Header


function testimonial_section_meta_box()
{
	add_meta_box(
		'Testimonial_section_meta',
		'Testimonial Section',
		'testimonial_section_meta_box_callback',
		['post', 'page'],
		'advanced',
		'high'
	);
}
add_action('add_meta_boxes', 'testimonial_section_meta_box');

function testimonial_section_meta_box_callback($post)
{
	$testimonial_main_heading = get_post_meta($post->ID, '_testimonial_main_heading', true);
	$testimonial_main_paragraph = get_post_meta($post->ID, '_testimonial_main_paragraph', true);

	wp_nonce_field('testimonial_section_nonce_action', 'testimonial_section_nonce');
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Testimonial Heading</h4>
		<p><label>Heading:</label><br>
			<input type="text" name="testimonial_main_heading" value="<?php echo esc_attr($testimonial_main_heading); ?>" style="width:100%;">
		</p>
		<p><label>Paragraph:</label><br>
			<textarea name="testimonial_main_paragraph" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_main_paragraph); ?></textarea>
		</p>
	</div>
	<?php
}

function save_testimonial_section_meta_box($post_id)
{
	if (
		!isset($_POST['testimonial_section_nonce']) ||
		!wp_verify_nonce($_POST['testimonial_section_nonce'], 'testimonial_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_testimonial_main_heading', sanitize_text_field($_POST['testimonial_main_heading'] ?? ''));
	update_post_meta($post_id, '_testimonial_main_paragraph', sanitize_textarea_field($_POST['testimonial_main_paragraph'] ?? ''));
}
add_action('save_post', 'save_testimonial_section_meta_box');



// Register the meta_box

function testimonial_card_meta_box()
{
	add_meta_box(
		'testimonial_card_meta',              // ID
		'Testimonial Card Section',                   // Title
		'testimonial_card_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
}
add_action('add_meta_boxes', 'testimonial_card_meta_box');

function testimonial_card_meta_box_callback($post)
{
	wp_nonce_field('testimonial_card_nonce_action', 'testimonial_card_nonce');

	$testimonial_card_title   = get_post_meta($post->ID, '_testimonial_card_title', true) ?: [];
	$testimonial_card_description = get_post_meta($post->ID, '_testimonial_card_description', true) ?: [];
	$testimonial_author_name = get_post_meta($post->ID, '_testimonial_card_author_name', true) ?: [];
	$testimonial_author_position = get_post_meta($post->ID, '_testimonial_card_author_position', true) ?: [];


	for ($i = 0; $i < 4; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Testimonial Card <?php echo $i + 1; ?></h4>


			<p><label>Title:</label><br>
				<input type="text" name="testimonial_card_title[]" value="<?php echo esc_attr($testimonial_card_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="testimonial_card_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_card_description[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Author Name:</label><br>
				<textarea name="testimonial_author_name[]" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_author_name[$i] ?? ''); ?></textarea>
			</p>
			<p><label>Author Position:</label><br>
				<textarea name="testimonial_author_position[]" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_author_position[$i] ?? ''); ?></textarea>
			</p>
		</div>
	<?php
	}
}




function save_testimonial_card_meta_box($post_id)
{
	if (
		!isset($_POST['testimonial_card_nonce']) ||
		!wp_verify_nonce($_POST['testimonial_card_nonce'], 'testimonial_card_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_testimonial_card_title', array_map('sanitize_text_field', $_POST['testimonial_card_title'] ?? []));
	update_post_meta($post_id, '_testimonial_card_description', array_map('sanitize_textarea_field', $_POST['testimonial_card_description'] ?? []));
	update_post_meta($post_id, '_testimonial_card_author_name', array_map('sanitize_textarea_field', $_POST['testimonial_author_name'] ?? []));
	update_post_meta($post_id, '_testimonial_card_author_position', array_map('sanitize_textarea_field', $_POST['testimonial_author_position'] ?? []));
}


add_action('save_post', 'save_testimonial_card_meta_box');


// Get In Touch Section

function get_in_touch_section_meta_box()
{
	add_meta_box(
		'get_in_touch_section_meta',              // ID
		'Get_in_touch Section',                   // Title
		'get_in_touch_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
}
add_action('add_meta_boxes', 'get_in_touch_section_meta_box');

function get_in_touch_section_meta_box_callback($post)
{
	wp_nonce_field('get_in_touch_section_nonce_action', 'get_in_touch_section_nonce');

	$headings   = get_post_meta($post->ID, '_get_in_touch_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_get_in_touch_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_get_in_touch_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_get_in_touch_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_get_in_touch_button_links', true) ?: '';
	?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Get_in_touch Section Information </h4>


		<p><label>Heading:</label><br>
			<input type="text" name="get_in_touch_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="get_in_touch_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="get_in_touch_images" id="get_in_touch_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="get_in_touch_image">Upload</button>
		</p>
		<p><label>Button Label:</label><br>
			<input type="text" name="get_in_touch_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="get_in_touch_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}




function save_get_in_touch_section_meta_box($post_id)
{
	if (
		!isset($_POST['get_in_touch_section_nonce']) ||
		!wp_verify_nonce($_POST['get_in_touch_section_nonce'], 'get_in_touch_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_get_in_touch_headings', sanitize_text_field($_POST['get_in_touch_headings']));
	update_post_meta($post_id, '_get_in_touch_paragraphs', sanitize_textarea_field($_POST['get_in_touch_paragraphs']));
	update_post_meta($post_id, '_get_in_touch_images', esc_url_raw($_POST['get_in_touch_images']));
	update_post_meta($post_id, '_get_in_touch_buttons', sanitize_text_field($_POST['get_in_touch_buttons']));
	update_post_meta($post_id, '_get_in_touch_button_links', esc_url_raw($_POST['get_in_touch_button_links']));
}


add_action('save_post', 'save_get_in_touch_section_meta_box');

?>