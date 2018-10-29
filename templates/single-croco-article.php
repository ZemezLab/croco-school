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
		<?php

			croco_school()->article_data->get_single_article();

			?>
	</div>
</div><?php

/**
 * Fires after main content output
 */
do_action( 'croco_article_after_main_content' );

get_footer( 'croco-article' );
