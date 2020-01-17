<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<td class="hp-message__listing">
	<?php if ( $message->get_listing__id() ) : ?>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $message->get_listing__id() ] ) ); ?>" target="_blank" class="hp-link"><i class="hp-icon fas fa-external-link-alt"></i><span><?php echo esc_html( $message->get_listing__title() ); ?></span></a>
	<?php endif; ?>
</td>
