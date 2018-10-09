<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="hp-vendor__action hp-js-link alt" data-url="#hp-<?php if ( is_user_logged_in() ) : ?>message-send-<?php echo esc_attr( $vendor->ID ); else : ?>user-login<?php endif; ?>" data-type="popup"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></button>
<?php if ( is_user_logged_in() ) : ?>
	<div id="hp-message-send-<?php echo esc_attr( $vendor->ID ); ?>" class="hp-popup">
		<h3 class="hp-popup__title"><?php esc_html_e( 'Send Message', 'hivepress-messages' ); ?></h3>
		<?php
		echo hivepress()->form->render_form(
			'message__send',
			[
				'attributes'    => [
					'data-type' => 'ajax reset',
				],
				'submit_button' => [
					'attributes' => [
						'class' => 'alt',
					],
				],
			]
		);
		?>
	</div>
	<?php
endif;
