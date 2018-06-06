<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

?>

<div class="wrap">
	<?php include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/nav.php'; ?>

	<form method="post" id="mainform" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
		<?php
		switch ( $this->tab_active ) {
			case Helper_Tabs_Admin_Weglot::SETTINGS:
			default:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/settings.php';
				break;
			case Helper_Tabs_Admin_Weglot::APPEARANCE:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/appearance.php';
				break;
			case Helper_Tabs_Admin_Weglot::ADVANCED:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/advanced.php';
				break;
			case Helper_Tabs_Admin_Weglot::STATUS:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/status.php';
				break;
		}

		if ( ! in_array( $this->tab_active, [ Helper_Tabs_Admin_Weglot::STATUS ], true ) ) {
			settings_fields( WEGLOT_OPTION_GROUP );
			submit_button();
		}
		?>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $this->tab_active ); ?>">
	</form>
</div>
