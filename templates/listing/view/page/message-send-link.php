<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-listing__action button button--large button--primary alt" data-component="link" data-url="#<?php if ( is_user_logged_in() ) : ?>message_send_modal_<?php echo esc_attr( get_the_ID() ); else : ?>user_login_modal<?php endif; ?>"><?php echo esc_html( hivepress()->translator->get_string( 'reply_to_listing' ) ); ?></button>
