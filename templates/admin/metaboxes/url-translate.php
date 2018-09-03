<?php

use WeglotWP\Helpers\Helper_Post_Meta_Weglot;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$languages_available     = $this->language_services->get_languages_configured();
$original_language       = weglot_get_original_language();
list( $permalink )       = get_sample_permalink( $post->ID );
$display_link            = str_replace( array( '%pagename%', '%postname%', home_url() ), '', $permalink );
$display_link            = implode( '/', array_filter( explode( '/', $display_link ), 'strlen' ) );

?>
<input type="hidden" id="weglot_post_id" data-id="<?php echo esc_attr( $post->ID ); ?>" />
<?php
foreach ( $languages_available as $language ) {
	$code                = $language->getIso639();
	if ( $code === $original_language ) {
		continue;
	}
	$post_name_weglot         = get_post_meta( $post->ID, sprintf( '%s_%s', Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $code ), true );
	$post_name_weglot_default = ( empty( $post_name_weglot ) ) ? $post->post_name : $post_name_weglot; ?>
	<label for="lang-<?php echo esc_attr( $code ); ?>">
		<strong><?php echo esc_attr( $language->getLocalName() ); ?></strong>
	</label>
	<p>
		<?php echo esc_url( home_url() ); ?>/<?php echo esc_attr( $code ); ?>/<span id="text-edit-<?php echo esc_attr( $code ); ?>"><?php echo esc_attr( $post_name_weglot_default ); ?></span>
		<input type="text" id="lang-<?php echo esc_attr( $code ); ?>" name="post_name_weglot[<?php echo esc_attr( $code ); ?>]" value="<?php echo esc_attr( $post_name_weglot ); ?>" style="display:none;"/>

		<button type="button" class="button button-small button-weglot-lang" data-lang="<?php echo esc_attr( $code ); ?>" aria-label="Edit permalink weglot"><?php esc_html_e( 'Edit', 'weglot' ); ?></button>

		<button type="button" class="button button-small button-weglot-lang-submit" data-lang="<?php echo esc_attr( $code ); ?>" style="display:none;"><?php esc_html_e( 'Ok', 'weglot' ); ?></button>

		<p id="weglot_permalink_not_available_<?php echo esc_attr( $code ); ?>" class="weglot_text_error" style="display:none;"><?php esc_html_e( 'The permalink is not available.', 'weglot' ); ?></p>
	</p>

	<?php
}
