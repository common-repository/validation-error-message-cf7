<?php
/*
Plugin Name: Validation Error Message - CF7
Plugin URI: http://saurabhspeaks.com
Description: This plugin help you to add custom validation error message for each tag in form for the Contact form 7.
Author: Praveen Goswami
Author URI: http://saurabhspeaks.com
Version: 1.0
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,this
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* Disallow users to direct access to the plugin file */

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	die('Sorry, but you cannot access this page directly.');
}
/**
 *
 * include validation class for check CF7 is active or not.
 * @author Praveen Goswami
 *
 */
define( 'PLUGIN_DIR', dirname(__FILE__).'/' );
require_once(PLUGIN_DIR . '/include/validation.class.php');

global $ValidateCF7;
$ValidateCF7 = new ValidateCF7(); //create object for ValidateCF7 Class

register_activation_hook( __FILE__, array( 'ValidateCF7', 'activation_check' ) );
/**
 *
 * add_error_message_tab_admin_cf7 add new tab for each contact from in admin
 * @author Praveen Goswami
 *
 */
function add_error_message_tab_admin_cf7( $settings ){
		$error_tab = array(
			'Error' => array(
				'title' => __( 'Validation Error Message', 'cf-7' ),
				'callback' => 'error_msg_tab_content_cf7'
			)
		);
		$settings = array_merge($settings, $error_tab);
		return $settings;
	}

add_filter( 'wpcf7_editor_panels', 'add_error_message_tab_admin_cf7' );

/**
 *
 * error_msg_tab_content_cf7 add content area for @Validation Error tab in admin
 * @author Praveen Goswami
 *
 */
function error_msg_tab_content_cf7( $cf7 ){

	$postId = sanitize_text_field($_GET['post']);
	$objTags = $cf7->form_scan_shortcode();

	$error_tab_content = "";
	$error_tab_content .= "<form>";
	$error_tab_content .= "<div id='additional_settings-sortables' class='meta-box'><div id='additionalsettingsdiv'>";
	$error_tab_content .= "<div class='handlediv' title='Click to toggle'><br></div><h3 class='hndle ui-sortable-handle'><span>Validation Error Message Settings</span></h3>";
	$error_tab_content .= "<div class='inside'>";

	$error_tab_content .= "<div class='mail-field'>";
	$error_tab_content .= "</div>";

	$findme   = '*';
	$error_tab_content .= "<br /><table>";

	foreach ($objTags as $key => $value) {

		$pos = strpos($value['type'], $findme);

		if($pos){
			$val = "val_of".$value['name'];
			$key = "_cf7cm_".$value['name'];
			$val = get_post_meta($postId, $key, true);
			$error_tab_content .= "<tr><td>Error Message For : <strong>".str_replace('-',' ',ucfirst($value['name']))."</strong></td></tr>";
			$error_tab_content .= "<tr><td><input type='text' size='50' name='$key' value='$val'> </td></tr><tr><td>";
		}
	}
	$error_tab_content .= "<input type='hidden' name='post' value='$postId'>";

	$error_tab_content .= "</td></tr></table></form>";
	$error_tab_content .= "</div>";
	$error_tab_content .= "</div>";
	$error_tab_content .= "</div>";

	echo $error_tab_content;

}

/**
 *
 * save_error_message_cf7 hook to save contact form 7 validation error message while saving contact form
 * @author Praveen Goswami
 *
 */
add_action('wpcf7_save_contact_form', 'save_error_message_cf7');

function save_error_message_cf7( $cf7 ) {

		$objTags = $cf7->form_scan_shortcode();

		$postId = sanitize_text_field($_POST['post']);

		foreach ($objTags as $key => $value) {
				$key = "_cf7cm_".$value['name'];
				$vals = sanitize_text_field($_POST[$key]);
				update_post_meta($postId, $key, $vals);
		}
}

/**
 *
 * validation_error_msg_display_cf7 hook to display contact form 7 error message for perticiler form
 * @author Praveen Goswami
 *
 */
add_filter( 'wpcf7_validate_text*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_email', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_email*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_url', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_url*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_tel', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_tel*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_number', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_number*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_range', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_range*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_textarea*', 'validation_error_msg_display_cf7', 1, 2 );
add_filter( 'wpcf7_validate_checkbox*', 'validation_error_msg_display_cf7', 1, 2);
add_filter( 'wpcf7_validate_file*', 'validation_error_msg_display_cf7', 1, 2);

function validation_error_msg_display_cf7( $result, $tag ) {

	$objTags = new WPCF7_Shortcode( $tag );
	$postId  = sanitize_text_field($_POST['_wpcf7']);
	$name    = $objTags->name;
	$key     = "_cf7cm_".$name;
	$type    = $objTags->type;

	$val     = get_post_meta($postId, $key, true);

	$postVal = isset( $_POST[$name] )
		? trim( strtr( (string) $_POST[$name], "\n", " " ) )
		: '';

	if(empty($val)){
		$val = wpcf7_get_message('invalid_required');
	}

	if($type == 'text*' && $postVal == ''){
		$result->invalidate( $objTags, $val );
	}

	if ( 'email' == $objTags->basetype ) {
		if ( $objTags->is_required() && $postVal == '') {
			$result->invalidate( $objTags, $val );
		} elseif ( '' != $postVal && ! wpcf7_is_email( $postVal ) ) {
			$result->invalidate( $objTags, wpcf7_get_message( 'invalid_email' ) );
		}
	}

	if($type == 'textarea*' && $postVal == ''){
		$result->invalidate( $objTags, $val );
	}

	if ( 'tel' == $objTags->basetype ) {
		if($objTags->is_required() && $postVal == ''){
			$result->invalidate( $objTags, $val );
		} elseif ( $postVal != '' && ! wpcf7_is_tel( $postVal ) ) {
				$result->invalidate( $objTags, wpcf7_get_message( 'invalid_tel' ) );
			}
	}

	if ( 'url' == $objTags->basetype ) {
		if($objTags->is_required() && $postVal == ''){
			$result->invalidate( $objTags, $val );
		} elseif ( $postVal != '' && ! wpcf7_is_url( $postVal ) ) {
				$result->invalidate( $objTags, wpcf7_get_message( 'invalid_url' ) );
			}
	}

	$min = $objTags->get_option( 'min', 'signed_int', true );
  $max = $objTags->get_option( 'max', 'signed_int', true );

  if ( $objTags->is_required() && '' == $postVal ) {
      $result->invalidate( $objTags, $val );
  } elseif ( $postVal != '' && ! wpcf7_is_number( $postVal ) ) {
      $result->invalidate( $objTags, wpcf7_get_message( 'invalid_number' ) );
  } elseif ( $postVal != '' && $min != '' && (float) $postVal < (float) $min ) {
      $result->invalidate( $objTags, wpcf7_get_message( 'number_too_small' ) );
  } elseif ( $postVal != '' && $max != '' && (float) $max < (float) $postVal ) {
      $result->invalidate( $objTags, wpcf7_get_message( 'number_too_large' ) );
  }

	if($type == 'checkbox*' && $postVal == ''){
		$result->invalidate( $objTags, $val );
	}

	if($type == 'file*' && $postVal == ''){
		$result->invalidate( $objTags, $val );
	}

	return $result;
}
?>