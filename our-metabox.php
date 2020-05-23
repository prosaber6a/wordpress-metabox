<?php
/**
 * Plugin Name: Our Metabox
 * Plugin URI: http://saberhr.com
 * Author: SaberHR
 * Author URI: http://saberhr.com
 * Description: Metabox API Demo
 * Licence: GPLv2 or Later
 * Text Domain: our-metabox
 */


class OurMetabox {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'omb_load_textdomain' ) );
		add_action( 'admin_menu', array( $this, 'omb_add_metabox' ) );
		add_action( 'save_post', array( $this, 'omb_save_location' ) );
	}

	public function omb_load_textdomain() {
		load_plugin_textdomain( 'our-metabox', false, plugin_dir_url( __FILE__ ) . '/languages' );
	}


	public function omb_add_metabox() {
		add_meta_box(
			'omb_post_location',
			__( 'Location Info', 'our-metabox' ),
			array( $this, 'omb_display_post_location' ),
			'post',
			'normal',
			'default'
		);
	}

	public function omb_display_post_location( $post ) {
		$location = get_post_meta( $post->ID, 'omb_location', true );
		$label    = __( 'Location', 'our-metabox' );
		wp_nonce_field( 'omb_location', 'omb_location_field' );
		$metabox_html = <<<EOD
<p>
	<label for="omb_location">{$label}</label>
	<input type="text" name="omb_location" id="omb_location" value="{$location}">
</p>
EOD;
		echo $metabox_html;

	}


	public function omb_save_location( $post_id ) {
		if ( ! $this->is_sercured( 'omb_location_field', 'omb_location', $post_id ) ) {
			return $post_id;
		}
		$location = isset( $_POST['omb_location'] ) ? $_POST['omb_location'] : '';
		if ( '' == $location ) {
			return $post_id;
		}
		$location = sanitize_text_field( $location );
		update_post_meta( $post_id, 'omb_location', $location );
	}


	private function is_sercured( $nonce_field, $nonce_action, $post_id ) {
		$nonce = isset( $_POST[ $nonce_field ] ) ? $_POST[ $nonce_field ] : '';

		if ( '' == $nonce ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		return true;
	}


}

new OurMetabox();