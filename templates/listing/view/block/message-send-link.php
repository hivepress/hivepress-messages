<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<a href="#<?php if ( is_user_logged_in() ) : ?>message_send_modal_<?php echo esc_attr( get_the_ID() ); else :	?>user_login_modal<?php endif; ?>" title="<?php echo esc_attr( hivepress()->translator->get_string( 'reply_to_listing' ) ); ?>" class="hp-listing__action"><i class="hp-icon fas fa-comment"></i></a>
