<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $message->get_attachment__id() ) :
	?>
	<a href="<?php echo esc_url( $message->get_attachment__url() ); ?>" target="_blank" class="hp-message__attachment hp-link">
		<i class="hp-icon fas fa-download"></i>
		<span><?php echo esc_html( wp_basename( $message->get_attachment__url() ) ); ?></span>
	</a>
	<?php
endif;
