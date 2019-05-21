<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="<?php echo esc_attr( str_replace( '_', '-', get_post_type() ) ); ?>__action alt" data-component="link" data-url="#<?php if ( is_user_logged_in() ) : ?>message_send_modal_<?php echo esc_attr( get_the_ID() ); else :	?>user_login_modal<?php endif; ?>"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></button>
