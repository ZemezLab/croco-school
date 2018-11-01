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

class Croco_School_Course extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-course';
	}

	public function get_title() {
		return esc_html__( 'Croco Course', 'croco-school' );
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
				'label'        => esc_html__( 'Use as Archive Template', 'croco-school' ),
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
					'default' => $default_course,
					'condition' => [
						//'is_archive_template' => 'no',
					],
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

		if ( ! $is_archive_template ) {
			$course_id = $settings['course_id'];
		} else {

			if ( isset( get_queried_object()->term_id )) {
				$course_id = get_queried_object()->term_id;
			}else {
				$course_id = $settings['course_id'];
			}
		}

		$term_data = get_term( $course_id, croco_school()->post_type->course_term_slug() );

		$course_id = $term_data->term_id;
		$course_name = $term_data->name;
		$course_description = $term_data->description;
		$course_article_count = $term_data->count;
		$course_thumbnail_id = get_term_meta( $course_id, 'course_thumbnail', true );

		$this->add_render_attribute( 'container', [
			'class' => [
				'croco-school-course',
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<div class="croco-school-course__inner"><?php

				echo sprintf( '<h3 class="croco-school-course__name">%s</h3>', $course_name );

				if ( $course_article_count > 0 ) {
					echo sprintf( '<span class="croco-school-course__lessons"><span>%s</span>%s</span>', $course_article_count, ( $course_article_count == 1 ) ? __( 'Lesson', 'croco-school' ) : __( 'Lessons', 'croco-school' ) );
				}?>

				<div class="croco-school-course__thumb"><?php

					if ( ! empty( $course_thumbnail_id ) ) {
						echo wp_get_attachment_image( $course_thumbnail_id, 'full' );
					} else {
						echo sprintf( '<img src="%s" alt="">', croco_school()->plugin_url( '/assets/images/course-empty-thumbnail.png') );
					}
				?></div><?php

				echo sprintf( '<p class="croco-school-course__desc">%s</p>', $course_description );

				$this->get_course_progress( $course_id );

				$this->get_course_progress_link( $course_id );?>

			</div>
		</div><?php
	}

	/**
	 * [get_course_progress description]
	 * @param  [type] $course_id [description]
	 * @return [type]            [description]
	 */
	public function get_course_progress( $course_id ) {
		$progress = croco_school()->progress->get_course_progress_data( $course_id );

		?><div class="croco-school-course__progress <?php echo $progress['status']; ?>-status">
			<div class="croco-school-course__progress-label"><?php echo esc_html__( 'Progress:', 'croco-school' ); ?></div>
			<div class="croco-school-course__progress-status"><?php

				switch ( $progress['status'] ) {
					case 'in_progress':
						$progress_data = $progress['data'];
						$done = $progress_data['done'];
						$total = $progress_data['total'];

						$percent = $done * 100 / $total;

						echo sprintf( '<div class="progress-bar">
							<div class="bar"><span style="width: %1$s%%"></span></div><div class="progress-count">%2$s/%3$s</div>
							</div>',
							$percent,
							$done,
							$total
						);
						break;

					case 'done':
						echo sprintf( '<div class="complete-message">%s</div>', __( 'Completed', 'croco-school' ) );
						break;

					case 'not_started':
						echo sprintf( '<div class="not-started-message">%s</div>', __( 'Not Started', 'croco-school' ) );
						break;

					case 'guest':
						echo sprintf( '<div class="not-started-message">%s</div>', __( 'Not Available For Guests', 'croco-school' ) );
						break;
				}
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * [get_course_progress_link description]
	 * @param  [type] $course_id [description]
	 * @return [type]            [description]
	 */
	public function get_course_progress_link( $course_id ) {
		$term_link_text = esc_html__( 'Start', 'croco-school' );

		$progress = croco_school()->progress->get_course_progress_data( $course_id );

		$term_link = $progress['link'];

		switch ( $progress['status'] ) {
			case 'in_progress':
				$term_link_text = esc_html__( 'Continue', 'croco-school' );
				break;

			case 'done':
				$term_link_text = esc_html__( 'Learn More', 'croco-school' );
				break;

			case 'not_started':
				$term_link_text = esc_html__( 'Start', 'croco-school' );
				$term_link = get_term_link( (int)$course_id, croco_school()->post_type->course_term_slug() );
				break;

			case 'guest':
				$term_link_text = esc_html__( 'View', 'croco-school' );
				break;
		}

		echo sprintf( '<a class="croco-school-course__progress-link" href="%s"><span>%s</span></a>', $term_link, $term_link_text );
	}

}

