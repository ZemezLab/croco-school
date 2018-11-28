<?php
/**
 * Plugin Name: Croco School
 * Plugin URI:  http://crocoblock.com
 * Description: The advanced plugin for creating popups with Elementor
 * Version:     1.0.2
 * Author:      Croco
 * Author URI:  http://crocoblock.com
 * Text Domain: croco-school
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * @package croco-school
 * @author  Zemez
 * @version 1.0.0
 * @license GPL-2.0+
 * @copyright  2018, Zemez
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Croco_School` doesn't exists yet.
if ( ! class_exists( 'Croco_School' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Croco_School {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '1.0.0';

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * [$slug description]
		 * @var string
		 */
		public $plugin_slug = 'croco-school';

		/**
		 * Framework component
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $framework = null;

		/**
		 * [$assets description]
		 * @var [type]
		 */
		public $assets = null;

		/**
		 * [$post_type description]
		 * @var [type]
		 */
		public $post_type = null;

		/**
		 * [$term_meta description]
		 * @var null
		 */
		public $term_meta = null;

		/**
		 * [$article_data description]
		 * @var null
		 */
		public $article_data = null;

		/**
		 * [$progress description]
		 * @var null
		 */
		public $progress = null;

		/**
		 * [$widgets description]
		 * @var null
		 */
		public $widgets = null;

		/**
		 * [$integration description]
		 * @var null
		 */
		public $integration = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load framework
			add_action( 'after_setup_theme', array( $this, 'framework_loader' ), -20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );

			// Load files.
			add_action( 'init', array( $this, 'init' ), -999 );

			// Register activation  hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			// Register deactivation hook.
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * [get_plugin_slug description]
		 * @return [type] [description]
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}

		/**
		 * Load framework modules
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public function framework_loader() {
			require $this->plugin_path( 'framework/loader.php' );

			$this->framework = new Croco_School_CX_Loader(
				[
					$this->plugin_path( 'framework/interface-builder/cherry-x-interface-builder.php' ),
					$this->plugin_path( 'framework/post-meta/cherry-x-post-meta.php' ),
					$this->plugin_path( 'framework/term-meta/cherry-x-term-meta.php' ),
				]
			);
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init() {

			$this->load_files();

			$this->assets = new Croco_School_Assets();

			$this->post_type = new Croco_School_Post_Type();

			$this->integration = new Croco_School_Integration();

			$this->widgets = new Croco_School_Widgets();

			$this->progress = new Croco_School_Progress();

			$this->article_data = new Croco_School_Article_Data();

			if ( is_admin() ) {
				$this->term_meta = new Croco_School_Term_Meta();
			}

		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/assets.php' );
			require $this->plugin_path( 'includes/integration.php' );
			require $this->plugin_path( 'includes/post-type.php' );
			require $this->plugin_path( 'includes/utils.php' );
			require $this->plugin_path( 'includes/widgets.php' );
			require $this->plugin_path( 'includes/progress.php' );
			require $this->plugin_path( 'includes/term-meta.php' );
			require $this->plugin_path( 'includes/article-data.php' );

			if ( class_exists( 'Elementor\Plugin' ) ) {
				require_once $this->plugin_path( 'includes/widgets/class-elementor-template-widget.php' );
			}
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}
		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'croco-school', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'croco-school/template-path', 'croco-school/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {

			flush_rewrite_rules();
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
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

if ( ! function_exists( 'croco_school' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function croco_school() {
		return Croco_School::get_instance();
	}
}

croco_school();
