<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;

/**
 *
 * @since 2.0
 *
 */
class Customize_Menu_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->language_services          = weglot_get_service( 'Language_Service_Weglot' );
		$this->option_services            = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services       = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->button_services            = weglot_get_service( 'Button_Service_Weglot' );
		$this->private_language_services  = weglot_get_service( 'Private_Language_Service_Weglot' );
		$this->menu_options_services      = weglot_get_service( 'Menu_Options_Service_Weglot' );
		return $this;
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		if ( ! $this->option_services->get_option( 'allowed' ) ) {
			return;
		}

		add_action( 'admin_head-nav-menus.php', [ $this, 'add_nav_menu_meta_boxes' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'nav_admin_enqueue_scripts' ] );
		add_action( 'wp_update_nav_menu_item', [ $this, 'custom_wp_update_nav_menu_item' ], 10, 2 );
		// add_filter( 'nav_menu_link_attributes', [ $this, 'add_nav_menu_link_attributes' ], 10, 2 );
		// add_filter( 'nav_menu_css_class', [ $this, 'add_nav_menu_css_class' ], 10, 2 );

		add_filter( 'megamenu_nav_menu_css_class', [ $this, 'add_nav_menu_css_class' ], 10, 2 );

		if ( $this->option_services->get_option( 'is_menu' ) ) {
			add_filter( 'wp_nav_menu_items', [ $this, 'weglot_fallback_menu' ] );
		}
	}

	/**
	 * @since 2.0
	 * @param string $items
	 * @return string
	 */
	public function weglot_fallback_menu( $items ) {
		$button = $this->button_services->get_html();
		$items .= $button;

		return $items;
	}

	/**
	 * @since 2.4.0
	 * @param int $menu_id
	 * @param int $menu_item_db_id
	 * @return void
	 */
	public function custom_wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0 ) {
		if ( empty( $_POST['menu-item-url'][ $menu_item_db_id ] ) || '#weglot_switcher' != $_POST['menu-item-url'][ $menu_item_db_id ] ) {
			return;
		}

		var_dump($_POST);
		var_dump($menu_id);
		var_dump($menu_item_db_id);
		die;

		// Security check as 'wp_update_nav_menu_item' can be called from outside WP admin
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		$menu_options = array( 'hide_if_no_translation' => 0, 'hide_current' => 0, 'force_home' => 0, 'show_flags' => 0, 'show_names' => 1, 'dropdown' => 0 ); // Default values
		// Our jQuery form has not been displayed
		if ( empty( $_POST['menu-item-pll-detect'][ $menu_item_db_id ] ) ) {
			if ( ! get_post_meta( $menu_item_db_id, '_pll_menu_item', true ) ) { // Our options were never saved
				update_post_meta( $menu_item_db_id, '_pll_menu_item', $options );
			}
		} else {
			foreach ( $options as $opt => $v ) {
				$options[ $opt ] = empty( $_POST[ 'menu-item-' . $opt ][ $menu_item_db_id ] ) ? 0 : 1;
			}
			update_post_meta( $menu_item_db_id, '_pll_menu_item', $options ); // Allow us to easily identify our nav menu item
		}
	}

	/**
	 * @since 2.0
	 * @return void
	 */
	public function nav_admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'nav-menus' !== $screen->base ) {
			return;
		}

		wp_enqueue_script( 'weglot_nav_menu', WEGLOT_URL_DIST . '/nav-js.js', [ 'jquery' ], WEGLOT_VERSION );

		$data['title']             = __( 'Weglot switcher', 'weglot' ); // The title
		$data['options']           = $this->option_services->get_option( 'menu_switcher');
		$data['list_options']      = $this->menu_options_services->get_list_options_menu_switcher();

		wp_localize_script( 'weglot_nav_menu', 'weglot_data', $data );
	}


	/**
	 * @since 2.0
	 * @version 2.0.2
	 * @see nav_menu_link_attributes
	 * @param array $classes
	 * @param object $item
	 * @return void
	 */
	public function add_nav_menu_css_class( $classes, $item ) {
		// $str              = 'weglot_menu_title-';
		// if ( strpos( $item->post_name, $str ) !== false ) {
		// 	$lang = explode( '-', substr( $item->post_name, strlen( $str ) ) );

		// 	if ( ! $this->request_url_services->is_translatable_url() || ! weglot_current_url_is_eligible() || $this->private_language_services->is_active_private_mode_for_lang( $lang[0] ) ) {
		// 		$classes[] = apply_filters( 'weglot_nav_menu_link_class', 'weglot-hide' );
		// 		return $classes;
		// 	}

		// 	$options      = $this->option_services->get_options();
		// 	$with_flags   = $options['with_flags'];
		// 	$type_flags   = $options['type_flags'];

		// 	$flag_class   = $with_flags ? 'weglot-flags ' : '';
		// 	$flag_class .= '0' === $type_flags ? '' : 'flag-' . $type_flags . ' ';

		// 	$classes[] = apply_filters( 'weglot_nav_menu_link_class', $flag_class . $lang[0] );
		// }

		return $classes;
	}

	/**
	 * @since 2.0
	 * @see nav_menu_link_attributes
	 * @param array $attrs
	 * @param object $item
	 * @return void
	 */
	public function add_nav_menu_link_attributes( $attrs, $item ) {
		$str              = 'weglot_menu_title-';
		// if ( strpos( $item->post_name, $str ) !== false ) {
		// 	$current_language = $this->request_url_services->get_current_language();

		// 	if ( ! $this->request_url_services->is_translatable_url() || ! weglot_current_url_is_eligible() ) {
		// 		$attrs['style'] = 'display:none';
		// 		return $attrs;
		// 	}

		// 	if ( ! isset( $attrs['class'] ) ) {
		// 		$attrs['class'] = '';
		// 	}

		// 	$attrs['class'] .= ' weglot-lang';

		// 	$attrs['data-wg-notranslate'] = 'true';
		// }

		return $attrs;
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'weglot_nav_link', __( 'Weglot switcher', 'weglot' ), [ $this, 'nav_menu_links' ], 'nav-menus', 'side', 'low' );
	}

	/**
	 * Output menu links.
	 * @since 2.0
	 * @see add_meta_box weglot_nav_link
	 */
	public function nav_menu_links() {
		global $_nav_menu_placeholder, $nav_menu_selected_id; ?>
		<div id="posttype-weglot-languages" class="posttypediv">
			<div id="tabs-panel-weglot-endpoints" class="tabs-panel tabs-panel-active">
				<ul id="weglot-endpoints-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="<?php echo $_nav_menu_placeholder; ?>" /> <?php esc_html_e( 'Weglot Switcher', 'weglot' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="custom" />
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php esc_html_e( 'Weglot Switcher', 'weglot' ); ?>" />
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#weglot_switcher" />
						<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-classes]" />
					</li>
				</ul>
			</div>
			<p class="button-controls">
				<span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'weglot' ); ?>" name="add-post-type-menu-item" id="submit-posttype-weglot-languages"><?php esc_attr_e( 'Add to Menu' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}
}

