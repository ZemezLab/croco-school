<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Term_Meta' ) ) {

	/**
	 * Define Croco_School_Term_Meta class
	 */
	class Croco_School_Term_Meta {

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

			new Cherry_X_Term_Meta( [
				'tax'        => 'croco-сourse',
				'builder_cb' => array( $this, 'get_interface_builder' ),
				'fields'     => [
					'container' => array(
						'type'        => 'section',
						'title'       => __( 'Course Settings', 'croco-school' ),
					),
					'course_settings' => array(
						'type'   => 'settings',
						'parent' => 'container',
					),
					'course_thumbnail' => array(
						'type'               => 'media',
						'parent'             => 'course_settings',
						'title'              => esc_html__( 'Thumbnail', 'croco-school' ),
						'multi_upload'       => false,
						'upload_button_text' => esc_html__( 'Set Thumbnail', 'croco-school' ),
					),
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
