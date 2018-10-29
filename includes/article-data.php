<?php
/**
 * Jet Popup post type template
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Article_Data' ) ) {

	/**
	 * Define Croco_School_Article_Data class
	 */
	class Croco_School_Article_Data {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		public function __construct( $args = [] ) {

		}

		/**
		 * [get_article_listing description]
		 * @param  array  $args [description]
		 * @return [type]       [description]
		 */
		public function get_article_listing( $args = [] ) {

			if ( have_posts() ) : while ( have_posts() ) : the_post();

					include croco_school()->get_template( 'croco-article-list-item.php' );

				endwhile;

			endif;
		}

		/**
		 * [get_single_article description]
		 * @return [type] [description]
		 */
		public function get_single_article() {

			$post_id = get_the_ID();

			if ( ! has_term( '', croco_school()->post_type->course_term_slug() ) ) {
				$this->get_single_guide_article();
			} else {
				$this->get_single_course_article();
			}
		}

		/**
		 * [get_single_guide_article description]
		 * @return [type] [description]
		 */
		public function get_single_guide_article() {

			?><div class="croco-school__single-article guide-article"><?php

				if ( is_active_sidebar( 'croco-school-article-sidebar' ) ) : ?>
					<aside id="secondary" class="croco-school__single-article-sidebar">
						<?php dynamic_sidebar( 'croco-school-article-sidebar' ); ?>
					</aside><!-- #secondary -->
				<?php endif;

				while ( have_posts() ) : the_post();

				?><article id="primary" class="croco-school__single-article-content"><?php

					$post_id = get_the_ID();

					$format = $this->get_post_format( $post_id );

					$post_format_template = 'croco-article-' . $format . '-single-post.php';

					croco_school()->progress->article_progress_start();

					include croco_school()->get_template( $post_format_template );

				?></article><?php
				endwhile;

			?></div><?php
		}

		/**
		 * [get_single_course_article description]
		 * @return [type] [description]
		 */
		public function get_single_course_article() {

			?><div class="croco-school__single-article course-article"><?php

				while ( have_posts() ) : the_post();

				?><article id="primary" class="croco-school__single-article-content"><?php

					$post_id = get_the_ID();

					$format = $this->get_post_format( $post_id );

					$post_format_template = 'croco-article-' . $format . '-single-post.php';

					croco_school()->progress->article_progress_start();

					include croco_school()->get_template( $post_format_template );

					echo $this->get_done_lesson_button();

				?></article><?php
				endwhile;

				if ( is_active_sidebar( 'croco-school-course-article-sidebar' ) ) : ?>
					<aside id="secondary" class="croco-school__single-article-sidebar">
						<?php dynamic_sidebar( 'croco-school-course-article-sidebar' ); ?>
					</aside><!-- #secondary -->
				<?php endif;

			?></div><?php
		}

		/**
		 *
		 */
		public function get_done_lesson_button() {
			global $wp;

			$article_id = get_the_ID();

			$progress_status = croco_school()->progress->get_article_progress( $article_id );

			$current_url = home_url( add_query_arg( [], $wp->request ) );

			$html = '<div class="croco-school__lesson-progress status-' . $progress_status . '">';

			switch ( $progress_status ) {
				case 'in_progress':

					$current_url = add_query_arg( [
						'action'     => 'croco_article_progress_done',
						'article_id' => $article_id,
					], $current_url );

					$html .= sprintf( '<a href="%s"><span>%s</span></a>', $current_url, __( 'Finish The Lesson', 'croco-school' ) );

					break;

				case 'done':

					$current_url = add_query_arg( [
						'action'     => 'croco_article_set_in_progress',
						'article_id' => $article_id,
					], $current_url );

					$html .= sprintf(
						'<span class="progress-message">%s</span><a href="%s"><span>%s</span></a>',
						__( 'Lesson Learned', 'croco-school' ),
						$current_url,
						__( 'Go Back This Lesson To Training', 'croco-school' ) );

					break;
			}

			$html .= '</div>';

			return $html;
		}

		/**
		 * [$post_id description]
		 * @var [type]
		 */
		public function get_post_format( $post_id = null ) {

			$format = get_post_format( $post_id );

			return ( ! empty( $format ) ) ? $format : 'standard';
		}

		/**
		 * Prints current page title.
		 *
		 * @return void
		 */
		public function page_title( $format = '%s' ) {

			$object = get_queried_object();

			if ( isset( $object->post_title ) ) {
				printf( $format, $object->post_title );
			} elseif ( isset( $object->name ) ) {
				printf( $format, $object->name );
			}

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}
