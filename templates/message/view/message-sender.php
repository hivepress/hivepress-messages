<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$sender = $message->get_sender();
$display = get_option( 'hp_user_enable_display' ) && $sender && get_current_user_id() !== $sender->get_id();
if ( $display ) : ?>
<a class="hp-link hp-message__sender" href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $sender->get_username() ] ) ); ?>">
<?php endif; ?>
<strong <?php if ( ! $display ) : ?>class="hp-message__sender"<?php endif; ?>><?php echo esc_html( $message->get_sender__display_name() ); ?></strong>
<?php if ( $display ) : ?></a><?php endif; ?>
