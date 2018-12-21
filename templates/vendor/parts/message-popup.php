<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() ) :
	?>
	<div id="hp-message-send-<?php echo esc_attr( $vendor->ID ); ?>" class="hp-popup">
		<h3 class="hp-popup__title"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></h3>
		<?php echo hivepress()->template->render_part( 'message/parts/send-form' ); ?>
	</div>
	<?php
endif;
