<?php

namespace WeglotWP\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Schema_Option_V3 {

	/**
	 * @since 3.0.0
	 * @return array
	 */
	public static function get_schema_options_v3_compatible() {
		$schema = [
			'api_key'                      => 'api_key',
			'api_key_private'              => 'api_key_private',
			'allowed'                      => 'allowed',
			'original_language'            => 'language_from',
			'destination_language'         => (object) [
				'path' => 'languages',
				'fn'   => function( $languages ) {
					$destinations = [];
					foreach ( $languages as $item ) {
						$destinations[] = $item['language_to'];
					}
					return $destinations;
				},
			],
			'private_mode'         => (object) [
				'path' => 'languages',
				'fn'   => function( $languages ) {
					$private = [];
					foreach ( $languages as $item ) {
						if ( ! $item['enabled'] ) {
							$private[ $item['language_to'] ] = true;
							$private['active'] = true;
						} else {
							$private[ $item['language_to'] ] = false;
						}
					}

					return $private;
				},
			],
			'auto_redirect'                => 'auto_switch',
			'autoswitch_fallback'          => 'auto_switch_fallback',
			'exclude_urls'                 => 'excluded_paths',
			'exclude_blocks'               => (object) [
				'path' => 'excluded_blocks',
				'fn'   => function( $excluded_blocks ) {
					$excluded = [];
					foreach ( $excluded_blocks as $item ) {
						$excluded[] = $item['value'];
					}
					return $excluded;
				},
			],
			'custom_settings'    => 'custom_settings',
			'is_dropdown'        => 'custom_settings.button_style.is_dropdown',
			'is_fullname'        => 'custom_settings.button_style.full_name',
			'with_name'          => 'custom_settings.button_style.with_name',
			'with_flags'         => 'custom_settings.button_style.with_flags',
			'flag_type'          => 'custom_settings.button_style.flag_type',
			'override_css'       => 'custom_settings.button_style.custom_css',
			'email_translate'    => 'custom_settings.translate_email',
			'active_search'      => 'custom_settings.translate_search',
			'translate_amp'      => 'custom_settings.translate_amp',
		];

		return $schema;
	}
}
