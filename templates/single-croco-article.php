<?php
/**
 * The Template for displaying single CPT Croco Article.
 *
 * @package   Croco_School
 * @author    Croco School
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Croco
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'croco-article' );

/**
 * Fires before main content output started
 */
do_action( 'croco_article_before_main_content' );

?><div class="croco-school">
	<div class="container">
		<div class="croco-school__single-article"><?php

			$post_id = get_the_ID();

			if ( is_active_sidebar( 'croco-school-article-sidebar' ) ) : ?>
				<aside id="secondary" class="croco-school__single-article-sidebar">
					<?php dynamic_sidebar( 'croco-school-article-sidebar' ); ?>
				</aside><!-- #secondary -->
			<?php endif;

			while ( have_posts() ) : the_post();

			?><article id="primary" class="croco-school__single-article-content"><?php

				croco_school()->article_data->get_single_article();

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?></article><?php

			endwhile;?>
		</div>
	</div>
</div><?php

/**
 * Fires after main content output
 */
do_action( 'croco_article_after_main_content' );

get_footer( 'croco-article' );
