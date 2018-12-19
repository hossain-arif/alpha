<?php

require_once get_theme_file_path() . '/inc/tgm.php';
require_once get_theme_file_path() . '/inc/cmb2-mb.php';

if (class_exists('Attachments')) {

	require_once('lib/attachments.php');
}

if (site_url() == 'http://localhost/lwhh') {
	define("VERSION", time());
} else {
	define("VERSION", wp_get_theme()->get("Version"));
}

function alpha_bootstraping()
{
	load_theme_textdomain("alpha");
	add_theme_support("post-thumbnails");
	add_theme_support("title-tag");
	add_theme_support("html5", array("search-form"));
	$alpha_custom_header_setails = array(
		'header-text' => true,
		'default-text-color' => '#222',
		'width' => 1200,
		'height' => 600,
		'flex-height' => true,
		'flex-width' => true,
	);
	add_theme_support("custom-header", $alpha_custom_header_setails);
	$alpha_custom_logo_defaults = array(
		'width' => 100,
		'height' => 100,
	);
	add_theme_support("custom-logo", $alpha_custom_logo_defaults);
	add_theme_support("custom-background");
	add_theme_support("post-formats", array("image", "audio", "video", "quote"));
	register_nav_menu("topmenu", __("Top Menu", "alpha"));
	register_nav_menu("footermenu", __("Footer Menu", "alpha"));
}

add_action("after_setup_theme", "alpha_bootstraping");


function alpha_assets()
{
	wp_enqueue_style("bootstrap", "//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css");
	wp_enqueue_style("featherlight-css", "//cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.min.css");
	wp_enqueue_style("dashicons");
	wp_enqueue_style("tiny-css", "//cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.8.7/tiny-slider.css");
	wp_enqueue_style("fontawesome","//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css");
	wp_enqueue_style("alpha", get_stylesheet_uri(), null, VERSION);

	wp_enqueue_script("featherlight-js", "//cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.min.js", array("jquery"), VERSION, true);
	wp_enqueue_script("tiny-js", "//cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.8.7/min/tiny-slider.js", null, VERSION, true);
	wp_enqueue_script("alpha-main", get_theme_file_uri("/assets/js/main.js"), array("jquery", "featherlight-js"), VERSION, true);

}

add_action("wp_enqueue_scripts", "alpha_assets");

function alpha_sidebar()
{
	register_sidebar(
		array(
			'name' => __('Single Post Sidebar', 'alpha'),
			'id' => 'sidebar-1',
			'description' => __('Right Sidebar', 'alpha'),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>'
		)
	);

	register_sidebar(
		array(
			'name' => __('Footer Left', 'alpha'),
			'id' => 'footer-left',
			'description' => __('Widgetized area for left side', 'alpha'),
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => ''
		)
	);

	register_sidebar(
		array(
			'name' => __('Footer Right', 'alpha'),
			'id' => 'footer-right',
			'description' => __('Widgetized area for right side', 'alpha'),
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => ''
		)
	);
}

add_action("widgets_init", "alpha_sidebar");

function alpha_the_excerpt($excerpt)
{
	if (!post_password_required()) {
		return $excerpt;
	} else {
		echo get_the_password_form();
	}
}

add_filter("the_excerpt", "alpha_the_excerpt");


function alpha_protected_title_change_format()
{
	return "%s";
}

add_filter("protected_title_format", "alpha_protected_title_change_format");


function alpha_menu_item_class($classes, $item)
{
	$classes[] = "list-inline-item";

	return $classes;
}

add_filter("nav_menu_css_class", "alpha_menu_item_class", 10, 2);


function alpha_about_page_template_banner()
{
	if (is_page()) {
		$alpha_feat_image = get_the_post_thumbnail_url(null, "large");
		?>
        <style>
            .page-header {
                background-image: url(<?php echo $alpha_feat_image;?>);
            }
        </style>
		<?php
	}
	if (is_front_page()) {
		?>
        <style>
            .header {
                background-image: url(<?php echo header_image();?>);
                background-size: cover;
                margin-bottom: 10px;
            }

            .header h1.heading, h3.tagline {
                color: #<?php echo get_header_textcolor();?>;
            <?php
			if (!display_header_text()){
				echo "display: none";
			}
			?>
            }
        </style>
		<?php
	}
}

add_action("wp_head", "alpha_about_page_template_banner", 11);


function alpha_body_class($classes)
{
	unset($classes[array_search("newClass", $classes)]);
	return $classes;
}

add_action("body_class", "alpha_body_class");


function alpha_highlight_search_results($text)
{
	if (is_search()) {
		$pattern = '/(' . join('|', explode(' ', get_search_query())) . ')/i';
		$text    = preg_replace($pattern, '<span class="search-highlight">\0</span>', $text);
	}
	return $text;
}

add_filter('the_content', 'alpha_highlight_search_results');
add_filter('the_excerpt', 'alpha_highlight_search_results');
add_filter('the_title', 'alpha_highlight_search_results');

function alpha_modify_main_query($wpq)
{
	if ($wpq->is_main_query() && is_home()) {
		$wpq->set("tag__not_in", array(11));
	}
}

add_action("pre_get_posts", "alpha_modify_main_query");


//add_filter('acf/settings/show_admin', '__return_false');

function alpha_admin_assets($hook)
{
	if (isset($_REQUEST['post']) || isset($_REQUEST['post_ID'])) {
		$post_id = empty($_REQUEST['post_ID']) ? $_REQUEST['post'] : $_REQUEST['post_ID'];
	}
	if ("post.php" == $hook) {
		$post_format = get_post_format($post_id);
		wp_enqueue_script("admin-js", get_theme_file_uri("/assets/js/admin.js"), array("jquery"), VERSION, true);
		wp_localize_script("admin-js", "alpha_pf", array("format" => $post_format));
	}
}

add_action("admin_enqueue_scripts", "alpha_admin_assets");