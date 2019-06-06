<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__listing">
	<?php if ( $message->get_listing_id() ) : ?>
		<a href="<?php echo esc_url( get_permalink( $message->get_listing_id() ) ); ?>" target="_blank" class="hp-link"><i class="hp-icon fas fa-external-link-alt"></i><span><?php echo esc_html( get_the_title( $message->get_listing_id() ) ); ?></span></a>
	<?php endif; ?>
</td>
