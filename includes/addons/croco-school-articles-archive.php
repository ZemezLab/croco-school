<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Croco_School_Articles_Archive extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-articles-archive';
	}

	public function get_title() {
		return esc_html__( 'Croco Articles Archive', 'croco-school' );
	}

	public function get_icon() {
		return 'eicon-wordpress';
	}

	public function get_categories() {
		return array( 'croco-school' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'Settings', 'croco-school' ),
			)
		);

		$this->add_control(
			'desc',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => 'Use on archive page',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render
	 *
	 * @return void|bool
	 */
	protected function render() {

		$this->__context = 'render';

		$settings = $this->get_settings();

		$term_id = isset( get_queried_object()->term_id ) ? get_queried_object()->term_id : false;

		if ( empty( $term_id ) ) {
			echo '<h2>Articles not found</h2>';
			return false;
		}
		?>

		<div class="cs-articles-archive">
			<?php
			$term_data = get_term( $term_id );

			printf( '<h2 class="cs-articles-archive__main-title">%s</h2>', $term_data->name );

			$this->generate_article_list( $term_id );

			$term_childs = get_term_children( $term_data->term_id, croco_school()->post_type->category_term_slug() );

			if ( ! empty( $term_childs ) ) { ?>

				<div class="cs-articles-archive__child-terms-list"><?php

					foreach ( $term_childs as $child ) {
						$child_term = get_term_by( 'id', $child, croco_school()->post_type->category_term_slug() );?>

						<div class="cs-articles-archive__child-term-item"><?php
							$this->generate_article_list( $child_term->term_id, true );?>
						</div><?php
					} ?>

				</div>
			<?php } ?>
		</div>

		<?php

	}

	/**
	 * Generate article list
	 *
	 * @param string $term_id
	 * @param bool   $term_name_visible
	 *
	 * @return bool
	 */
	public function generate_article_list( $term_id = '', $term_name_visible = false ) {

		if ( empty( $term_id ) ) {
			return false;
		}

		$term_data = get_term( $term_id );

		$query_param = array(
			'post_type'      => croco_school()->post_type->article_post_slug(),
			'posts_per_page' => - 1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'tax_query'      => array(
				array(
					'taxonomy'         => croco_school()->post_type->category_term_slug(),
					'field'            => 'term_id',
					'terms'            => $term_id,
					'include_children' => false,
				)
			),
		);

		$query = new \WP_Query( $query_param );

		if ( empty( $query->posts ) ) {
			return false;
		}

		if ( $term_name_visible ) { ?>
			<h3 class="cs-articles-archive__child-term-title"><?php echo $term_data->name; ?></h3>
		<?php } ?>

		<ul class="cs-articles-archive__list"><?php

			while ( $query->have_posts() ) : $query->the_post();

				$post_id = $query->post->ID;
				$format  = get_post_meta( $post_id, 'article_format', true );
				?>

				<li id="cs-article-<?php echo $post_id; ?>" class="cs-articles-archive__article-item">
					<a class="cs-articles-archive__article-link" href="<?php the_permalink(); ?>">
						<span class="cs-article-link-text"><?php
							the_title();

							if ( 'video' === $format ) { ?>
								<span class="video-format-icon">
									<svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M19.8047 3.01389C19.8047 3.01389 19.7461 2.76111 19.6289 2.25556C19.5117 1.75 19.3034 1.34815 19.0039 1.05C18.6263 0.648148 18.2422 0.414815 17.8516 0.35C17.474 0.272222 17.1875 0.22037 16.9922 0.194444C16.3021 0.142593 15.5469 0.103704 14.7266 0.0777778C13.9062 0.0518519 13.1445 0.0324074 12.4414 0.0194444C11.7513 0.00648148 11.1719 0 10.7031 0C10.2344 0 10 0 10 0C10 0 9.76562 0 9.29688 0C8.82812 0 8.24219 0.00648148 7.53906 0.0194444C6.84896 0.0324074 6.09375 0.0518519 5.27344 0.0777778C4.45312 0.103704 3.69792 0.142593 3.00781 0.194444C2.8125 0.22037 2.51953 0.272222 2.12891 0.35C1.7513 0.414815 1.3737 0.648148 0.996094 1.05C0.696615 1.34815 0.488281 1.75 0.371094 2.25556C0.253906 2.76111 0.195312 3.01389 0.195312 3.01389C0.195312 3.01389 0.16276 3.35093 0.0976562 4.025C0.0325521 4.69907 0 5.43796 0 6.24167V7.75833C0 8.56204 0.0325521 9.30093 0.0976562 9.975C0.16276 10.6361 0.195312 10.9667 0.195312 10.9667C0.195312 10.9667 0.253906 11.2259 0.371094 11.7444C0.488281 12.25 0.696615 12.6519 0.996094 12.95C1.3737 13.3519 1.77734 13.5852 2.20703 13.65C2.64974 13.7148 2.98177 13.7667 3.20312 13.8056C3.60677 13.8444 4.1862 13.8769 4.94141 13.9028C5.69661 13.9287 6.44531 13.9481 7.1875 13.9611C7.94271 13.9741 8.60026 13.987 9.16016 14C9.72005 14 10 14 10 14C10 14 10.2344 14 10.7031 14C11.1719 14 11.7513 13.9935 12.4414 13.9806C13.1445 13.9676 13.9062 13.9481 14.7266 13.9222C15.5469 13.8833 16.3021 13.838 16.9922 13.7861C17.1875 13.7731 17.474 13.7343 17.8516 13.6694C18.2422 13.5917 18.6263 13.3519 19.0039 12.95C19.3034 12.6519 19.5117 12.25 19.6289 11.7444C19.7461 11.2259 19.8047 10.9667 19.8047 10.9667C19.8047 10.9667 19.8372 10.6361 19.9023 9.975C19.9674 9.30093 20 8.56204 20 7.75833V6.24167C20 5.43796 19.9674 4.69907 19.9023 4.025C19.8372 3.35093 19.8047 3.01389 19.8047 3.01389ZM7.92969 9.58611V3.98611L13.3398 6.78611L7.92969 9.58611Z" fill="#FF0000" />
									</svg>
								</span>
							<?php } ?>
						</span>

						<span class="read-now">
							<span class="read-now__text">Read now</span>
							<i class="read-now__icon nc-icon-glyph arrows-1_bold-right"></i>
						</span>
					</a>
				</li><?php

			endwhile;

			wp_reset_postdata();?>
		</ul><?php
	}

}

