<?php
/**
 * The Template for displaying single CPT Croco Article Standard.
 *
 * @package   Croco_School
 * @author    Croco School
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Croco
 */

?><h2 class="croco-school__single-article-title"><?php echo the_title(); ?></h2>
<div class="croco-school__single-article-content"><?php
	ob_start();
	the_content( '' );
	$content = ob_get_contents();
	ob_end_clean();

	echo $content; ?>
</div>
