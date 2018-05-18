<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Util\Url;
use Weglot\Util\Server;

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Redirect URL
 *
 * @since 2.0
 */
class Redirect_Service_Weglot implements Mediator_Service_Interface_Weglot {
	/**
	 * @since 2.0
	 *
	 * @var string
	 */
	protected $weglot_url = null;

	/**
	 *
	 * @var boolean
	 */
	protected $no_redirect = false;

	/**
	 * @see Mediator_Service_Interface_Weglot
	 * @since 2.0
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->option_services      = $services['Option_Service_Weglot'];
		$this->request_url_services = $services['Request_Url_Service_Weglot'];
	}

	/**
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function get_no_redirect() {
		return $this->no_redirect;
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function auto_redirect() {
		if ( ! isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) { //phpcs:ignore
			return;
		}

		$server_lang           = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ); //phpcs:ignore
		$destination_languages = $this->option_services->get_option( 'destination_language' );

		if (
			in_array( $server_lang, $destination_languages, true ) &&
			$server_lang !== $this->request_url_services->get_current_language()
		) {
			wp_safe_redirect( sprintf( '%s%s/', $this->request_url_services->get_weglot_url()->getBaseUrl(), $server_lang ) );
			exit();
		}
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function verify_no_redirect() {
		if ( strpos( $this->request_url_services->get_weglot_url()->getUrl(), '?no_lredirect=true' ) === false ) {
			return;
		}

		$this->no_redirect = true;

		if ( isset( $_SERVER['REQUEST_URI'] ) ) { // phpcs:ignore
			$_SERVER['REQUEST_URI'] = str_replace(
				'?no_lredirect=true',
				'',
				$_SERVER['REQUEST_URI'] //phpcs:ignore
			);
		}
	}
}


