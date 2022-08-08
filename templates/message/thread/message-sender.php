<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set message url.
$url = hivepress()->router->get_url( 'messages_view_page', [ 'user_id' => $message->get_sender__id() ] ) . '#message-' . $message->get_id();

// Set messages sender.
$name = $message->get_sender__display_name();

if ( get_option( 'hp_message_allow_monitoring' ) && current_user_can( 'manage_options' ) && ( $message->get_sender__id() !== $message->get_recipient__id() && ! in_array( get_current_user_id(), [ $message->get_sender__id(), $message->get_recipient__id() ] ) ) ) {
	$url = hivepress()->router->get_url(
		'messages_view_page',
		[
			'user_id'      => $message->get_sender__id(),
			'recipient_id' => $message->get_recipient__id(),
		]
	) . '#message-' . $message->get_id();

	$name = $message->get_sender__display_name() . ' - ' . $message->get_recipient__display_name();
}

?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( $url ); ?>" class="hp-link">
		<i class="hp-icon fas fa-envelope<?php if ( $message->is_read() ) : ?>-open<?php endif; ?>"></i>
		<span><?php echo esc_html( $name ); ?></span>
	</a>
</td>
