<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#" class="hp-message__action hp-message__action--delete hp-link" data-component="file-delete" data-url="<?php echo esc_url( hivepress()->router->get_url( 'message_delete_action', [ 'message_id' => $message->get_id() ] ) ); ?>'"><i class="hp-icon fas fa-times"></i><span><?php esc_html_e( 'Delete', 'hivepress-messages' ); ?></span></a>
