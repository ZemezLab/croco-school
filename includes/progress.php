<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Progress' ) ) {

	/**
	 * Define Croco_School_Progress class
	 */
	class Croco_School_Progress {

		/**
		 * [$meta_slug description]
		 * @var string
		 */
		private $meta_slug = 'croco_school_learning_progress';

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * __construct
		 */
		public function __construct() {
			add_action( 'init', [ $this, 'article_progress_done' ] );

			add_action( 'init', [ $this, 'article_set_in_progress' ] );

			add_action( 'init', [ $this, 'article_progress_erase' ] );
		}

		/**
		 * [meta_slug description]
		 * @return [type] [description]
		 */
		public function meta_slug() {
			return $this->meta_slug;
		}

		/**
		 * [reset_popup_rate description]
		 * @return [type] [description]
		 */
		public function article_progress_start() {

			$user_data = wp_get_current_user()->data;
			$user_id = $user_data->ID;
			$article_id = get_the_ID();

			$current = $this->get_user_progress_data();

			if ( ! isset( $current[ $article_id ] ) ) {
				$current[ $article_id ] = [
					'status' => 'in_progress',
				];

				$this->set_user_progress_data( $current );
			}
		}

		/**
		 * [reset_popup_rate description]
		 * @return [type] [description]
		 */
		public function article_progress_done() {

			if ( ! isset( $_GET['action'] ) ) {
				return;
			}

			if ( 'croco_article_progress_done' !== $_GET['action'] ) {
				return;
			}

			if ( ! isset( $_GET['article_id'] ) ) {
				return;
			}

			$article_id = $_GET['article_id'];

			$current = $this->get_user_progress_data();

			$current[ $article_id ] = [
				'status' => 'done',
			];

			$this->set_user_progress_data( $current );
		}

		/**
		 * [article_set_in_progress description]
		 * @return [type] [description]
		 */
		public function article_set_in_progress() {

			if ( ! isset( $_GET['action'] ) ) {
				return;
			}

			if ( 'croco_article_set_in_progress' !== $_GET['action'] ) {
				return;
			}

			if ( ! isset( $_GET['article_id'] ) ) {
				return;
			}

			$article_id = $_GET['article_id'];

			$current = $this->get_user_progress_data();

			$current[ $article_id ] = [
				'status' => 'in_progress',
			];

			$this->set_user_progress_data( $current );
		}

		/**
		 * [get_user_progress_data description]
		 * @return [type] [description]
		 */
		public function get_user_progress_data() {

			$user_data  = wp_get_current_user()->data;
			$user_id    = $user_data->ID;

			$progress = get_user_meta( $user_id, $this->meta_slug(), true );

			if ( empty( $progress ) ) {
				return [];
			}

			return $progress;
		}

		/**
		 * [set_user_progress_data description]
		 * @param [type] $data [description]
		 */
		public function set_user_progress_data( $data ) {
			$user_data  = wp_get_current_user()->data;
			$user_id    = $user_data->ID;

			update_user_meta( $user_id, $this->meta_slug(), $data );
		}

		/**
		 * [get_article_progress description]
		 * @param  [type] $article_id [description]
		 * @return [type]             [description]
		 */
		public function get_article_progress( $article_id ) {

			$progress_data = $this->get_user_progress_data();

			if ( ! isset( $progress_data[ $article_id ] ) ) {
				return 'not_started';
			}

			return $progress_data[ $article_id ]['status'];
		}

		/**
		 * [get_course_progress_data description]
		 * @return [type] [description]
		 */
		public function get_course_progress_data( $course_id ) {

			$progress = [];

			$query = new WP_Query( [
				'post_type' => croco_school()->post_type->article_post_slug(),
				'tax_query' => [
					[
						'taxonomy'	=> croco_school()->post_type->course_term_slug(),
						'field'		=> 'term_id',
						'terms'		=> $course_id,
					]
				]
			] );

			$progress_data = [];

			while ( $query->have_posts() ) : $query->the_post();
				$article_id  = $query->post->ID;

				$progress_data[ $article_id ] = $this->get_article_progress( $article_id );

			endwhile;

			wp_reset_postdata();

			if ( ! in_array( 'not_started', $progress_data ) && ! in_array( 'in_progress', $progress_data ) ) {

				$link = get_term_link( (int)$course_id, croco_school()->post_type->course_term_slug() );

				return [
					'status'           => 'done',
					'article_progress' => $progress_data,
					'data'             => [],
					'link'             => $link,
				];
			}

			if ( ! in_array( 'done', $progress_data ) && ! in_array( 'in_progress', $progress_data ) ) {

				$first_article_id = array_keys( $progress_data )[0];

				$link = get_post_permalink( $first_article_id );

				return [
					'status'           => 'not_started',
					'article_progress' => $progress_data,
					'data'             => [],
					'link'             => $link,
				];
			}

			$count = 0;

			foreach ( $progress_data as $id => $status ) {

				if ( 'done' === $status ) {
					$count ++;
				}

				if ( 'in_progress' === $status || 'not_started' === $status ) {

				}
			}

			$progress_count = [
				'done'  => $count,
				'total' => count( $progress_data ),
			];

			$link = '#';

			$in_progress_id = array_search( 'in_progress', $progress_data );

			if ( $in_progress_id ) {
				$link = get_post_permalink( $in_progress_id );
			} else {
				$not_started_id = array_search( 'not_started', $progress_data );
				$link = get_post_permalink( $not_started_id );
			}

			return [
				'status'           => 'in_progress',
				'article_progress' => $progress_data,
				'data'             => $progress_count,
				'link'             => $link,
			];
		}

		/**
		 * [article_progress_erase description]
		 * @return [type] [description]
		 */
		public function article_progress_erase() {

			if ( ! isset( $_GET['action'] ) ) {
				return;
			}

			if ( 'progress_erase' !== $_GET['action'] ) {
				return;
			}

			$user_data  = wp_get_current_user()->data;
			$user_id    = $user_data->ID;

			update_user_meta( $user_id, $this->meta_slug(), [] );
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
