<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-vendor__action hp-vendor__action--message button button--large button--primary alt" data-component="link" data-url="#<?php if ( is_user_logged_in() ) : ?>message_send_modal_<?php echo esc_attr( $user->get_id() ); else : ?>user_login_modal<?php endif; ?>"><?php echo esc_html( hivepress()->translator->get_string( 'send_message' ) ); ?></button>
