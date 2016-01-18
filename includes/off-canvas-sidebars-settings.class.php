<?php
/**
 * Off-Canvas Sidebars plugin settings
 *
 * Settings
 * @author Jory Hogeveen <info@keraweb.nl>
 * @package off-canvas-slidebars
 * @version 0.1
 */

! defined( 'ABSPATH' ) and die( 'You shall not pass!' );

class OCS_Off_Canvas_Sidebars_Settings {
	
	private $general_key = '';
	private $plugin_key = '';
	private $plugin_tabs = array();
	private $general_settings = array();
	private $general_labels = array();

	function __construct() {
		add_action( 'admin_init', array( $this, 'load_plugin_data' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'register_importexport_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ), 15 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
	}

	/**
	 * Get plugin defaults
	 */
	function load_plugin_data() {
		global $off_canvas_sidebars;
		$this->general_settings = $off_canvas_sidebars->get_settings();
		$this->general_labels = $off_canvas_sidebars->get_general_labels();
		$this->general_key = $off_canvas_sidebars->get_general_key();
		$this->plugin_key = $off_canvas_sidebars->get_plugin_key();
	}
	
	function enqueue_styles_scripts( $hook ) {
		if ( $hook != 'appearance_page_off-canvas-sidebars-settings' ) {
			return;
		}
        // Add the color picker css and script file
        wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_script( 'wp-color-picker' );
	}

	function register_settings() {
		$this->plugin_tabs[$this->general_key] = esc_attr__( 'Off-Canvas Sidebars Settings', 'off-canvas-sidebars' );
		
		register_setting( $this->general_key, $this->general_key, array( $this, 'validate_input' ) );
		
		add_settings_section( 'section_general', esc_attr__( 'Off-Canvas Sidebars Settings', 'off-canvas-sidebars' ), array( $this, 'register_general_settings' ), $this->general_key );
		
		// Register sidebar settings
		foreach ($this->general_settings['sidebars'] as $sidebar => $sidebar_data) {
			add_settings_section( 'section_sidebar_'.$sidebar, esc_attr__( 'Off-Canvas '.$this->general_labels['sidebars'][$sidebar]['label'], 'off-canvas-sidebars' ), '', $this->general_key );
			$this->register_sidebar_settings( $sidebar );
		}
		
		do_action( 'off_canvas_sidebar_settings' );
	}

	function register_general_settings() {
		add_settings_field( 
			'enable_frontend', 
			esc_attr__( 'Enable front-end', 'off-canvas-sidebars' ), 
			array( $this, 'checkbox_option' ), 
			$this->general_key, 
			'section_general' ,
			array( 
				'name' => 'enable_frontend', 
				'label' => __( 'Let this plugin add the necessary elements on the front-end.', 'off-canvas-sidebars' ),
				'description' => sprintf( __( '<a href="%s" target="_blank">Read this to setup your theme for support!</a> (Themes based on the Genesis Framework are supported by default)', 'off-canvas-sidebars' ), 'https://wordpress.org/plugins/off-canvas-sidebars/installation/' )
			) 
		);
		add_settings_field( 
			'enabled_sidebars', 
			esc_attr__( 'Enable Sidebars', 'off-canvas-sidebars' ), 
			array( $this, 'enabled_sidebars_option' ), 
			$this->general_key, 
			'section_general' 
		);
		add_settings_field( 
			'site_close', 
			esc_attr__( 'Close sidebar when clicking on the site', 'off-canvas-sidebars' ), 
			array( $this, 'checkbox_option' ), 
			$this->general_key, 
			'section_general', 
			array( 'name' => 'site_close', 'label' => __( 'Enables closing of a off-canvas sidebar by clicking on the site. Default: true.', 'off-canvas-sidebars' ) ) 
		);
		add_settings_field( 
			'disable_over', 
			esc_attr__( 'Disable over', 'off-canvas-sidebars' ), 
			array( $this, 'number_option' ), 
			$this->general_key, 
			'section_general', 
			array( 'name' => 'disable_over', 'description' => __( 'Disable off-canvas sidebars over specified screen width. Leave blank to disable.', 'off-canvas-sidebars' ) ) 
		);
		add_settings_field( 
			'hide_control_classes', 
			esc_attr__( 'Auto-hide control classes', 'off-canvas-sidebars' ), 
			array( $this, 'checkbox_option' ), 
			$this->general_key, 'section_general', 
			array( 'name' => 'hide_control_classes', 'label' => __( 'Hide off-canvas sidebar control classes over width specified in <strong>"Disable over"</strong>. Default: false.', 'off-canvas-sidebars' ) ) 
		);
		add_settings_field( 
			'scroll_lock', 
			esc_attr__( 'Scroll lock', 'off-canvas-sidebars' ), 
			array( $this, 'checkbox_option' ), 
			$this->general_key, 
			'section_general', 
			array( 'name' => 'scroll_lock', 'label' => __( 'Prevent site content scrolling whilst a off-canvas sidebar is open. Default: false.', 'off-canvas-sidebars' ) ) 
		);
		add_settings_field( 
			'background_color', 
			esc_attr__( 'Background color', 'off-canvas-sidebars' ), 
			array( $this, 'color_option' ), 
			$this->general_key, 
			'section_general', 
			array( 'name' => 'background_color', 'description' => __( 'Choose a background color for the site container. Default: <code>#ffffff</code>.', 'off-canvas-sidebars' ) ) 
		);
	}
	
	function register_sidebar_settings( $sidebar ) {
		add_settings_field( 
			'sidebar_width', 
			esc_attr__( 'Width', 'off-canvas-sidebars' ), 
			array( $this, 'sidebar_width' ), 
			$this->general_key, 
			'section_sidebar_' . $sidebar, 
			array( 'sidebar' => $sidebar ) 
		);
		add_settings_field( 
			'sidebar_style', 
			esc_attr__( 'Style', 'off-canvas-sidebars' ), 
			array( $this, 'sidebar_style' ), 
			$this->general_key, 
			'section_sidebar_' . $sidebar, 
			array( 'sidebar' => $sidebar ) 
		);
		add_settings_field( 
			'background_color', 
			esc_attr__( 'Background color', 'off-canvas-sidebars' ), 
			array( $this, 'color_option' ), 
			$this->general_key, 
			'section_sidebar_' . $sidebar, 
			array( 'sidebar' => $sidebar, 'name' => 'background_color', 'description' => __( 'Choose a background color for this sidebar. Default: <code>#222222</code>.', 'off-canvas-sidebars' ) ) 
		);
	}
	
	function enabled_sidebars_option() {
		?><fieldset><?php
		$prefixName = esc_attr( $this->general_key ).'[sidebars]';
		$prefixValue = $this->general_settings['sidebars'];
		$prefixId = $this->general_key.'_sidebars';
		foreach ($prefixValue as $sidebar => $sidebar_data) {
		?>
		<label><input type="checkbox" name="<?php echo $prefixName.'['.$sidebar.'][enable]'; ?>" id="<?php echo $prefixId.'_enable_'.$sidebar; ?>" value="1" <?php checked( $prefixValue[$sidebar]['enable'], 1 ); ?> /> <?php echo $this->general_labels['sidebars'][$sidebar]['label']; ?></label><br />
	<?php }
		?></fieldset><?php
	}
	
	function sidebar_width( $args ) {
		$prefixes = $this->get_option_prefixes( $args );
		$prefixName = $prefixes['prefixName'];
		$prefixValue = $prefixes['prefixValue'];
		$prefixId = $prefixes['prefixId'];
		if ( isset( $args['sidebar'] ) ) {
		?><fieldset>
            <label><input type="radio" name="<?php echo $prefixName.'[width]'; ?>" id="<?php echo $prefixId.'_width_default'; ?>" value="default" <?php checked( $prefixValue['width'], 'default' ); ?> /> <?php _e( 'Default', 'off-canvas-sidebars' ); ?></label><br />
            <label><input type="radio" name="<?php echo $prefixName.'[width]'; ?>" id="<?php echo $prefixId.'_width_thin'; ?>" value="thin" <?php checked( $prefixValue['width'], 'thin' ); ?> /> <?php _e( 'Thin', 'off-canvas-sidebars' ); ?></label><br />
            <label><input type="radio" name="<?php echo $prefixName.'[width]'; ?>" id="<?php echo $prefixId.'_width_wide'; ?>" value="wide" <?php checked( $prefixValue['width'], 'wide' ); ?> /> <?php _e( 'Wide', 'off-canvas-sidebars' ); ?></label><br />
            <label><input type="radio" name="<?php echo $prefixName.'[width]'; ?>" id="<?php echo $prefixId.'_width_custom'; ?>" value="custom" <?php checked( $prefixValue['width'], 'custom' ); ?> /> <?php _e( 'Custom', 'off-canvas-sidebars' ); ?></label>: 
            <div style="display: inline-block; vertical-align:top">
                <input type="number" name="<?php echo $prefixName.'[width_input]'; ?>" min="1" max="" step="1" value="<?php echo $prefixValue['width_input'] ?>" /> 
                <select name="<?php echo $prefixName.'[width_input_type]'; ?>">
                    <option value="%" <?php selected( $prefixValue['width_input_type'], '%' ); ?>>%</option>
                    <option value="px" <?php selected( $prefixValue['width_input_type'], 'px' ); ?>>px</option>
                </select>
            </div>
        </fieldset><?php
		}
	}
	
	function sidebar_style( $args ) {
		$prefixes = $this->get_option_prefixes( $args );
		$prefixName = $prefixes['prefixName'];
		$prefixValue = $prefixes['prefixValue'];
		$prefixId = $prefixes['prefixId'];
		if ( isset( $args['sidebar'] ) ) {
		?><fieldset>
			<label><input type="radio" name="<?php echo $prefixName.'[style]'; ?>" id="<?php echo $prefixId.'_style_push'; ?>" value="push" <?php checked( $prefixValue['style'], 'push' ); ?> /> <?php _e( 'Sidebar pushes the site across when opened.', 'off-canvas-sidebars' ); ?></label><br />
			<label><input type="radio" name="<?php echo $prefixName.'[style]'; ?>" id="<?php echo $prefixId.'_style_overlay'; ?>" value="overlay" <?php checked( $prefixValue['style'], 'overlay' ); ?> /> <?php _e( 'Sidebar overlays the site when opened.', 'off-canvas-sidebars' ); ?></label>
		</fieldset><?php
		}
	}

	function checkbox_option( $args ) {
		$prefixes = $this->get_option_prefixes( $args );
		$prefixName = $prefixes['prefixName'];
		$prefixValue = $prefixes['prefixValue'];
		$prefixId = $prefixes['prefixId'];
		if ( isset( $args['name'] ) ) {
		?><fieldset>
			<?php if ( isset( $args['label'] ) ) { ?><label><?php } ?>
            <input type="checkbox" name="<?php echo $prefixName.'['.$args['name'].']'; ?>" id="<?php echo $prefixId.'_'.$args['name']; ?>" value="1" <?php checked( $prefixValue[$args['name']], 1 ); ?> /> 
			<?php if ( isset( $args['label'] ) ) { echo $args['label'] ?></label><?php } ?>
			<?php if ( isset( $args['description'] ) ) { ?>
			<p class="description"><?php echo $args['description'] ?></p>
			<?php } ?>
		</fieldset><?php
		}
	}

	function number_option( $args ) {
		$prefixes = $this->get_option_prefixes( $args );
		$prefixName = $prefixes['prefixName'];
		$prefixValue = $prefixes['prefixValue'];
		$prefixId = $prefixes['prefixId'];
		if ( isset( $args['name'] ) ) {
		?><fieldset>
            <input type="number" id="<?php echo $prefixId.'_'.$args['name']; ?>" name="<?php echo $prefixName.'['.$args['name'].']'; ?>" value="<?php echo $prefixValue[$args['name']] ?>" min="1" max="" step="1" /> px&nbsp;&nbsp;
            <?php if ( isset( $args['description'] ) ) { ?>
            <p class="description"><?php echo $args['description'] ?></p>
            <?php } ?>
		</fieldset><?php
		}
	}

	function color_option( $args ) {
		$prefixes = $this->get_option_prefixes( $args );
		$prefixName = $prefixes['prefixName'];
		$prefixValue = $prefixes['prefixValue'];
		$prefixId = $prefixes['prefixId'];
		if ( isset( $args['name'] ) ) {
		?><fieldset>
			<label><input type="radio" name="<?php echo $prefixName.'['.$args['name'].'_type]'; ?>" class="<?php echo $prefixId.'_background_color_type'; ?>" id="<?php echo $prefixId.'_background_color_type_theme'; ?>" value="" <?php checked( $prefixValue[$args['name'].'_type'], '' ); ?> /> <?php _e( 'Default', 'off-canvas-sidebars' ); ?></label> <span class="description">(<?php _e( 'Overwritable with CSS', 'off-canvas-sidebars' ); ?>)</span><br />
			<label><input type="radio" name="<?php echo $prefixName.'['.$args['name'].'_type]'; ?>" class="<?php echo $prefixId.'_background_color_type'; ?>" id="<?php echo $prefixId.'_background_color_type_transparent'; ?>" value="transparent" <?php checked( $prefixValue[$args['name'].'_type'], 'transparent' ); ?> /> <?php _e( 'Transparent', 'off-canvas-sidebars' ); ?></label><br />
			<label><input type="radio" name="<?php echo $prefixName.'['.$args['name'].'_type]'; ?>" class="<?php echo $prefixId.'_background_color_type'; ?>" id="<?php echo $prefixId.'_background_color_type_color'; ?>" value="color" <?php checked( $prefixValue[$args['name'].'_type'], 'color' ); ?> /> <?php _e( 'Color', 'off-canvas-sidebars' ); ?></label><br />
            <div class="<?php echo $prefixId.'_'.$args['name'].'_wrapper'; ?>">
            <input type="text" class="color-picker" id="<?php echo $prefixId.'_'.$args['name']; ?>" name="<?php echo $prefixName.'['.$args['name'].']'; ?>" value="<?php echo $prefixValue[$args['name']] ?>" />
            </div>
            <?php if ( isset( $args['description'] ) ) { ?>
            <p class="description"><?php echo $args['description'] ?></p>
            <?php } ?>
		</fieldset><?php
		}
	}
	
	/**
	 * Returns attribute prefixes for general settings and sidebar settings
	 *
	 * @since 0.1
	 *
	 * @param array $args Arguments from the settings field
	 * @return array $prefixes Prefixes for name, value and id attributes
	 */
	function get_option_prefixes( $args ) {
		if ( isset( $args['sidebar'] ) ) {
			$prefixName = esc_attr( $this->general_key ).'[sidebars]['.$args['sidebar'].']';
			$prefixValue = $this->general_settings['sidebars'][$args['sidebar']];
			$prefixId = $this->general_key.'_sidebars_'.$args['sidebar'];
		} else {
			$prefixName = esc_attr( $this->general_key );
			$prefixValue = $this->general_settings;
			$prefixId = $this->general_key;
		}
		return array( 'prefixName' => $prefixName, 'prefixValue' => $prefixValue, 'prefixId' => $prefixId );
	}
	
	/**
	 * Validates post values
	 *
	 * @since 0.1
	 *
	 * @param array $input
	 * @return array $output
	 */
	function validate_input( $input ) {
		$output = array();
		// First set default values
		$output = $this->general_settings;
		
		// Make sure unchecked checkboxes are 0 on save
		foreach ( $output['sidebars'] as $key => $value ) {
			$output['sidebars'][$key]['enable'] = ( ! empty( $input['sidebars'][$key]['enable'] ) ) ? strip_tags( $input['sidebars'][$key]['enable'] ) : '0';
		}
		
		$output['enable_frontend'] 		= ( ! empty( $input['enable_frontend'] ) ) 			? strip_tags( $input['enable_frontend'] ) 		: '0';
		$output['site_close'] 			= ( ! empty( $input['site_close'] ) ) 				? strip_tags( $input['site_close'] ) 			: '0';
		$output['hide_control_classes'] = ( ! empty( $input['hide_control_classes'] ) ) 	? strip_tags( $input['hide_control_classes'] ) 	: '0';
		$output['scroll_lock'] 			= ( ! empty( $input['scroll_lock'] ) ) 				? strip_tags( $input['scroll_lock'] ) 			: '0';
		
		// Allow 3 level arrays
		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $input[$key] as $key2 => $value2 ) {
					if ( is_array( $value2 ) ) {
						foreach ( $input[$key][$key2] as $key3 => $value3 ) {
							$output[$key][$key2][$key3] = strip_tags( stripslashes( $input[$key][$key2][$key3] ) );
						}
					} else {
						$output[$key][$key2] = strip_tags( stripslashes( $input[$key][$key2] ) );
					}
				}
			} else {
				$output[$key] = strip_tags( stripslashes( $input[$key] ) );
			}
		}

