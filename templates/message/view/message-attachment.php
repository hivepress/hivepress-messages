<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $message->get_attachment__id() ) :
	?>
	<a href="<?php echo esc_url( $message->get_attachment__url() ); ?>" target="_blank" class="hp-message__attachment hp-link">
		<i class="hp-icon fas fa-file-download"></i>
		<span><?php echo esc_html( $message->get_attachment__name() ); ?></span>
	</a>
	<?php
endif;
