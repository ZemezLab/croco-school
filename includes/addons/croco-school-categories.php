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

class Croco_School_Categories extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-categories';
	}

	public function get_title() {
		return esc_html__( 'Croco Article Categories', 'croco-school' );
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

		$available_category = \Croco_School_Utils::avaliable_article_category();

		if ( ! $available_category ) {

			$this->add_control(
				'no_category',
				array(
					'label' => false,
					'type'  => Controls_Manager::RAW_HTML,
					'raw'   => '<p>Article categories not founded</p>',
				)
			);

		} else {
			$default_category = array_keys( $available_category )[0];

			$this->add_control(
				'term_id',
				array(
					'label'   => esc_html__( 'Article Categories', 'croco-school' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $available_category,
					'default' => $default_category,
				)
			);
		}

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'Title Tag', 'croco-school' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'DIV',
				),
				'default' => 'h3',
			)
		);

		$this->add_responsive_control(
			'child_terms_columns',
			array(
				'label'     => esc_html__( 'Child Terms Columns', 'croco-school' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .croco-school-cats__child-item' => 'flex: 0 0 calc( 100%/{{VALUE}} ); max-width: calc( 100%/{{VALUE}} );',
				)
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
		$term_id  = $settings['term_id'];

		if ( empty( $term_id ) ) {
			echo '<h2>Article categories not found</h2>';
			return false;
		}

		$term_data = get_term( $term_id );
		$title_tag = $settings['title_tag'];
		$term_slug = croco_school()->post_type->category_term_slug();

		$archive_id = null;

		if ( is_archive() && isset( get_queried_object()->term_id ) ) {
			$archive_id = get_queried_object()->term_id;
		}
		
		$mod = is_archive() ? 'archive' : 'single';

		?>
		<div class="croco-school-cats croco-school-cats--<?php echo $mod; ?>">
			<?php
			printf( '<%1$s class="croco-school-cats__title">%2$s</%1$s>',
				$title_tag,
				$term_data->name
			);

			$term_child = get_term_children( $term_id, $term_slug );
			
			if ( ! empty( $term_child ) ) { ?>
				<ul class="croco-school-cats__child-list">

					<?php
					foreach ( $term_child as $term_child_id ) {

						$child_term_data = get_term( $term_child_id );
						$current_class   = ( $archive_id === $term_child_id ) ? 'current-category' : '';

						printf(
							'<li class="croco-school-cats__child-item croco-school-cats__child-item-%1$s%4$s"><a href="%2$s" class="croco-school-cats__child-link">%3$s</a></li>',
							$term_child_id,
							esc_url( get_term_link( $term_child_id, $term_slug ) ),
							$child_term_data->name,
							$current_class
						);

					} ?>

				</ul>
			<?php } ?>
		</div>

		<?php
	}

}

