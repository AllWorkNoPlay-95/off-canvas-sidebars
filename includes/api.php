<?php
/**
 * Off-Canvas Sidebars plugin API
 *
 * API
 * @author Jory Hogeveen <info@keraweb.nl>
 * @package off-canvas-sidebars
 * @version 0.3
 */

! defined( 'ABSPATH' ) and die( 'You shall not pass!' );

/**
 * Echos one, multiple or all OCS sidebars
 *
 * @since  0.3
 * @param  string|array|bool  $sidebars  (Optional) The ID of this sidebar as configured in: Appearances > Off-Canvas Sidebars > Sidebars
 */
function the_ocs_off_canvas_sidebar( $sidebars = false ) {
	if ( $instance = Off_Canvas_Sidebars_Frontend() ) {
		if ( false === $sidebars ) {
			$instance->do_sidebars();
		} else {
			if ( is_array( $sidebars ) ) {
				foreach( $sidebars as $sidebar ) {
					$instance->do_sidebar( (string) $sidebar );
				}
			} else {
				$instance->do_sidebar( (string) $sidebars );
			}
		}
	}
}

/**
 * Output a trigger element for off-canvas sidebars
 *
 * @since  0.3.2
 * @param  array   $atts {
 *     Required array of arguments
 *     @type  string      id       (Required) The off-canvas sidebar ID.
 *     @type  string      action   The trigger action. Default: toggle
 *     @type  string      element  The HTML element. Default: button
 *     @type  int|string  button   Add a button class? 0 = no, 1 = yes. Default: 0
 *     @type  string      text     The text to show. Default: ''
 *     @type  array       attr     Other attributes to add {
 *          Format: attribute name (array key) => attribute value
 *     }
 * }
 * @param  string  $content  (Optional) $content
 * @return string
 */
function the_ocs_control_trigger( $atts, $content = '' ) {
	if ( $instance = Off_Canvas_Sidebars_Frontend() ) {

		if ( empty( $atts['id'] ) ) {
			return __( 'No Off-Canvas Sidebar ID provided.', 'off-canvas-sidebars' );
		}

		$atts = shortcode_atts( array(
			'id'      => false,
			'action'  => 'toggle', // toggle|open|close
			'element' => 'button', // button|span|i|b|a|etc.
			'button'  => 0, // 1|0
			'text'    => '', // Text to show
			'attr'    => array(), // An array of attribute keys and their values
			'echo'    => true
		), $atts, 'ocs_trigger' );

		if ( ! empty( $content ) ) {
			$atts['text'] = $content;
		}

		$return = $instance->do_control_trigger( $atts[ 'id' ], $atts );
		if ( (boolean) $atts['echo'] ) {
			echo $return;
		}
		return $return;
	}
}

/**
 * Main instance of Off-Canvas Sidebars Frontend.
 *
 * Returns the main instance of OCS_Off_Canvas_Sidebars_Frontend to prevent the need to use globals.
 *
 * @since   0.3
 * @return  OCS_Off_Canvas_Sidebars_Frontend|false
 */
function Off_Canvas_Sidebars_Frontend() {
	if ( class_exists( 'OCS_Off_Canvas_Sidebars_Frontend' ) ) {
		return OCS_Off_Canvas_Sidebars_Frontend::get_instance();
	}
	return false;
}
