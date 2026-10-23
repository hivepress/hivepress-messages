<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__sender">
	<a href="<?php echo esc_url( $message_url ); ?>" class="hp-link">
		<?php if ( get_current_user_id() !== $message->get_recipient__id() ) : ?>
			<i class="hp-icon fas fa-eye" title="<?php esc_attr_e( 'Monitoring', 'hivepress-messages' ); ?>"></i>
		<?php else : ?>
			<i class="hp-icon fas fa-envelope<?php if ( $message->is_read() ) : ?>-open<?php endif; ?>"></i>
		<?php endif; ?>
		<span><?php echo esc_html( $sender_name ); ?></span>
	</a>
</td>
