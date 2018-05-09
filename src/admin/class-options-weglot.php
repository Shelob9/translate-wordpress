<?php

namespace Weglot\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Hooks_Interface_Weglot;

/**
 * Sanitize options after submit form
 *
 * @since 2.0
 */
class Options_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', [ $this, 'weglot_options_init' ] );
	}

	/**
	 * Register setting options
	 *
	 * @see admin_init
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_options_init() {
		register_setting( WEGLOT_OPTION_GROUP, WEGLOT_SLUG, [ $this, 'sanitize_options' ] );
	}

	/**
	 * Callback register_setting for sanitize options
	 *
	 * @since 2.0
	 *
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options( $options ) {
		return $options;
	}
}