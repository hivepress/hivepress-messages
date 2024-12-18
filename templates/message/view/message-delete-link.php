<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#message_delete_modal_<?php echo esc_attr( $message->get_id() ); ?>" title="<?php esc_attr_e( 'Delete Message', 'hivepress-messages' ); ?>" class="hp-message__action hp-message__action--delete hp-link"><i class="hp-icon fas fa-times"></i></a>
