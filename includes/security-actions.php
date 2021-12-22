<?php

/**
 * Security actions for website
 *
 * @author      Makarand Mane
 * @category    Admin
 * @package     Initialization
 * @version     1.0
 */

/**
 * Disable the emoji's
 * Remove unwanted meta data from html head.
 * 
 * https://kinsta.com/knowledgebase/disable-emojis-wordpress/
 * 
 * @return void
 */

function cmp_security_patches() {
	
	/******
	*	Disable emoji
	*/
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
	add_filter( 'wp_resource_hints', 'wplms_dns_prefetch', 10, 2 );
	
	add_filter( 'emoji_svg_url', '__return_empty_string', 10 );
	
	//https://wordpress.stackexchange.com/questions/211467/remove-json-api-links-in-header-html
	// Remove the REST API lines from the HTML Header
	remove_action( 'wp_head',      'rest_output_link_wp_head'              );
	remove_action( 'wp_head',      'wp_oembed_add_discovery_links'         );
	remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );	
	// Remove the REST API endpoint.
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );	
	// Turn off oEmbed auto discovery.
	add_filter( 'embed_oembed_discover', '__return_false' );	
	// Don't filter oEmbed results.
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );	
	// Remove oEmbed discovery links.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );	
	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );	
	// Remove all embeds rewrite rules.
	//add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
	remove_action( 'wp_head', 'rsd_link' );
	
	/******
	*	Remove meta information
	*/
	//Remove WordPress Meta Generator Tag
	remove_action('wp_head', 'wp_generator');

	//Remove Visual Composer / WPBakery Page Builder Meta Generator Tag
	//remove_action('wp_head', array( visual_composer(), 'addMetaData') ); Uncomment this line if Visual composer is Install.

	//Remove Slider Revolution Meta Generator Tag
	add_filter( 'revslider_meta_generator', '__return_empty_string' );
	
	add_filter('xmlrpc_enabled', '__return_false');
}
add_action( 'init', 'cmp_security_patches' );

 
/**
* Filter function used to remove the tinymce emoji plugin.
* 
* @param array $plugins 
* @return array Difference betwen the two arrays
*/
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
* Remove emoji CDN hostname from DNS prefetching hints.
*
* @param array $urls URLs to print for resource hints.
* @param string $relation_type The relation type the URLs are printed for.
* @return array Difference betwen the two arrays.
*/
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		
		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}

	return $urls;
}

/**
 * Prefetch google fonts domains
 *
 * @param [string] $urls
 * @param [string] $relation_type
 * @return void
 */
function wplms_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		$urls[] = '//fonts.googleapis.com';
		$urls[] = '//fonts.gstatic.com';
	}

	return $urls;
}
