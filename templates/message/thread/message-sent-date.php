<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__sent-date hp-message__date">
	<time datetime="<?php echo esc_attr( $message->get_sent_date() ); ?>"><?php echo esc_html( $message->display_sent_date() ); ?></time>
</td>
