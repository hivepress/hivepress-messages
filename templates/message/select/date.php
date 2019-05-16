<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__date">
	<time datetime="<?php echo esc_attr( $message->get_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( $message->get_date() ); ?></time>
</td>
