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
		 * [get_single_article description]
		 * @return [type] [description]
		 */
		public function get_single_article() {

			$post_id = get_the_ID();

			if ( ! has_term( '', croco_school()->post_type->course_term_slug() ) ) {

				if ( function_exists( 'cherry_get_search_form' ) ) {
					?><div class="croco-school__single-search">
						<div class="croco-school-container"><?php
							cherry_get_search_form();
						?></div>
					</div><?php
				}

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

			$is_active_sidebar = is_active_sidebar( 'croco-school-article-sidebar' );

			$is_sidebar_class = $is_active_sidebar ? 'has-sidebar' : 'no-sidebar';

			?><div class="croco-school__single-article container guide-article <?php echo $is_sidebar_class; ?>"><?php

				if ( $is_active_sidebar ) : ?>
					<aside id="secondary" class="croco-school__single-article-sidebar">
						<div class="croco-school__single-article-sidebar-inner">
							<?php dynamic_sidebar( 'croco-school-article-sidebar' ); ?>
						</div>
					</aside><!-- #secondary -->
				<?php endif;

				while ( have_posts() ) : the_post();

				?><article id="primary" class="croco-school__single-article-container">
					<div class="croco-school__single-article-container-inner"><?php

						$post_id = get_the_ID();

						$format = $this->get_post_format( $post_id );?>

						<h1 class="croco-school__single-article-title"><?php echo the_title(); ?></h1>

						<div class="croco-school__single-article-content"><?php
							ob_start();
							the_content( '' );
							$content = ob_get_contents();
							ob_end_clean();

							echo $content;
						?></div>
					</div>
				</article><?php
				endwhile;

			?></div><?php
		}

		/**
		 * [get_single_course_article description]
		 * @return [type] [description]
		 */
		public function get_single_course_article() {

			$is_active_sidebar = is_active_sidebar( 'croco-school-course-article-sidebar' );

			$is_sidebar_class = $is_active_sidebar ? 'has-sidebar' : 'no-sidebar';

			?><div class="croco-school__single-article container course-article <?php echo $is_sidebar_class; ?>"><?php

				while ( have_posts() ) : the_post();

				?><article id="primary" class="croco-school__single-article-container">
					<div class="croco-school__single-article-container-inner"><?php

						$post_id = get_the_ID();

						$format = $this->get_post_format( $post_id );

						croco_school()->progress->article_progress_start();?>

						<h1 class="croco-school__single-article-title"><?php echo the_title(); ?></h1>

						<div class="croco-school__single-article-content"><?php
							ob_start();
							the_content( '' );
							$content = ob_get_contents();
							ob_end_clean();

							echo $content;
						?></div><?php

						echo $this->get_done_lesson_button();
					?></div>
				</article><?php
				endwhile;

				if ( $is_active_sidebar ) : ?>
					<aside id="secondary" class="croco-school__single-article-sidebar">
						<div class="croco-school__single-article-sidebar-inner">
							<?php dynamic_sidebar( 'croco-school-course-article-sidebar' ); ?>
						</div>
					</aside><!-- #secondary -->
				<?php endif;?>

			</div><?php
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

					$html .= sprintf( '<a class="finish-button" href="%s"><i class="nc-icon-glyph sport_flag-finish"></i><span>%s</span></a>', $current_url, __( 'Finish The Lesson', 'croco-school' ) );

					break;

				case 'done':

					$current_url = add_query_arg( [
						'action'     => 'croco_article_set_in_progress',
						'article_id' => $article_id,
					], $current_url );

					$html .= sprintf(
						'<div class="progress-message">
							<i class="nc-icon-glyph ui-1_check-bold"></i>
							<span>%s</span>
						</div>
						<a class="go-back-button" href="%s"><i class="nc-icon-glyph arrows-1_curved-previous"></i><span>%s</span></a>',
						__( 'Lesson Learned', 'croco-school' ),
						$current_url,
						__( 'Go Back This Lesson To Training', 'croco-school' ) );

					break;
			}

			$term_list = get_the_terms( $article_id, croco_school()->post_type->course_term_slug() );

			if ( ! empty( $term_list ) ) {
				$course_id = $term_list[0]->term_id;

				$course_link = get_term_link( (int)$course_id, croco_school()->post_type->course_term_slug() );

				$html .= sprintf( '<a class="back-button" href="%s"><i class="nc-icon-glyph education_book-bookmark"></i><span>%s</span></a>', $course_link, __( 'Back To Course', 'croco-school' ) );
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
