<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-vendor__action hp-js-link alt" data-url="#hp-<?php if ( is_user_logged_in() ) : ?>message-send-<?php echo esc_attr( $vendor->ID ); else : ?>user-login<?php endif; ?>" data-type="popup"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></button>
<?php echo hivepress()->template->render_part( 'message/parts/contact-popup', [ 'vendor' => $vendor ] );
