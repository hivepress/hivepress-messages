<?php
namespace HivePress\Messages;

/**
 * Tests messages.
 *
 * @class Message_Test
 */
class Message_Test extends \WP_UnitTestCase {

	/**
	 * User ID.
	 *
	 * @var int
	 */
	public $user_id;

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Send arguments.
	 *
	 * @var array
	 */
	public $send_args;

	/**
	 * Get arguments.
	 *
	 * @var array
	 */
	public $get_args;

	/**
	 * Setups test.
	 */
	public function setUp() {
		parent::setUp();

		// Create user and login.
		wp_set_current_user( $this->factory->user->create() );

		$this->user_id = $this->factory->user->create();

		// Create post.
		$this->post_id = $this->factory->post->create( [ 'post_type' => 'hp_listing' ] );

		// Set default arguments.
		$this->send_args = [
			'user_id' => $this->user_id,
			'post_id' => $this->post_id,
			'message' => 'Lorem ipsum dolor sit amet consectetuer',
		];

		$this->get_args = [
			'type'    => 'hp_message',
			'status'  => 'approve',
			'user_id' => get_current_user_id(),
			'karma'   => $this->user_id,
			'post_id' => $this->post_id,
		];
	}

	/**
	 * Tests sending.
	 */
	public function test_sending() {

		// Test if message is sent.
		hivepress()->message->send( $this->send_args );

		$this->assertCount( 1, get_comments( $this->get_args ) );

		// Test sending to oneself.
		hivepress()->message->send( array_merge( $this->send_args, [ 'user_id' => get_current_user_id() ] ) );

		$this->assertCount( 0, get_comments( array_merge( $this->get_args, [ 'karma' => get_current_user_id() ] ) ) );

		// Test sending without a post.
		hivepress()->message->send( array_merge( $this->send_args, [ 'post_id' => 0 ] ) );

		$this->assertCount( 2, get_comments( array_merge( $this->get_args, [ 'post_id' => null ] ) ) );

		// Test invalid post types.
		wp_update_post(
			[
				'ID'        => $this->post_id,
				'post_type' => 'post',
			]
		);

		hivepress()->message->send( $this->send_args );

		$this->assertCount( 1, get_comments( $this->get_args ) );

		// Test invalid post statuses.
		wp_update_post(
			[
				'ID'          => $this->post_id,
				'post_type'   => 'hp_listing',
				'post_status' => 'draft',
			]
		);

		hivepress()->message->send( $this->send_args );

		$this->assertCount( 1, get_comments( $this->get_args ) );
	}

	/**
	 * Tests deletion.
	 */
	public function test_deletion() {

		// Test if message is sent.
		hivepress()->message->send( $this->send_args );

		$this->assertCount( 1, get_comments( $this->get_args ) );

		// Delete user.
		wp_delete_user( get_current_user_id() );

		// Test if message is removed.
		$this->assertCount( 0, get_comments( array_merge( $this->get_args, [ 'post_id' => null ] ) ) );
	}
}
