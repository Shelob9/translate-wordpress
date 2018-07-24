<?php

namespace WeglotWP\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function helper for URL replace filter
 *
 * @since 2.0
 */
abstract class Helper_Filter_Url_Weglot {

	/**
	 * @since 2.0.2
	 * @param string $url
	 * @return string
	 */
	protected static function get_clean_base_url( $url ) {
		$current_language = weglot_get_current_language();

		$parsed_url = parse_url( $url ); //phpcs:ignore
		$scheme     = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
		$host       = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$port       = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
		$user       = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
		$pass       = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';
		$pass       = ($user || $pass) ? "$pass@" : '';
		$path       = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '/';
		$query      = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
		$fragment   = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

		return ( strlen( $path ) > 2 && substr( $path, 0, 4 ) === "/$current_language/" ) ? "$scheme$user$pass$host$port$path$query$fragment" : "$scheme$user$pass$host$port/$l$path$query$fragment";
	}

	/**
	 * Filter URL log redirection
	 *
	 * @param string $url_filter
	 * @return string
	 */
	public static function filter_url_log_redirect( $url_filter ) {
		$current_and_original_language   = weglot_get_current_and_original_language();
		$request_url_service             = weglot_get_request_url_service();
		$choose_current_language         = $current_and_original_language['current'];

		$url_filter = self::get_clean_base_url( $url_filter );
		$url        = $request_url_service->create_url_object( $url_filter );

		if ( $current_and_original_language['current'] === $current_and_original_language['original']
			&& isset( $_SERVER['HTTP_REFERER'] ) //phpcs:ignore
		) {
			$url                     = $request_url_service->create_url_object( $_SERVER['HTTP_REFERER'] ); //phpcs:ignore
			$choose_current_language = $url->detectCurrentLanguage();

			if ( $choose_current_language !== $current_and_original_language['original'] ) {
				$url = $request_url_service->create_url_object( $url_filter );
			}
		}

		return $url->getForLanguage( $choose_current_language );
	}


	/**
	 * Filter url without Ajax
	 *
	 * @since 2.0
	 * @param string $url_filter
	 * @return string
	 */
	public static function filter_url_without_ajax( $url_filter ) {
		$current_and_original_language = weglot_get_current_and_original_language();
		$request_url_service           = weglot_get_request_url_service();
		if ( $current_and_original_language['current'] === $current_and_original_language['original'] ) {
			return $url_filter;
		}

		$url = $request_url_service->create_url_object( $url_filter );

		return $url->getForLanguage( $current_and_original_language['current'] );
	}

	/**
	 * Filter url with optional Ajax
	 *
	 * @since 2.0
	 * @param string $url_filter
	 * @return string
	 */
	public static function filter_url_with_ajax( $url_filter ) {
		$current_and_original_language = weglot_get_current_and_original_language();
		$choose_current_language       = $current_and_original_language['current'];
		$request_url_service           = weglot_get_request_url_service();
		if ( $current_and_original_language['current'] !== $current_and_original_language['original'] ) { // Not ajax
			$url = $request_url_service->create_url_object( $url_filter );
		} else {
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) { //phpcs:ignore
				// Ajax
				$url                     = $request_url_service->create_url_object( $_SERVER['HTTP_REFERER'] ); //phpcs:ignore
				$choose_current_language = $url->detectCurrentLanguage();
				$url                     = $request_url_service->create_url_object( $url_filter );
			}
		}

		return $url->getForLanguage( $choose_current_language );
	}
}
