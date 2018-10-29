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
				'taxonomy'   => 'croco-сourse',
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
				'taxonomy'   => 'croco-article-category',
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

	}

}
