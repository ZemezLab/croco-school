<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Utils' ) ) {

	/**
	 * Define Croco_School_Utils class
	 */
	class Croco_School_Utils {


		public static function avaliable_courses() {
			$terms = get_terms( [
				'taxonomy'   => 'сourse',
				'hide_empty' => false,
			] );

			$result = [];

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$result[ $term->term_id ] = $term->name;
				}
			}

			return $result;
		}

		public static function avaliable_article_category() {

			$terms = get_terms( [
				'taxonomy'   => 'article-category',
				'hide_empty' => false,
			] );

			$result = [];

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$result[ $term->term_id ] = $term->name;
				}
			}

			return $result;
		}

		/**
		 * Cut text
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public static function cut_text( $text = '', $length = -1, $trimmed_type = 'word', $after, $content = false ) {

			if ( -1 !== $length ) {

				if ( $content ) {
					$text = strip_shortcodes( $text );
					$text = apply_filters( 'the_content', $text );
					$text = str_replace( ']]>', ']]&gt;', $text );
				}

				if ( 'word' === $trimmed_type ) {
					$text = wp_trim_words( $text, $length, $after );
				} else {
					$text = wp_html_excerpt( $text, $length, $after );
				}
			}

			return $text;
		}

		/**
		 * Get all image sizes.
		 *
		 * Retrieve available image sizes with data like `width`, `height` and `crop`.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 *
		 * @return array An array of available image sizes.
		 */
		public static function get_all_image_sizes() {
			global $_wp_additional_image_sizes;

			$default_image_sizes = [ 'thumbnail', 'medium', 'medium_large', 'large' ];

			$image_sizes = [];

			foreach ( $default_image_sizes as $size ) {
				$image_sizes[ $size ] = [
					'width' => (int) get_option( $size . '_size_w' ),
					'height' => (int) get_option( $size . '_size_h' ),
					'crop' => (bool) get_option( $size . '_crop' ),
				];
			}

			if ( $_wp_additional_image_sizes ) {
				$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
			}

			/** This filter is documented in wp-admin/includes/media.php */
			return apply_filters( 'image_size_names_choose', $image_sizes );
		}

		/**
		 * [get_image_sizes description]
		 * @return [type] [description]
		 */
		public static function get_image_sizes() {
			$wp_image_sizes = self::get_all_image_sizes();

			$image_sizes = [];

			foreach ( $wp_image_sizes as $size_key => $size_attributes ) {
				$control_title = ucwords( str_replace( '_', ' ', $size_key ) );
				if ( is_array( $size_attributes ) ) {
					$control_title .= sprintf( ' - %d x %d', $size_attributes['width'], $size_attributes['height'] );
				}

				$image_sizes[ $size_key ] = $control_title;
			}

			$image_sizes['full'] = _x( 'Full', 'Image Size Control', 'elementor' );

			return $image_sizes;
		}

	}

}
