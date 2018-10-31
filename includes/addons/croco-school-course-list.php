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

class Croco_School_Course_List extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-course-list';
	}

	public function get_title() {
		return esc_html__( 'Croco Course List', 'croco-school' );
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

		$this->end_controls_section();
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	protected function render() {

		$this->__context = 'render';

		$settings = $this->get_settings();

		$this->add_render_attribute( 'container', [
			'class' => [
				'croco-school-course-list',
			],
		] );

		$course_terms = get_terms( [
			'taxonomy'   => croco_school()->post_type->course_term_slug(),
			'hide_empty' => false,
		] );

		if ( empty( $course_terms ) ) {
			echo esc_html__( 'Any Course Not Found', 'croco-school' );

			return;
		}

		?><div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<div class="croco-school-course-list__inner">
				<div class="croco-school-course-list__grid"><?php

					foreach( $course_terms as $term_data ) {?>
						<div class="croco-school-course-list__item">
							<div class="croco-school-course-list__item-inner"><?php
								$this->generate_list_item( $term_data );?>
							</div>
						</div><?php
					}?>
				</div>
			</div>
		</div><?php
	}


	public function generate_list_item( $term_data ) {
		$course_id = $term_data->term_id;
		$course_name = $term_data->name;
		$course_link = get_term_link( (int)$course_id, croco_school()->post_type->course_term_slug() );
		$course_description = $term_data->description;
		$course_description = \Croco_School_Utils::cut_text( $course_description, 15, 'word', '...', true );
		$course_article_count = $term_data->count;
		$course_thumbnail_id = get_term_meta( $course_id, 'course_thumbnail', true );

		if ( $course_article_count > 0) {
			echo sprintf( '<span class="croco-school-course-list__lessons"><span>%s</span>%s</span>', $course_article_count, ( $course_article_count == 1 ) ? __( 'Lesson', 'croco-school' ) : __( 'Lessons', 'croco-school' ) );
		}?>

		<div class="croco-school-course-list__item-thumb"><?php

			if ( ! empty( $course_thumbnail_id ) ) {
				echo wp_get_attachment_image( $course_thumbnail_id, 'full' );
			} else {
				echo sprintf( '<img src="%s" alt="">', croco_school()->plugin_url( '/assets/images/course-empty-thumbnail.png') );
			}
		?></div><?php

		?><div class="croco-school-course-list__item-content"><?php

			echo sprintf( '<h3 class="croco-school-course-list__item-name"><a href="%1$s">%2$s</a></h3>', $course_link, $course_name );

			echo sprintf( '<p class="croco-school-course-list__item-desc">%s</p>', $course_description );

			$this->get_course_progress( $course_id );

			$this->get_course_progress_link( $course_id );?>
		</div><?php
	}

	/**
	 * [get_course_progress description]
	 * @param  [type] $course_id [description]
	 * @return [type]            [description]
	 */
	public function get_course_progress( $course_id ) {?>
		<div class="croco-school-course-list__progress">
			<div class="croco-school-course-list__progress-label"><?php echo esc_html__( 'Progress:', 'croco-school' ); ?></div>
			<div class="croco-school-course-list__progress-status"><?php

				$progress = croco_school()->progress->get_course_progress_data( $course_id );

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
				break;
		}

		echo sprintf( '<a class="croco-school-course-list__progress-link" href="%s"><span>%s</span></a>', $term_link, $term_link_text );
	}

}

