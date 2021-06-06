<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-listing__action hp-listing__action--message button button--large button--primary alt" data-component="link" data-url="#message_send_modal_<?php echo esc_attr( $booking->get_id() ); ?>"><?php echo esc_html( hivepress()->translator->get_string( 'send_message' ) ); ?></button>
