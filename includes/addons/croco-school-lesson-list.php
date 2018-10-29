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

class Croco_School_Lesson_List extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-lesson-list';
	}

	public function get_title() {
		return esc_html__( 'Croco Lesson List', 'croco-school' );
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

		$this->add_render_attribute( 'container', [
			'class' => [
				'croco-school-lesson-list',
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<div class="croco-school-lesson-list__inner">
				<div class="croco-school-lesson-list__grid"><?php

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
						$excerpt = get_the_excerpt( $post_id );
						$excerpt = \Croco_School_Utils::cut_text( $excerpt, 15, 'word', '...', true );
						$permalink = get_the_permalink( $post_id );

						$status = croco_school()->progress->get_article_progress( $post_id );

						$link_text = __( 'View Lesson', 'croco-school' );

						switch ( $status ) {

							case 'in_progress':
								$link_text = __( 'Continue', 'croco-school' );
								break;

							case 'done':
								$link_text = __( 'Complete', 'croco-school' );
								break;

							case 'not_started':
								$link_text = __( 'View Lesson', 'croco-school' );
								break;
						}

						?><div id="croco-article-<?php the_ID(); ?>" class="croco-school-lesson-list__item">
							<div class="croco-school-lesson-list__item-inner"><?php

								echo sprintf( '<span class="croco-school-lesson-list__lessons"><span>%s</span>%s</span>', __( 'Lesson', 'croco-school' ), $count );

								echo sprintf( '<h2 class="croco-school-lesson-list__title">%s</h2>', $title );

								echo sprintf( '<p class="croco-school-lesson-list__excerpt">%s</p>', $excerpt );

								echo sprintf( '<a class="croco-school-lesson-list__permalink status-%s" href="%s"><i class="fa fa-check"></i><span>%s</span></a>', $status, $permalink, $link_text ); ?>
							</div>
						</div><?php

						$count++;

					endwhile;

					wp_reset_postdata();

					?>
				</div>
			</div>
		</div><?php

	}
}

