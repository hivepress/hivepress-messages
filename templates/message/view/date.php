<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<time class="hp-message__date" datetime="<?php echo esc_attr( $message->get_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( $message->get_date() ); ?></time>
