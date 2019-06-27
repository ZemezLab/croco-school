<?php
/**
 * Template part to display sharing button.
 *
 * @package Cherry_Socialize
 */
?>
<li class="<?php echo esc_attr( $config['base_class'] ); ?>__item">
	<a class="<?php echo esc_attr( $config['base_class'] ); ?>__link <?php echo esc_attr( $config['base_class'] ); ?>__link--<?php echo esc_attr( $id ); ?>" href="<?php echo htmlentities( $share_url ); ?>" target="_blank" rel="nofollow" title="<?php printf( esc_html__( 'Share on %s', 'croco-school' ), esc_attr( $network['name'] ) ); ?>">
		<?php
		switch ( $config['type'] ) {
			case 'icon': ?>
				<i class="<?php echo esc_attr( $config['base_class'] ); ?>__link-icon <?php echo esc_attr( $network['icon'] ); ?>"></i>
				<?php
				break;

			case 'text': ?>
				<span class="<?php echo esc_attr( $config['base_class'] ); ?>__link-text"><?php esc_html_e( $network['name'] ); ?></span>
				<?php
				break;

			default: ?>
				<i class="<?php echo esc_attr( $config['base_class'] ); ?>__link-icon <?php echo esc_attr( $network['icon'] ); ?>"></i>
				<span class="<?php echo esc_attr( $config['base_class'] ); ?>__link-text"><?php esc_html_e( $network['name'] ); ?></span>
		<?php } ?>
	</a>
</li>
