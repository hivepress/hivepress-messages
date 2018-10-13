<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! empty( $chats ) ) :
	?>
	<table>
		<?php foreach ( $chats as $chat ) : ?>
		<tr>
			<td>
				<a href="<?php echo esc_url( hivepress()->template->get_url( 'message__chat', [ $chat->user_id ] ) ); ?>"><i class="hp-icon fas fa-reply"></i><?php echo esc_html( $chat->comment_author ); ?></a>
			</td>
			<td>
				<?php if ( ! empty( $chat->comment_post_ID ) ) : ?>
				<a href="<?php echo esc_url( get_permalink( $chat->comment_post_ID ) ); ?>" target="_blank"><i class="hp-icon fas fa-external-link-alt"></i><?php echo esc_html( get_the_title( $chat->comment_post_ID ) ); ?></a>
				<?php endif; ?>
			</td>
			<td>
				<time datetime="<?php echo esc_attr( get_comment_date( 'Y-m-d', $chat->comment_ID ) ); ?>"><?php echo esc_html( get_comment_date( '', $chat->comment_ID ) ); ?></time>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<div class="hp-no-results">
		<p><?php esc_html_e( 'No messages yet.', 'hivepress-messages' ); ?></p>
	</div>
	<?php
endif;
