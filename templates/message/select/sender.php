<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'message/view_messages', [ 'recipient_id' => $message->get_sender_id() ] ) ); ?>"><strong><?php echo esc_html( $message->get_sender_name() ); ?></strong></a>
</td>
