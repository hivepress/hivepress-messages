<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$display = get_option( 'hp_user_enable_display' );
?>
<div class="hp-message__sender">
	<?php
	if ( $display ) :
		?>
		<a href="<?php echo esc_url( hivepress()->router->get_url( 'user_view_page', [ 'username' => $message->get_sender__username() ] ) ); ?>">
		<?php
	endif;

	echo esc_html( $message->get_sender__display_name() );

	if ( $display ) :
		?>
		</a>
		<?php
	endif;
	?>
</div>
