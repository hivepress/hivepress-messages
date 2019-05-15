<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#<?php if ( is_user_logged_in() ) : ?>message_send<?php else :	?>user_login<?php endif; ?>_modal" title="<?php esc_attr_e( 'Send Message', 'hivepress-messages' ); ?>" class="hp-listing__action"><i class="hp-icon fas fa-comment"></i></a>
