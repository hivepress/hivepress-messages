<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'message/view_messages', [ 'user_id' => $message->get_sender_id() ] ) ); ?>" class="hp-link"><i class="hp-icon fas fa-reply"></i><span><?php echo esc_html( $message->get_sender_name() ); ?></span></a>
</td>
