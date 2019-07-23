<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-listing__action button button--large button--primary alt" data-component="link" data-url="#<?php if ( is_user_logged_in() ) : ?>message_send<?php else : ?>user_login<?php endif; ?>_modal"><?php esc_html_e( 'Reply to Listing', 'hivepress-messages' ); ?></button>
