<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#<?php if ( is_user_logged_in() ) : ?>message_send_modal_<?php echo esc_attr( get_the_ID() ); else :	?>user_login_modal<?php endif; ?>" title="<?php esc_attr_e( 'Send Message', 'hivepress-messages' ); ?>" class="<?php echo esc_attr( str_replace( '_', '-', get_post_type() ) ); ?>__action"><i class="hp-icon fas fa-comment"></i></a>
