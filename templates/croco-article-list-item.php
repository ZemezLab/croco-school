<?php
/**
 * The Template for displaying Article Item
 *
 * @package   Croco_School
 * @author    Croco School
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Croco
 */

?><div class="croco-school__article-list-item">
	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
</div>