		return $output;
	}

	/**
	 * Create admin menu page
	 * @since 0.1
	 */
	function add_admin_menus() {
		add_submenu_page( 'themes.php', esc_attr__( 'Off-Canvas Sidebars', 'off-canvas-sidebars' ), esc_attr__( 'Off-Canvas Sidebars', 'off-canvas-sidebars' ), 'edit_theme_options', 'off-canvas-sidebars-settings', array( $this, 'plugin_options_page' ) );
	}

	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 *
	 * @since 0.1
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_key;
		?>
	<div class="wrap">
		<?php $this->plugin_options_tabs(); ?>
		<form method="post" action="options.php" enctype="multipart/form-data">
        	<div class="metabox-holder"><div class="container">
			<?php wp_nonce_field( 'update-options' ); ?>
            <?php if ( $tab != 'importexport' ) { ?>
			<p><?php echo sprintf( __('You can add the control buttons with a widget, menu item or with custom code, <a href="%s" target="_blank">click here for documentation.</a>', 'off-canvas-sidebars' ), 'http://plugins.adchsm.me/slidebars/usage.php' ); ?></p>
			<p><?php echo $this->general_labels['compatibility_notice_theme']; ?></p>
            <?php } ?>
            <div id="" class="">
			<?php settings_fields( $tab ); ?>
			<?php $this->do_settings_sections( $tab ); ?>
            </div>
			<?php if ( $tab == 'importexport' ) $this->importexport_fields(); ?>
			<?php if ( $tab != 'importexport' ) submit_button(); ?>
            </div></div>
		</form>
		<script type="text/javascript">
		<!--
			jQuery(document).ready(function($){
				<?php foreach ($this->general_settings['sidebars'] as $sidebar => $sidebar_data) { ?>
				gocs_show_hide_options('off_canvas_sidebars_options_sidebars_enable_<?php echo $sidebar; ?>', 'section_sidebar_<?php echo $sidebar; ?>');
				gocs_show_hide_options('off_canvas_sidebars_options_sidebars_<?php echo $sidebar; ?>_background_color_type_color', 'off_canvas_sidebars_options_sidebars_<?php echo $sidebar; ?>_background_color_wrapper');
				<?php } ?>
				gocs_show_hide_options('off_canvas_sidebars_options_background_color_type_color', 'off_canvas_sidebars_options_background_color_wrapper');
				
				function gocs_show_hide_options(trigger, target) {
					if (!$('#'+trigger).is(':checked')) {
						$('.'+target).slideUp('fast');				
					}
					$('#'+trigger).bind('change', function() {
						if ($(this).is(':checked')) {
							$('.'+target).slideDown('fast');				
						} else {
							$('.'+target).slideUp('fast');				
						}
					});
				}
				
				$('input.color-picker').wpColorPicker();
			});
		-->
		</script>
	</div>
	<?php
		//add_action( 'in_admin_footer', array( 'OCS_Lib', 'admin_footer' ) );
	}
	
	/**
	 * This function is similar to the function in the Settings API, only the output HTML is changed.
	 * Print out the settings fields for a particular settings section
	 *
	 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
	 *
	 * @since 0.1
	 *
	 * @param string $page Slug title of the admin page who's settings fields you want to show.
	 * @param string $section Slug title of the settings section who's fields you want to show.
	 */
	function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
	 
		if ( ! isset( $wp_settings_sections[$page] ) )
			return;
	 
		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			echo '<div id="" class="stuffbox '.$section['id'].'">';
			if ( $section['title'] )
				echo "<h3 class=\"hndle\"><span>{$section['title']}</span></h3>\n";
	 
			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );
	 
			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;
			echo '<div class="inside"><table class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table></div>';
			echo '</div>';
		}
	}

	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 *
	 * @since 0.1
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_key;

		echo '<h1 class="nav-tab-wrapper">';
		foreach ( $this->plugin_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.esc_attr( $active ).'" href="?page='.esc_attr( $this->plugin_key ).'&amp;tab='.esc_attr( $tab_key ).'">'.esc_html( $tab_caption ).'</a>';
		}
		echo '</h1>';
	}
	
	function importexport_fields() {
		?>
	<h3><?php _e( 'Import/Export Settings', 'off-canvas-sidebars' ); ?></h3>

	<p><a class="submit button" href="?off-canvas-sidebars-settings-export"><?php esc_attr_e( 'Export Settings', 'off-canvas-sidebars' ); ?></a></p>

	<p>
		<input type="hidden" name="off-canvas-sidebars-settings-import" id="off-canvas-sidebars-settings-import" value="true" />
		<?php submit_button( esc_attr__( 'Import Settings', 'off-canvas-sidebars' ), 'button', 'off-canvas-sidebars-settings-submit', false ); ?>
		<input type="file" name="off-canvas-sidebars-settings-import-file" id="off-canvas-sidebars-settings-import-file" />
	</p>

	<?php
	}

	function register_importexport_settings() {
		$this->plugin_tabs['importexport'] = esc_attr__( 'Import/Export', 'off-canvas-sidebars' );

		if ( isset( $_GET['gocs_message'] ) ) {
			switch ( $_GET['gocs_message'] ) {
				case 1:
					$gocs_message_class = 'updated';
					$gocs_message = esc_attr__( 'Settings Imported', 'off-canvas-sidebars' );
					break;
				case 2:
					$gocs_message_class = 'error';
					$gocs_message = esc_attr__( 'Invalid Settings File', 'off-canvas-sidebars' );
					break;
				case 3:
					$gocs_message_class = 'error';
					$gocs_message = esc_attr__( 'No Settings File Selected', 'off-canvas-sidebars' );
					break;
				default:
					$gocs_message_class = '';
					$gocs_message = '';
					break;
			}
		}

		if ( isset( $gocs_message ) && $gocs_message != '' ) {
			echo '<div class="' . $gocs_message_class . '"><p>'.esc_html( $gocs_message ).'</p></div>';
		}

		// export settings
		if ( isset( $_GET['off-canvas-sidebars-settings-export'] ) ) {
			header( "Content-Disposition: attachment; filename=off-canvas-sidebars-settings.txt" );
			header( 'Content-Type: text/plain; charset=utf-8' );
			$general = $this->general_settings;

			echo "[START=OCS SETTINGS]\n";
			foreach ( $general as $id => $text )
				echo "$id\t".json_encode( $text )."\n";
			echo "[STOP=OCS SETTINGS]";
			exit;
		}

		// import settings
		if ( isset( $_POST['off-canvas-sidebars-settings-import'] ) ) {
			$gocs_message = '';
			if ( $_FILES['off-canvas-sidebars-settings-import-file']['tmp_name'] ) {
				$import = explode( "\n", file_get_contents( $_FILES['off-canvas-sidebars-settings-import-file']['tmp_name'] ) );
				if ( array_shift( $import ) == "[START=OCS SETTINGS]" && array_pop( $import ) == "[STOP=OCS SETTINGS]" ) {
					foreach ( $import as $import_option ) {
						list( $key, $value ) = explode( "\t", $import_option );
						$options[$key] = json_decode( sanitize_text_field( $value ), true );
					}
					update_option( $this->general_key, $options );
					$gocs_message = 1;
				} else {
					$gocs_message = 2;
				}
			} else {
				$gocs_message = 3;
			}

			wp_redirect( admin_url( '/themes.php?page=off-canvas-sidebars-settings&tab=importexport&gocs_message='.esc_attr( $gocs_message ) ) );
			exit;
		}
	}

} // end class