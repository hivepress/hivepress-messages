<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-listing__action hp-js-link alt" data-url="#hp-<?php if ( is_user_logged_in() ) : ?>message-send-<?php the_ID(); else : ?>user-login<?php endif; ?>" data-type="popup"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></button>
<?php echo hivepress()->template->render_part( 'listing/parts/message-popup' ); ?>
