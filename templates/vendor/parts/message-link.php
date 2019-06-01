<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#hp-<?php if ( is_user_logged_in() ) : ?>message-send-<?php echo esc_attr( $vendor->ID ); else : ?>user-login<?php endif; ?>" title="<?php esc_attr_e( 'Send Message', 'hivepress-messages' ); ?>" class="hp-vendor__action hp-js-link" data-type="popup"><i class="hp-icon fas fa-comment"></i></a>
<?php echo hivepress()->template->render_part( 'vendor/parts/message-popup', [ 'vendor' => $vendor ] ); ?>
