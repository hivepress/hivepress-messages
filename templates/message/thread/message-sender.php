<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$url_args    = [ 'user_id' => $message->get_sender__id() ];
$sender_name = $message->get_sender__display_name();

if ( get_current_user_id() !== $message->get_recipient__id() ) :
	$url_args['recipient_id'] = $message->get_recipient__id();
	$sender_name             .= '&nbsp;&rarr;&nbsp;' . $message->get_recipient__display_name();
endif;
?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( hivepress()->router->get_url( 'messages_view_page', $url_args ) . '#message-' . $message->get_id() ); ?>" class="hp-link">
		<i class="hp-icon fas fa-envelope
		<?php
		if ( $message->is_read() ) :
			?>
			-open<?php endif; ?>"></i>
		<span><?php echo esc_html( $sender_name ); ?></span>
	</a>
</td>
