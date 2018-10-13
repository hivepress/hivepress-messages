<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

echo hivepress()->form->render_form(
	'message__send',
	[
		'attributes'    => [
			'data-type' => get_query_var( 'hp_message_chat' ) ? '' : 'ajax reset',
		],
		'submit_button' => [
			'attributes' => [
				'class' => 'alt',
			],
		],
	]
);
