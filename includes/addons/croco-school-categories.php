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

			$this->add_control(
				'additional_sub_terms_ids',
				array(
					'label'       => esc_html__( 'Additional Sub Categories', 'croco-school' ),
					'label_block' => true,
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'options'     => $available_category,
					'default'     => array(),
				)
			);
		}

		$this->add_control(
			'is_archive_template',
			array(
				'label'   => esc_html__( 'Is Archive Template?', 'croco-school' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'   => esc_html__( 'Show Title', 'croco-school' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

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
				'condition' => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_type',
			array(
				'label'   => esc_html__( 'Title Text', 'croco-school' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'croco-school' ),
					'custom'  => esc_html__( 'Custom', 'croco-school' ),
				),
				'condition' => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_title',
			array(
				'label'   => esc_html__( 'Custom Title Text', 'croco-school' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'condition' => array(
					'show_title' => 'yes',
					'title_type' => 'custom',
				),
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

		$term_data  = get_term( $term_id );
		$show_title = filter_var( $settings['show_title'], FILTER_VALIDATE_BOOLEAN );
		$title_tag  = $settings['title_tag'];
		$title_type = $settings['title_type'];
		$term_slug  = croco_school()->post_type->category_term_slug();

		$is_archive = filter_var( $settings['is_archive_template'], FILTER_VALIDATE_BOOLEAN );
		$archive_id = null;

		if ( $is_archive && is_archive() && isset( get_queried_object()->term_id ) ) {
			$archive_id = get_queried_object()->term_id;
		}
		
		$mod = $is_archive ? 'archive' : 'single';

		?>
		<div class="croco-school-cats croco-school-cats--<?php echo $mod; ?>">
			<?php

			if ( $show_title ) {
				printf( '<%1$s class="croco-school-cats__title">%2$s</%1$s>',
					$title_tag,
					( 'default' === $title_type ) ? $term_data->name : $settings['custom_title']
				);
			}

			$terms_child = get_terms(
				array(
					'parent'     => $term_id,
					'taxonomy'   => $term_slug,
					'hide_empty' => 1,
				)
			);

			if ( ! empty( $settings['additional_sub_terms_ids'] ) ) {
				$additional_sub_terms = get_terms(
					array(
						'include'    => $settings['additional_sub_terms_ids'],
						'taxonomy'   => $term_slug,
						'hide_empty' => 1,
					)
				);

				$terms_child = array_merge( $terms_child, $additional_sub_terms );
			}

			if ( ! empty( $terms_child ) ) { ?>
				<ul class="croco-school-cats__child-list">

					<?php
					foreach ( $terms_child as $term_child ) {

						$current_class = ( $archive_id === $term_child->term_id ) ? ' current-category' : '';

						printf(
							'<li class="croco-school-cats__child-item croco-category-%1$s"><a href="%2$s" class="croco-school-cats__child-link%4$s">%3$s</a></li>',
							$term_child->term_id,
							esc_url( get_term_link( $term_child->term_id, $term_slug ) ),
							$term_child->name,
							$current_class
						);

					} ?>

				</ul>
			<?php } ?>
		</div>

		<?php
	}

}

