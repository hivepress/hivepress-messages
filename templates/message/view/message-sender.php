<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<strong class="hp-message__sender <?php if ( $message->get_sender_id() === get_current_user_id() ) : ?>hp-message__sender--current<?php endif; ?>"><?php echo esc_html( $message->get_sender_name() ); ?></strong>
