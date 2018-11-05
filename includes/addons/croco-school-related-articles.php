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

class Croco_School_Related_Articles extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-related-articles';
	}

	public function get_title() {
		return esc_html__( 'Croco Related Articles', 'croco-school' );
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
			'is_archive_template',
			[
				'label'        => esc_html__( 'Use as Single Article Widget', 'croco-school' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'croco-school' ),
				'label_off'    => esc_html__( 'No', 'croco-school' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$avaliable_category = \Croco_School_Utils::avaliable_article_category();

		if ( ! $avaliable_category ) {

			$this->add_control(
				'no_category',
				array(
					'label' => false,
					'type'  => Controls_Manager::RAW_HTML,
					'raw'   => '<p>Article categories not founded</p>',
				)
			);

		} else {
			$default_category = array_keys( $avaliable_category )[0];

			$this->add_control(
				'term_id',
				[
					'label'   => esc_html__( 'Article Category', 'croco-school' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $avaliable_category,
					'default' => $default_category,
				]
			);
		}

		$this->add_control(
			'use_article_limit',
			array(
				'label'        => esc_html__( 'Use Article limit', 'croco-school' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'article_limit',
			array(
				'label'   => esc_html__( 'Article List Limit', 'croco-school' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 0,
				'max'     => 20,
				'step'    => 1,
				'condition' => [
					'use_article_limit' => 'yes',
				],
			)
		);

		$this->end_controls_section();
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	protected function render() {

		$this->__context = 'render';

		$settings = $this->get_settings();

		$is_archive_template = filter_var( $settings['is_archive_template'], FILTER_VALIDATE_BOOLEAN );

		$term_id = $settings['term_id'];

		if ( $is_archive_template && isset( get_queried_object()->ID ) ) {
			$article_id = get_queried_object()->ID;

			$term_list = get_the_terms( $article_id, croco_school()->post_type->category_term_slug() );

			if ( ! empty( $term_list ) ) {
				$term_id = $term_list[0]->term_id;
			}
		}

		$this->add_render_attribute( 'container', [
			'class' => [
				'croco-school-related-articles',
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<div class="croco-school-related-articles__inner"><?php
				$this->generate_article_list( $term_id );
			?></div>
		</div><?php
	}

	/**
	 * [generate_article_list description]
	 * @param  string $term_id [description]
	 * @return [type]          [description]
	 */
	public function generate_article_list( $term_id = '' ) {

		if ( empty( $term_id ) ) {
			return false;
		}

		$settings = $this->get_settings();
		$is_archive_template = filter_var( $settings['is_archive_template'], FILTER_VALIDATE_BOOLEAN );
		$use_article_limit = filter_var( $settings['use_article_limit'], FILTER_VALIDATE_BOOLEAN );
		$article_list_limit = $settings['article_limit'];

		$query = new \WP_Query( [
			'post_type' => croco_school()->post_type->article_post_slug(),
			'tax_query' => [
				[
					'taxonomy'	=> croco_school()->post_type->category_term_slug(),
					'field'		=> 'term_id',
					'terms'		=> $term_id,
				]
			]
		] );

		if( ! empty( $query->posts ) ) {
			?><div class="croco-school-related-articles__list">

				<ul><?php

					$count = 0;

					while ( $query->have_posts() ) : $query->the_post();
						$post_id = $query->post->ID;

						$is_course_article = croco_school()->progress->is_course_article( $post_id );

						if ( $use_article_limit && $count > $article_list_limit - 1 ) {
							continue;
						}?>

						<li class="croco-school-related-articles__item" id="croco-article-<?php the_ID(); ?>">
							<a class="croco-school-related-articles__link" href="<?php the_permalink(); ?>"><?php
								the_title();

								if ( $is_course_article ) {
									echo sprintf( '<i class="nc-icon-glyph education_hat" data-tippy="%s" data-tippy-theme="light-border" data-tippy-arrow="true"></i>', esc_html__( 'This article is presented as a lesson from the course.', 'croco-school' ) );
								}
							?></a>
						</li><?php

						$count++;

					endwhile;

					wp_reset_postdata();?>
				</ul>
			</div><?php
		}
	}

}

