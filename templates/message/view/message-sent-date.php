<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<time class="hp-message__sent-date hp-message__date hp-meta" datetime="<?php echo esc_attr( $message->get_sent_date() ); ?>"><?php echo esc_html( $message->display_sent_date() ); ?></time>
