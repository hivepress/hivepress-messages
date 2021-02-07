<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'messages_view_page', [ 'user_id' => $message->get_sender__id() ] ) . '#message-' . $message->get_id() ); ?>" class="hp-link">
		<i class="hp-icon fas fa-envelope<?php if ( $message->is_read() ) : ?>-open<?php endif; ?>"></i>
		<span><?php echo esc_html( $message->get_sender__display_name() ); ?></span>
	</a>
</td>
