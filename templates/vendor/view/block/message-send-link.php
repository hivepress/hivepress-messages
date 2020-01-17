<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#<?php if ( is_user_logged_in() ) : ?>message_send_modal_<?php echo esc_attr( $vendor->get_id() ); else : ?>user_login_modal<?php endif; ?>" title="<?php esc_attr_e( 'Send Message', 'hivepress-messages' ); ?>" class="hp-vendor__action hp-vendor__action--message"><i class="hp-icon fas fa-comment"></i></a>
