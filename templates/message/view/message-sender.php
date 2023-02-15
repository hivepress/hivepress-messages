<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$display = get_option( 'hp_user_enable_display' ) && $message->get_sender() && get_current_user_id() !== $message->get_sender__id();
if ( $display ) : ?>
<a class="hp-link hp-message__sender" href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $message->get_sender__username() ] ) ); ?>">
<?php endif; ?>
<strong <?php if ( ! $display ) : ?>class="hp-message__sender"<?php endif; ?>><?php echo esc_html( $message->get_sender__display_name() ); ?></strong>
<?php if ( $display ) : ?></a><?php endif; ?>
