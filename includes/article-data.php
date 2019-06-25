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
			add_filter( 'cx_breadcrumbs/trail_taxonomies', [ $this, 'modify_breadcrumbs_trail_taxonomies'] );
		}

		/**
		 * [modify_breadcrumbs_trail_taxonomies description]
		 * @param  [type] $trail_taxonomies [description]
		 * @return [type]                   [description]
		 */
		public function modify_breadcrumbs_trail_taxonomies ( $trail_taxonomies ) {

			$trail_taxonomies['article'] = 'article-category';

			return $trail_taxonomies;
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

			//$is_active_sidebar = is_active_sidebar( 'croco-school-article-sidebar' );

			$is_active_sidebar = false;

			$is_sidebar_class = $is_active_sidebar ? 'has-sidebar' : 'no-sidebar';

			?><div class="croco-school__single-article container guide-article <?php echo $is_sidebar_class; ?>"><?php
					do_action( 'cx_breadcrumbs/render' );

					//$this->render_back_btn_html();

				?><div class="croco-school__single-article-inner"><?php

					while ( have_posts() ) : the_post();

					?><article id="primary" class="croco-school__single-article-container">
						<div class="croco-school__single-article-container-inner"><?php

							$post_id = get_the_ID();

							$format = $this->get_post_format( $post_id );?>

							<h1 class="croco-school__single-article-title"><?php echo the_title(); ?></h1>
							<?php	$this->get_article_media();?>
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
					if ( $is_active_sidebar ) : ?>
						<aside id="secondary" class="croco-school__single-article-sidebar">
							<div class="croco-school__single-article-sidebar-inner">
								<?php dynamic_sidebar( 'croco-school-article-sidebar' ); ?>
							</div>
						</aside><!-- #secondary -->
					<?php endif;?>
				</div>
			</div><?php
		}

		/**
		 * [get_single_course_article description]
		 * @return [type] [description]
		 */
		public function get_single_course_article() {

			$is_active_sidebar = is_active_sidebar( 'croco-school-course-article-sidebar' );

			$is_sidebar_class = $is_active_sidebar ? 'has-sidebar' : 'no-sidebar';

			?><div class="croco-school__single-article container course-article <?php echo $is_sidebar_class; ?>"><?php
					$this->get_article_media();
				?><div class="croco-school__single-article-inner"><?php

					while ( have_posts() ) : the_post();

					?><article id="primary" class="croco-school__single-article-container">
						<div class="croco-school__single-article-container-inner">
								<?php

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
				</div>
			</div><?php
		}

		/**
		 * [get_article_media description]
		 * @return [type] [description]
		 */
		public function get_article_media() {
			$post_id = get_the_ID();

			$article_type = get_post_meta( $post_id, 'article_format', true );

			if ( 'standard' === $article_type ) {
				return false;
			}

			?><div class="croco-school__single-article-media">
				<div class="croco-school__single-article-media-inner"><?php

			switch ( $article_type ) {
				case 'image':

					$article_image = get_post_meta( $post_id, 'article_image', true );
					$article_image_size = get_post_meta( $post_id, 'article_image_size', true );

					echo wp_get_attachment_image( $article_image, $article_image_size, false, [ 'class' => 'croco-school-article-image' ] );

					# code...
					break;

				case 'video':
					$video_url = get_post_meta( $post_id, 'video_url', true );
					$video_poster = get_post_meta( $post_id, 'video_poster', true );
					$video_aspect_ratio = get_post_meta( $post_id, 'video_aspect_ratio', true );
					$video_aspect_ratio = isset( $video_aspect_ratio ) ? $video_aspect_ratio : '169';

					?><div class="croco-school__single-media-frame aspect-ratio-<?php echo $video_aspect_ratio; ?>"><?php

						$video_properties = $this->get_video_properties( $video_url );

						echo $this->get_embed_html( $video_url, [
							'autoplay'       => '0',
							'controls'       => '1',
							'rel'            => '0',
							'loop'           => '0',
							'wmode'          => 'opaque',
							'playlist'       => $video_properties['video_id'],
							'modestbranding' => '0',
						] );

						if ( ! empty( $video_poster ) ) {
							$poster_data = wp_get_attachment_image_src( $video_poster, 'full' );
							$poster_src = esc_url( $poster_data[0] );

							?><div class="video-embed-image-overlay" style="background-image: url( <?php echo $poster_src; ?> );">
								<div class="video-play-icon">
									<i class="fa fa-play"></i>
								</div>
							</div><?php
						}

					?></div><?php

					break;
			}

				?></div>
			</div><?php
		}

		/**
		 * [get_done_lesson_button description]
		 * @return [type] [description]
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
		 * [get_embed_html description]
		 * @param  [type] $video_url        [description]
		 * @param  array  $embed_url_params [description]
		 * @param  array  $options          [description]
		 * @param  array  $frame_attributes [description]
		 * @return [type]                   [description]
		 */
		public function get_embed_html( $video_url, array $embed_url_params = [], array $options = [], array $frame_attributes = [] ) {
			$default_frame_attributes = [
				'class'           => 'croco-school-video-iframe',
				'allowfullscreen',
			];

			$video_embed_url = $this->get_embed_url( $video_url, $embed_url_params, $options );

			if ( ! $video_embed_url ) {
				return null;
			}

			$default_frame_attributes['src'] = $video_embed_url;

			$frame_attributes = array_merge( $default_frame_attributes, $frame_attributes );

			$attributes_for_print = [];

			foreach ( $frame_attributes as $attribute_key => $attribute_value ) {
				$attribute_value = esc_attr( $attribute_value );

				if ( is_numeric( $attribute_key ) ) {
					$attributes_for_print[] = $attribute_value;
				} else {
					$attributes_for_print[] = sprintf( '%1$s="%2$s"', $attribute_key, $attribute_value );
				}
			}

			$attributes_for_print = implode( ' ', $attributes_for_print );

			$iframe_html = "<iframe $attributes_for_print></iframe>";

			/** This filter is documented in wp-includes/class-oembed.php */
			return apply_filters( 'oembed_result', $iframe_html, $video_url, $frame_attributes );
		}

		/**
		 * [get_embed_url description]
		 * @param  [type] $video_url        [description]
		 * @param  array  $embed_url_params [description]
		 * @param  array  $options          [description]
		 * @return [type]                   [description]
		 */
		public function get_embed_url( $video_url, array $embed_url_params = [], array $options = [] ) {
			$video_properties = $this->get_video_properties( $video_url );

			if ( ! $video_properties ) {
				return null;
			}

			$embed_patterns = [
				'youtube' => 'https://www.youtube{NO_COOKIE}.com/embed/{VIDEO_ID}?feature=oembed',
				'vimeo'   => 'https://player.vimeo.com/video/{VIDEO_ID}#t={TIME}',
			];

			$embed_pattern = $embed_patterns[ $video_properties['provider'] ];

			$replacements = [
				'{VIDEO_ID}' => $video_properties['video_id'],
			];

			if ( 'youtube' === $video_properties['provider'] ) {
				$replacements['{NO_COOKIE}'] = ! empty( $options['privacy'] ) ? '-nocookie' : '';
			} elseif ( 'vimeo' === $video_properties['provider'] ) {
				$time_text = '';

				if ( ! empty( $options['start'] ) ) {
					$time_text = date( 'H\hi\ms\s', $options['start'] );
				}

				$replacements['{TIME}'] = $time_text;
			}

			$embed_pattern = str_replace( array_keys( $replacements ), $replacements, $embed_pattern );

			return add_query_arg( $embed_url_params, $embed_pattern );
		}

		/**
		 * [get_video_properties description]
		 * @param  [type] $video_url [description]
		 * @return [type]            [description]
		 */
		public function get_video_properties( $video_url ) {

			$provider_match_masks = [
				'youtube' => '/^.*(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/',
				'vimeo'   => '/^.*vimeo\.com\/(?:[a-z]*\/)*([‌​0-9]{6,11})[?]?.*/',
			];

			foreach ( $provider_match_masks as $provider => $match_mask ) {
				preg_match( $match_mask, $video_url, $matches );

				if ( $matches ) {
					return [
						'provider' => $provider,
						'video_id' => $matches[1],
					];
				}
			}

			return null;
		}

		public function render_back_btn_html() {
			$btn_format = '<div class="croco-article-back-btn-wrap"><a href="%1$s" class="croco-article-back-btn">%2$s%3$s</a></div>';

			$top_level_term = $this->get_top_level_category();

			if ( ! $top_level_term ) {
				return false;
			}

			$btn_icon = '';
			$btn_text = '<span class="croco-article-back-btn__text">Back</span>';

			printf(
				$btn_format,
				get_term_link( $top_level_term->term_id, $top_level_term->taxonomy ),
				$btn_icon,
				$btn_text
			);
		}

		public function get_top_level_category() {
			$post_id  = get_the_ID();
			$taxonomy = croco_school()->post_type->category_term_slug();

			$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'orderby' => 'parent' ) );

			if ( ! $terms || is_wp_error( $terms ) ) {
				return false;
			}

			$term = array_pop( $terms );

			$parent_id = $term->parent;

			while ( $parent_id ) {
				$_term     = get_term_by( 'id', $parent_id, $taxonomy );
				$parent_id = $_term->parent;

				if ( $parent_id ) {
					$term = $_term;
				}
			}

			return $term;
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
