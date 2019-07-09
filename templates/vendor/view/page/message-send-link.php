<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-vendor__action button is-primary alt" data-component="link" data-url="#<?php if ( is_user_logged_in() ) : ?>message_send<?php else : ?>user_login<?php endif; ?>_modal"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></button>
