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
			return 'article';
		}

		/**
		 * Returns post type slug
		 *
		 * @return string
		 */
		public function course_term_slug() {
			return 'сourse';
		}

		/**
		 * [knowledge_base_term_slug description]
		 * @return [type] [description]
		 */
		public function category_term_slug() {
			return 'article-category';
		}

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			$this->register_post_type();
			$this->register_taxonomy();

			//add_action( 'post.php',          array( $this, 'add_post_formats_support' ) );
			//add_action( 'load-post.php', array( $this, 'add_post_formats_support' ) );
			//add_action( 'load-post-new.php', array( $this, 'add_post_formats_support' ) );

			add_filter( 'template_include', array( $this, 'article_view_template' ) );

			add_action( 'restrict_manage_posts', array( $this, 'admin_filter_by_tax' ) , 10, 2);

			add_filter( 'wp_insert_post_data', array( $this, 'increment_menu_order' ) );
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
						'name'          => esc_html__( 'Articles', 'croco-school' ),
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
                        'custom-fields',
						//'post-formats',
					] ),
					'show_in_rest'    => true,
					'public'          => true,
					'capability_type' => 'post',
					'hierarchical'    => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'         => [
						'slug'       => $this->article_post_slug() . 's',
						'with_front' => false,
						'feeds'      => true,
					],
					'query_var'       => true,
					'menu_position'   => 25,
					'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-book' : '',
					'can_export'      => true,
					'has_archive'     => true,
				],
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
						'name'          => esc_html__( 'Courses', 'croco-school' ),
						'singular_name' => esc_html__( 'Course', 'croco-school' ),
						'label'         => esc_html__( 'Courses', 'croco-school' ),
						'menu_name'     => esc_html__( 'Courses', 'croco-school' ),
						'search_items'  => esc_html__( 'Search Courses', 'croco-school' ),
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
						'name'          => esc_html__( 'Arcticle Category', 'croco-school' ),
						'singular_name' => esc_html__( 'Arcticle Category', 'croco-school' ),
						'label'         => esc_html__( 'Arcticle Category', 'croco-school' ),
						'menu_name'     => esc_html__( 'Arcticle Category', 'croco-school' ),
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

			$archive_template = 'archive-article.php';

			if ( is_single() && $this->article_post_slug() === get_post_type() ) {

				$file   = 'single-article.php';
				$find[] = $file;
				$find[] = croco_school()->template_path() . $file;

			}

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

		public function admin_filter_by_tax( $post_type, $which ) {

			// Apply this only on a specific post type
			if ( $this->article_post_slug() !== $post_type ) {
				return;
			}

			$taxonomy_slug = $this->category_term_slug();

			// Retrieve taxonomy data
			$taxonomy_obj  = get_taxonomy( $taxonomy_slug );
			$taxonomy_name = $taxonomy_obj->labels->name;

			// Retrieve taxonomy terms
			$terms = get_terms( $taxonomy_slug );

			// Display filter HTML
			echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
			echo '<option value="">' . sprintf( esc_html__( 'Show All %s', 'croco-school' ), $taxonomy_name ) . '</option>';
			foreach ( $terms as $term ) {
				printf(
					'<option value="%1$s" %2$s>%3$s (%4$s)</option>',
					$term->slug,
					( ( isset( $_GET[$taxonomy_slug] ) && ( $_GET[$taxonomy_slug] == $term->slug ) ) ? ' selected="selected"' : '' ),
					$term->name,
					$term->count
				);
			}
			echo '</select>';

		}

		public function increment_menu_order( $data ) {

			if ( ! isset( $_POST['post_type'] ) || $_POST['post_type'] !== $this->article_post_slug() ) {
				return $data;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $data;
			}

			if ( $data['post_date'] !== $data['post_modified'] ) { // if update post
				return $data;
			}

			if ( ! empty( $data['menu_order'] ) ) {
				return $data;
			}

			$data['menu_order'] = 100;

			return $data;
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
