<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! empty( $messages ) ) :
	?>
	<div class="hp-messages">
		<?php foreach ( $messages as $message ) : ?>
		<div class="hp-message">
			<?php if ( ! empty( $message->comment_post_ID ) ) : ?>
			<div class="hp-message__subject">
				<i class="fas fa-reply"></i>
				<a href="<?php echo esc_url( get_permalink( $message->comment_post_ID ) ); ?>" target="_blank"><?php echo esc_html( get_the_title( $message->comment_post_ID ) ); ?></a>
			</div>
			<?php endif; ?>
			<div class="hp-message__header">
				<strong class="hp-message__sender"><?php echo esc_html( $message->comment_author ); ?></strong>
				<time class="hp-message__date" datetime="<?php echo esc_attr( get_comment_date( 'Y-m-d', $message->comment_ID ) ); ?>"><?php echo esc_html( get_comment_date( '', $message->comment_ID ) ); ?></time>
			</div>
			<div class="hp-message__text">
				<?php comment_text( $message->comment_ID ); ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<div class="hp-no-results">
		<p><?php esc_html_e( 'No messages yet.', 'hivepress-messages' ); ?></p>
	</div>
	<?php
endif;
