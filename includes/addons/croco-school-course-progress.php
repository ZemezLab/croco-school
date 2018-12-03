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

class Croco_School_Course_Progress extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-course-progress';
	}

	public function get_title() {
		return esc_html__( 'Croco Course Progress', 'croco-school' );
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
			'title',
			array(
				'label'   => esc_html__( 'Title', 'croco-school' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Course Lessons', 'croco-school' ),
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

		$avaliable_courses = \Croco_School_Utils::avaliable_courses();

		if ( ! $avaliable_courses ) {

			$this->add_control(
				'no_courses',
				array(
					'label' => false,
					'type'  => Controls_Manager::RAW_HTML,
					'raw'   => '<p>Courses not founded</p>',
				)
			);

		} else {
			$default_course = array_keys( $avaliable_courses )[0];

			$this->add_control(
				'course_id',
				[
					'label'   => esc_html__( 'Croco Course', 'croco-school' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $avaliable_courses,
					'default' => $default_course
				]
			);
		}

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

		$course_id = $settings['course_id'];

		if ( $is_archive_template && isset( get_queried_object()->ID ) ) {
			$article_id = get_queried_object()->ID;

			$term_list = get_the_terms( $article_id, croco_school()->post_type->course_term_slug() );

			if ( ! empty( $term_list ) ) {
				$course_id = $term_list[0]->term_id;
			}
		}

		$term_data = get_term( $course_id, croco_school()->post_type->course_term_slug() );

		$course_id = $term_data->term_id;

		$this->add_render_attribute( 'container', [
			'class' => [
				'croco-school-course-progress',
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'container' ); ?>><?php

			if ( ! empty( $settings['title'] ) ) {
				echo sprintf( '<h2 class="croco-school-course-progress__title">%s</h2>', $settings['title'] );
			}

			?><div class="croco-school-course-progress__inner"><?php

				$query = new \WP_Query( [
						'post_type' => croco_school()->post_type->article_post_slug(),
						'tax_query' => [
							[
								'taxonomy' => croco_school()->post_type->course_term_slug(),
								'field'    => 'term_id',
								'terms'    => $course_id,
							]
						]
					] );

					$count = 1;

	  while ( $query->have_posts() ) : $query->the_post();
						$post_id  = $query->post->ID;

						$title = get_the_title( $post_id );
						$permalink = get_the_permalink( $post_id );

						$status = croco_school()->progress->get_article_progress( $post_id );

						$current_post = $article_id === $post_id ? true : false;

			  switch ( $status ) {

							case 'in_progress':
								$progress_text = __( 'In Progress', 'croco-school' );
								$progress_icon = '<i class="nc-icon-glyph arrows-1_bold-right"></i>';
								break;

							case 'done':
								$progress_text = __( 'Completed', 'croco-school' );
								$progress_icon = '<i class="nc-icon-glyph ui-1_check-bold"></i>';
								break;

							case 'not_started':
								$progress_text = __( 'Not Viewed', 'croco-school' );
								$progress_icon = '<i class="nc-icon-glyph arrows-1_bold-right"></i>';
								break;

							case 'guest':
								$progress_text = false;
								$progress_icon = '<i class="nc-icon-glyph education_book-open"></i>';
								break;
						}

						if ( $current_post ){
							$progress_icon = '<i class="nc-icon-glyph ui-1_eye-19"></i>';
						}

						$current_class = $current_post ? 'current' : '';

						?><div id="croco-school-course-progress-<?php the_ID(); ?>" class="croco-school-course-progress__item <?php echo $status; ?>-status <?php echo $current_class ?>">
							<div class="croco-school-course-progress__item-icon"><?php
								echo $progress_icon;
							?></div>
							<div class="croco-school-course-progress__item-inner"><?php
								echo sprintf( '<h3 class="croco-school-course-progress__item-title"><a href="%s">%s</a></h3>', $permalink, $title );

								if ( $progress_text ) {
									echo sprintf( '<span class="croco-school-course-progress__item-progress">%s</span>', $progress_text );
								}?>
							</div>
						</div><?php

						$count++;

					endwhile;

					wp_reset_postdata();?>

			</div>
		</div><?php
	}

}

