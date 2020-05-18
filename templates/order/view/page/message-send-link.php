<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( $order->get_status() === 'wc-processing' ) :
	?>
	<button type="button" class="hp-order__action hp-order__action--message button button--primary alt" data-component="link" data-url="#message_send_modal">
		<?php
		if ( get_current_user_id() === $order->get_buyer__id() ) :
			echo esc_html( hivepress()->translator->get_string( 'contact_seller' ) );
		else :
			echo esc_html( hivepress()->translator->get_string( 'contact_buyer' ) );
		endif;
		?>
	</button>
	<?php
endif;
