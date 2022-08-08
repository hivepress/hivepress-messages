<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( $message_url ); ?>" class="hp-link">
		<i class="hp-icon fas fa-envelope<?php if ( $message->is_read() ) : ?>-open<?php endif; ?>"></i>
		<span><?php echo esc_html( $sender_name ); ?></span>
	</a>
</td>
