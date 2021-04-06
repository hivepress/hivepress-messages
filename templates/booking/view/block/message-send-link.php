<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#message_send_modal_<?php echo esc_attr( $booking->get_id() ); ?>" title="<?php echo esc_attr( hivepress()->translator->get_string( 'send_message' ) ); ?>" class="hp-listing__action hp-listing__action--message"><i class="hp-icon fas fa-comment"></i></a>
