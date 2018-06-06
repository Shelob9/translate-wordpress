<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Endpoint\Languages;
use Weglot\Client\Client;

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Button services
 *
 * @since 2.0
 */
class Button_Service_Weglot implements Mediator_Service_Interface_Weglot {


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

	public function get_html() {
		$options                          = $this->option_services->get_options();
		$is_fullname                      = $options['is_fullname'];
		$with_name                        = $options['with_name'];
		$is_dropdown                      = $options['is_dropdown'];
		$flag_class                       = $options['with_flags'];
		$destination_language             = $options['destination_language'];
		$original_language                = $options['original_language'];
		$current_language                 = $this->request_url_services->get_current_language();
		$type_flags                       = $options['type_flags'];
		$weglot_url                       = $this->request_url_services->get_weglot_url();

		$flag_class                       = 'wg-flags ';
		$tag                              = $is_dropdown ? 'div' : 'li';
		$list_tag                         = $is_dropdown ? '<ul>' : '';

		$client    = new Client( $this->option_services->get_option( 'api_key' ) );
		$languages = new Languages( $client );
		$languages = $languages->handle();

		$button_html = sprintf( '<!--Weglot %s-->', WEGLOT_VERSION );
		$button_html .= "<aside data-wg-notranslate='' id='weglot-selector' class='wg-default wg-drop country-selector closed'>";

		$button_html .= sprintf(
			'<%s data-wg-notranslate="" class="wgcurrent wg-li wg-flags %s"><a href="#" onclick="return false;">%s</a></%s>',
			$tag,
			$current_language,
			$languages[ $current_language ]->getEnglishName(),
			$tag
		);

		$button_html .= $list_tag;

		foreach ( $destination_language as $key => $key_code ) {
			if ( $key_code === $current_language ) {
				continue;
			}

			$button_html .= sprintf( '<li class="wg-li %s">', $flag_class . $key_code );

			$button_html .= sprintf(
				'<a data-wg-notranslate href="%s">%s</a>',
				$weglot_url->getForLanguage( $key_code ),
				$languages[ $key_code ]->getEnglishName()
			);

			$button_html .= '</li>';
		}

		$button_html .= $list_tag;
		$button_html .= '</aside>';

		return apply_filters( 'weglot_button_html', $button_html );
	}
}
