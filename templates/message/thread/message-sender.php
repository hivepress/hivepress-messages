<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( get_option( 'hp_message_allow_monitoring' ) && current_user_can( 'manage_options' ) && ( $message->get_sender__id() !== $message->get_recipient__id() && ! in_array( get_current_user_id(), [ $message->get_sender__id(), $message->get_recipient__id() ] ) ) ) {
	?>
	<td class="hp-message__sender">
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'messages_monitor_page', [ 'user_id'      => $message->get_sender__id(), 'recipient_id' => $message->get_recipient__id(), ] ) . '#message-' . $message->get_id() );?>" class="hp-link">
			<i class="hp-icon fas fa-envelope<?php if ( $message->is_read() ) : ?>-open<?php endif; ?>"></i>
			<span><?php echo esc_html( $message->get_sender__display_name() ); ?></span>
		</a>
	</td>
<?php } else { ?>
	<td class="hp-message__sender">
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'messages_view_page', [ 'user_id' => $message->get_sender__id() ] ) . '#message-' . $message->get_id() ); ?>" class="hp-link">
			<i class="hp-icon fas fa-envelope<?php if ( $message->is_read() ) : ?>-open<?php endif; ?>"></i>
			<span><?php echo esc_html( $message->get_sender__display_name() ); ?></span>
		</a>
	</td>
<?php }
?>
