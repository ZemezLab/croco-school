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

class Croco_School_Guide extends Croco_School_Base {

	public function get_name() {
		return 'croco-school-guide';
	}

	public function get_title() {
		return esc_html__( 'Croco Articles', 'croco-school' );
	}

	public function get_icon() {
		return 'eicon-wordpress';
	}

	public function get_categories() {
		return array( 'croco-school' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'croco-school' ),
			)
		);

		$this->add_control(
			'guide_image',
			array(
				'label' => esc_html__( 'Guide Image', 'croco-school' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'guide_name',
			array(
				'label'   => esc_html__( 'Guide Name', 'croco-school' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Croco Guide',
			)
		);

		$this->add_control(
			'more_link_icon',
			array(
				'label'       => esc_html__( 'More Link Icon', 'croco-school' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
				'default'     => 'fa fa-long-arrow-right',
			)
		);

		$this->add_control(
			'use_back_button',
			array(
				'label'        => esc_html__( 'Use Back Button', 'croco-school' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'back_button_icon',
			array(
				'label'       => esc_html__( 'Back Button Icon', 'croco-school' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
				'default'     => 'fa fa-long-arrow-left',
				'condition' => [
					'use_back_button' => 'yes'
				],
			)
		);

		$this->add_control(
			'back_button_text',
			array(
				'label'   => esc_html__( 'Back Button Text', 'croco-school' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Back', 'croco-school' ),
				'condition' => [
					'use_back_button' => 'yes'
				],
			)
		);

		$this->add_control(
			'back_button_url',
			array(
				'label' => esc_html__( 'Back Button Link', 'croco-school' ),
				'type' => Controls_Manager::URL,
				'placeholder' => '#',
				'default' => array(
					'url' => '#',
				),
				'condition' => [
					'use_back_button' => 'yes'
				],
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'Settings', 'croco-school' ),
			)
		);

		$this->add_control(
			'is_archive_template',
			array(
				'label'        => esc_html__( 'Use as Archive Template', 'croco-school' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'show_child_terms',
			array(
				'label'        => esc_html__( 'Show Child Terms', 'croco-school' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
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
					'condition' => [
						//'is_archive_template' => 'no',
					],
				]
			);
		}

		$this->add_control(
			'use_article_limit',
			array(
				'label'        => esc_html__( 'Use article limit', 'croco-school' ),
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
				'default' => -1,
				'min'     => 0,
				'max'     => 20,
				'step'    => 1,
				'condition' => [
					'use_article_limit' => 'yes',
				],
			)
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_typography_styles',
			array(
				'label'      => esc_html__( 'Typography', 'croco-school' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'main_title_typography_heading',
			array(
				'label'     => esc_html__( 'Title Typography', 'croco-school' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'main_title_color',
			array(
				'label'  => esc_html__( 'Main Title Color', 'croco-school' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .croco-school-guide__name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'main_title_typography',
				'selector' => '{{WRAPPER}} .croco-school-guide__name',
			)
		);

		$this->add_control(
			'children_title_typography_heading',
			array(
				'label'     => esc_html__( 'Children Term Title Typography', 'croco-school' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'children_title_color',
			array(
				'label'  => esc_html__( 'Guide Title Color', 'croco-school' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .croco-school-guide__article-list-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'children_title_typography',
				'selector' => '{{WRAPPER}} .croco-school-guide__article-list-title',
			)
		);

		$this->add_control(
			'article_typography_heading',
			array(
				'label'     => esc_html__( 'Article Typography', 'croco-school' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'article_typography_tabs' );

		$this->start_controls_tab(
			'article_typography_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'croco-school' ),
			)
		);

		$this->add_control(
			'article_color_normal',
			array(
				'label'  => esc_html__( 'Article Color', 'croco-school' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .croco-school-guide__article-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'article_typography_normal',
				'selector' => '{{WRAPPER}} .croco-school-guide__article-link',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'article_typography_tab_hover',
			array(
				'label' => esc_html__( 'Hover', 'croco-school' ),
			)
		);

		$this->add_control(
			'article_color_hover',
			array(
				'label'  => esc_html__( 'Article Color', 'croco-school' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .croco-school-guide__article-link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'article_typography_hover',
				'selector' => '{{WRAPPER}} .croco-school-guide__article-link:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'more_articles_typography_heading',
			array(
				'label'     => esc_html__( 'More Articles Typography', 'croco-school' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'more_articles_color',
			array(
				'label'  => esc_html__( 'More Articles Color', 'croco-school' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .croco-school-guide__more-articles' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'more_articles_typography',
				'selector' => '{{WRAPPER}} .croco-school-guide__more-articles',
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
		$show_child_terms = filter_var( $settings['show_child_terms'], FILTER_VALIDATE_BOOLEAN );
		$use_back_button = filter_var( $settings['use_back_button'], FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_archive_template ) {
			$term_id = $settings['term_id'];
		} else {

			if ( isset( get_queried_object()->term_id )) {
				$term_id = get_queried_object()->term_id;
			}else {
				$term_id = $settings['term_id'];
			}
		}

		if ( empty( $term_id ) ) {
			echo '<h2>Articles not found</h2>';
			return false;
		}

		$id_int = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute( 'container', [
			'class' => [
				'croco-school-guide',
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<div class="croco-school-guide__inner"><?php

				$term_data = get_term( $term_id );

				if ( ! empty( $settings['guide_image']['url'] ) ) {
					echo sprintf( '<div class="croco-school-guide__thumbnail"><img src="%s" alt=""/></div>', $settings['guide_image']['url'] );
				}

				if ( ! empty( $settings['guide_name'] ) ) {
					$back_button_text = $settings['back_button_text'];
					$back_button_url = $settings['back_button_url'];

					$back_button_html = '';

					if ( ! empty( $back_button_text ) && $use_back_button ) {
						$back_button_icon = $settings['back_button_icon'];

						$back_button_html = sprintf( '<a class="croco-school-guide__back" href="%s"><i class="%s"></i>%s</a>', $back_button_url['url'], $back_button_icon, $back_button_text );
					}

					echo sprintf( '<div class="croco-school-guide__name-container">%s<h2 class="croco-school-guide__name">%s</h2></div>', $back_button_html, $term_data->name );
				}

				$this->generate_article_list( $term_id );

				if ( $show_child_terms ) {
					$term_childs = get_term_children( $term_data->term_id, croco_school()->post_type->category_term_slug() );?>

					<div class="croco-school-guide__terms-list"><?php
						foreach ( $term_childs as $child ) {
							$child_term = get_term_by( 'id', $child, croco_school()->post_type->category_term_slug() );?>
							<div class="croco-school-guide__terms-item"><?php
								$this->generate_article_list( $child_term->term_id, true );?>
							</div><?php
						}?>
					</div><?php
				}?>
			</div>
		</div><?php
	}

	/**
	 * [generate_article_list description]
	 * @param  string $term_id [description]
	 * @return [type]          [description]
	 */
	public function generate_article_list( $term_id = '', $term_name_visible = false ) {

		if ( empty( $term_id ) ) {
			return false;
		}

		$settings = $this->get_settings();
		$is_archive_template = filter_var( $settings['is_archive_template'], FILTER_VALIDATE_BOOLEAN );
		$use_article_limit = filter_var( $settings['use_article_limit'], FILTER_VALIDATE_BOOLEAN );
		$article_list_limit = $settings['article_limit'];
		$more_link_icon = $settings['more_link_icon'];

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

		$term_data = get_term( $term_id );

		?><div class="croco-school-guide__article-list"><?php
			if ( $term_name_visible ) {?>
				<h3 class="croco-school-guide__article-list-title"><?php echo $term_data->name; ?></h3><?php
			}?>

			<ul><?php

				$count = 0;

				while ( $query->have_posts() ) : $query->the_post();

					if ( $use_article_limit && $count > $article_list_limit - 1 ) {
						continue;
					}?>

					<li id="croco-article-<?php the_ID(); ?>">
						<a class="croco-school-guide__article-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li><?php

					$count++;

				endwhile;

				wp_reset_postdata();?>
			</ul><?php
				if ( $use_article_limit ) {
					$term_link_text = esc_html__( 'See all articles', 'croco-school' );
					$term_link = get_term_link( (int)$term_id, croco_school()->post_type->category_term_slug() );

					$more_link_icon_html = ! empty( $more_link_icon ) ? '<i class="' . $more_link_icon . '"></i>' : '';

					echo sprintf( '<a class="croco-school-guide__more-articles" href="%s">%s%s</a>', $term_link, $term_link_text, $more_link_icon_html );
				}
			?>
		</div><?php
	}

}

