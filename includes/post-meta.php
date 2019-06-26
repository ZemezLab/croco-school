<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Post_Meta' ) ) {

	/**
	 * Define Croco_School_Post_Meta class
	 */
	class Croco_School_Post_Meta {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 *
		 */
		public function __construct() {

			add_action( 'init', [ $this, 'register_meta_boxes' ] );
		}

		/**
		 * [register_meta_boxes description]
		 * @return [type] [description]
		 */
		public function register_meta_boxes() {

			new Cherry_X_Post_Meta( [
				'id'            => 'article-settings',
				'title'         => esc_html__( 'Article Settings', 'croco-school' ),
				'page'          => [ croco_school()->post_type->article_post_slug() ],
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => [ $this, 'get_interface_builder' ],
				'fields'        => [

					'main_settings' => [
						'type'   => 'settings',
					],

					'article_format'  => [
						'type'        => 'radio',
						'parent'      => 'main_settings',
						'title'       => esc_html__( 'Article Format', 'croco-school' ),
						'description' => esc_html__( 'Select Article Format', 'croco-school' ),
						'value'       => 'standard',
						'options'     => [
							'standard' => [
								'label' => esc_html__( 'Standard', 'croco-school' ),
							],
							'image' => [
								'label' => esc_html__( 'Image', 'croco-school' ),
							],
							'video' => [
								'label' => esc_html__( 'Video', 'croco-school' ),
							],
						],
					],

					'vertical_tabs' => [
						'type'   => 'component-tab-vertical',
						'parent' => 'main_settings',
					],

					'image_tab' => [
						'type'   => 'settings',
						'parent'      => 'vertical_tabs',
						'title'       => esc_html__( 'Image Settings', 'croco-school' ),
					],

					'video_tab' => [
						'type'   => 'settings',
						'parent'      => 'vertical_tabs',
						'title'       => esc_html__( 'Video Settings', 'croco-school' ),
					],

					'article_image' => [
						'type'               => 'media',
						'parent'             => 'image_tab',
						'title'              => esc_html__( 'Article Image', 'croco-school' ),
						'multi_upload'       => false,
						'upload_button_text' => esc_html__( 'Set Image', 'croco-school' ),
					],

					'article_image_size' => [
						'type'        => 'select',
						'parent'             => 'image_tab',
						'value'       => 'full',
						'options'     => Croco_School_Utils::get_image_sizes(),
						'title'       => esc_html__( 'Image Size', 'croco-school' ),
					],

					'video_url' => array(
						'type'         => 'text',
						'parent'       => 'video_tab',
						'value'        => '',
						'title'        => esc_html__( 'Video Url', 'jet-elements' ),
					),

					'video_aspect_ratio'  => [
						'type'        => 'radio',
						'parent'      => 'video_tab',
						'title'       => esc_html__( 'Aspect Ratio', 'croco-school' ),
						'value'       => '169',
						'options'     => [
							'169' => [
								'label' => esc_html__( '16:9', 'croco-school' ),
							],
							'219' => [
								'label' => esc_html__( '21:9', 'croco-school' ),
							],
							'43' => [
								'label' => esc_html__( '4:3', 'croco-school' ),
							],
							'32' => [
								'label' => esc_html__( '3:2', 'croco-school' ),
							],
						],
					],

					'video_poster' => [
						'type'               => 'media',
						'parent'             => 'video_tab',
						'title'              => esc_html__( 'Video Poster', 'croco-school' ),
						'multi_upload'       => false,
						'upload_button_text' => esc_html__( 'Set Image', 'croco-school' ),
					],

				],
			] );

		}

		/**
		 * [kava_extra_get_interface_builder description]
		 *
		 * @return [type] [description]
		 */
		public function get_interface_builder() {

			$builder_data = croco_school()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			return new CX_Interface_Builder(
				array(
					'path' => $builder_data['path'],
					'url'  => $builder_data['url'],
				)
			);
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
