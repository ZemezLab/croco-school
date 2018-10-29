<?php
/**
 * Jet Popup post type template
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Croco_School_Post_Type' ) ) {

	/**
	 * Define Croco_School_Post_Type class
	 */
	class Croco_School_Post_Type {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Returns post type slug
		 *
		 * @return string
		 */
		public function article_post_slug() {
			return 'croco-article';
		}

		/**
		 * Returns post type slug
		 *
		 * @return string
		 */
		public function teacher_post_slug() {
			return 'croco-teacher';
		}

		/**
		 * Returns post type slug
		 *
		 * @return string
		 */
		public function course_term_slug() {
			return 'croco-сourse';
		}

		/**
		 * [knowledge_base_term_slug description]
		 * @return [type] [description]
		 */
		public function category_term_slug() {
			return 'croco-article-category';
		}

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			$this->register_post_type();
			$this->register_taxonomy();

			add_action( 'post.php',          array( $this, 'add_post_formats_support' ) );
			add_action( 'load-post.php', array( $this, 'add_post_formats_support' ) );
			add_action( 'load-post-new.php', array( $this, 'add_post_formats_support' ) );

			add_filter( 'template_include', array( $this, 'article_view_template' ) );
		}

		/**
		 * Register post type
		 *
		 * @return void
		 */
		public function register_post_type() {

			$post_types = [

				$this->article_post_slug() => [
					'labels'              => [
						'name'          => esc_html__( 'Croco Article', 'croco-school' ),
						'singular_name' => esc_html__( 'Article', 'croco-school' ),
						'all_items'     => esc_html__( 'All Articles', 'croco-school' ),
						'add_new'       => esc_html__( 'Add New Article', 'croco-school' ),
						'add_new_item'  => esc_html__( 'Add New Article', 'croco-school' ),
						'edit_item'     => esc_html__( 'Edit Article', 'croco-school' ),
						'menu_name'     => esc_html__( 'Croco School', 'croco-school' ),
					],
					'supports'          => apply_filters( 'croco-school/post-type/article/register/supports', [
						'title',
						'editor',
						'author',
						'thumbnail',
						'excerpt',
						'comments',
						'revisions',
						'page-attributes',
						'post-formats',
					] ),
					'public'          => true,
					'capability_type' => 'post',
					'hierarchical'    => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'         => [
						'slug'       => $this->article_post_slug(),
						'with_front' => false,
						'feeds'      => true,
					],
					'query_var'       => true,
					'menu_position'   => 25,
					'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-book' : '',
					'can_export'      => true,
					'has_archive'     => true,
				],

				/*$this->teacher_post_slug() => [
					'labels'              => [
						'name'          => esc_html__( 'Croco Teacher', 'croco-school' ),
						'singular_name' => esc_html__( 'Teacher', 'croco-school' ),
						'all_items'     => esc_html__( 'All Teachers', 'croco-school' ),
						'add_new'       => esc_html__( 'Add New Teacher', 'croco-school' ),
						'add_new_item'  => esc_html__( 'Add New Teacher', 'croco-school' ),
						'edit_item'     => esc_html__( 'Edit Teacher Data', 'croco-school' ),
						'menu_name'     => esc_html__( 'Croco Teacher', 'croco-school' ),
					],
					'hierarchical'        => false,
					'description'         => 'description',
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => 26,
					'menu_icon'           => 'dashicons-welcome-learn-more',
					'show_in_nav_menus'   => false,
					'publicly_queryable'  => true,
					'exclude_from_search' => true,
					'has_archive'         => true,
					'query_var'           => true,
					'can_export'          => true,
					'rewrite'             => true,
					'capability_type'     => 'post',
					'supports'            => apply_filters( 'croco-school/post-type/teacher/register/supports', [
						'title',
						'editor',
						'author',
						'thumbnail',
						'comments',
						'revisions',
					] ),
				],*/
			];

			foreach ( $post_types as $post_slug => $post_data ) {
				register_post_type( $post_slug, $post_data );
			}
		}

		/**
		 * [register_taxonomy description]
		 * @return [type] [description]
		 */
		public function register_taxonomy() {

			$taxonomy_list = [
				$this->course_term_slug() => [
					'labels'        => [
						'name'          => esc_html__( 'Croco Courses', 'croco-school' ),
						'singular_name' => esc_html__( 'Croco Course', 'croco-school' ),
						'label'         => esc_html__( 'Croco Courses', 'croco-school' ),
						'menu_name'     => esc_html__( 'Croco Courses', 'croco-school' ),
						'search_items'  => esc_html__( 'Search Croco Courses', 'croco-school' ),
					],
					'hierarchical'      => true,
					'rewrite'           => true,
					'query_var'         => true,
					'show_ui'           => true,
					'show_admin_column' => true,
					'rewrite'           => [
						'slug' => $this->course_term_slug()
					],
				],

				$this->category_term_slug() => [
					'labels'        => [
						'name'          => esc_html__( 'Croco Arcticle Category', 'croco-school' ),
						'singular_name' => esc_html__( 'Croco Arcticle Category', 'croco-school' ),
						'label'         => esc_html__( 'Croco Arcticle Category', 'croco-school' ),
						'menu_name'     => esc_html__( 'Croco Arcticle Category', 'croco-school' ),
						'search_items'  => esc_html__( 'Search Article Categories', 'croco-school' ),
					],
					'hierarchical'          => true,
					'rewrite'               => true,
					'query_var'             => true,
					'show_ui'               => true,
					'show_admin_column'     => true,
					'rewrite'               => [
						'slug' => $this->category_term_slug()
					],

				],
			];

			foreach ( $taxonomy_list as $term_slug => $term_args ) {
				register_taxonomy( $term_slug, $this->article_post_slug(), $term_args );
			}
		}

		/**
		 * Checks if the template is assigned to the page.
		 *
		 * @since  1.0.0
		 * @param  string $template current template name.
		 * @return string
		 */
		public function article_view_template( $template ) {

			$find        = [];
			$file        = '';

			$archive_template = 'archive-croco-article.php';

			if ( is_single() && $this->article_post_slug() === get_post_type() ) {

				$file   = 'single-croco-article.php';
				$find[] = $file;
				$find[] = croco_school()->template_path() . $file;

			} /*elseif ( is_tax( $this->category_term_slug() ) ) {

				$term = get_queried_object();
				$file = 'archive-croco-article.php';

				$file_term = 'taxonomy-' . $term->taxonomy . '.php';

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = croco_school()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = croco_school()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file_term;
				$find[] = croco_school()->template_path() . $file_term;
				$find[] = $file;
				$find[] = croco_school()->template_path() . $file;

			} elseif ( is_post_type_archive( $this->article_post_slug() ) ) {

				$file   = $archive_template;
				$find[] = $file;
				$find[] = croco_school()->template_path() . $file;

			}*/

			if ( $file ) {
				$template = locate_template( array_unique( $find ) );

				if ( ! $template ) {
					$template = croco_school()->plugin_path( 'templates/' . $file );
				}
			}

			return $template;
		}

		/**
		 * Post formats.
		 *
		 * @since 1.0.0
		 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
		 */
		public function add_post_formats_support() {
			global $typenow;

			if ( $this->article_post_slug() != $typenow ) {
				return;
			}

			$args = [ 'image', 'video' ];

			add_post_type_support( $this->article_post_slug(), 'post-formats', $args );
			add_theme_support( 'post-formats', $args );
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
