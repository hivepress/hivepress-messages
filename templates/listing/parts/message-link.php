<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#hp-<?php if ( is_user_logged_in() ) : ?>message-send-<?php the_ID(); else : ?>user-login<?php endif; ?>" title="<?php esc_attr_e( 'Send Message', 'hivepress-messages' ); ?>" class="hp-listing__action hp-js-link" data-type="popup"><i class="hp-icon fas fa-comment"></i></a>
<?php echo hivepress()->template->render_part( 'listing/parts/message-popup' ); ?>
