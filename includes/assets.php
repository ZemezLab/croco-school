<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Assets' ) ) {

	/**
	 * Define Croco_School_Assets class
	 */
	class Croco_School_Assets {

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
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 10 );

		}

		/**
		 * Enqueue public-facing stylesheets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function register_scripts() {

			wp_register_script(
				'croco-school-sticky-sidebar',
				croco_school()->plugin_url( 'assets/js/sticky-sidebar.min.js' ),
				[],
				'1.1.3',
				true
			);

			wp_register_script(
				'croco-school-tippy',
				'https://unpkg.com/tippy.js@3/dist/tippy.all.min.js',
				[],
				'3.1.1',
				true
			);
		}

		/**
		 * [enqueue_scripts description]
		 * @return [type] [description]
		 */
		public function enqueue_scripts() {

			//$screen = get_current_screen();

			wp_enqueue_style(
				'croco-school-frontend',
				croco_school()->plugin_url( 'assets/css/croco-school-frontend.css' ),
				false,
				croco_school()->get_version()
			);

			wp_enqueue_script(
				'croco-school-frontend',
				croco_school()->plugin_url( 'assets/js/croco-school-frontend.js' ),
				[ 'jquery', 'croco-school-sticky-sidebar', 'croco-school-tippy' ],
				croco_school()->get_version(),
				true
			);

			$this->localize_data['ajax_url'] = esc_url( admin_url( 'admin-ajax.php' ) );

			wp_localize_script(
				'croco-school-frontend',
				'CrocoSchool',
				$this->localize_data
			);

		}

		/**
		 * Enqueue admin styles
		 *
		 * @return void
		 */
		public function enqueue_admin_assets() {

		}

		/**
		 * [suffix description]
		 * @return [type] [description]
		 */
		public function suffix() {
			return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
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
