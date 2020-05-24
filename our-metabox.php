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
		add_action( 'save_post', array( $this, 'omb_save_metabox' ) );
	}

	public function omb_load_textdomain() {
		load_plugin_textdomain( 'our-metabox', false, plugin_dir_url( __FILE__ ) . '/languages' );
	}


	public function omb_add_metabox() {
		add_meta_box(
			'omb_post_location',
			__( 'Location Info', 'our-metabox' ),
			array( $this, 'omb_display_metabox' ),
			array( 'post', 'page' ),
			'normal',
			'default'
		);
	}

	public function omb_display_metabox( $post ) {
		$location     = get_post_meta( $post->ID, 'omb_location', true );
		$country      = get_post_meta( $post->ID, 'omb_country', true );
		$is_favorite  = get_post_meta( $post->ID, 'omb_is_favorite', true );
		$saved_colors = get_post_meta( $post->ID, 'omb_clr', true );
		$saved_sport  = get_post_meta( $post->ID, 'omb_sport', true );
		$checked      = $is_favorite == 1 ? 'checked' : '';
		$label1       = __( 'Location', 'our-metabox' );
		$label2       = __( 'Country', 'our-metabox' );
		$label3       = __( 'Is Favorite', 'our-metabox' );
		$label4       = __( 'Colors', 'our-metabox' );
		$label5       = __( 'Sports', 'our-metabox' );
		$colors       = array( 'red', 'blue', 'green', 'yellow', 'magenta', 'pink', 'black' );
		$sports       = array( 'Cricked', 'Football', 'Basket Ball', 'Athletics', 'Swimming' );
		wp_nonce_field( 'omb_location', 'omb_location_field' );
		$metabox_html = <<<EOD
<p>
	<label for="omb_location">{$label1}: </label>
	<input type="text" name="omb_location" id="omb_location" value="{$location}">
</p>
<p>
	<label for="omb_country">{$label2}: </label>
	<input type="text" name="omb_country" id="omb_country" value="{$country}">
</p>
<p>
	<label for="omb_country">{$label3}: </label>
	<input type="checkbox" name="omb_is_favorite" id="omb_country" value="1" {$checked}>
</p>
<p>
	<label>{$label4}: </label>
EOD;


		foreach ( $colors as $color ) {
			$_color       = ucwords( $color );
			$checked      = in_array( $color, $saved_colors ) ? 'checked' : '';
			$metabox_html .= <<<EOD
<label for="omb_clr_{$color}">{$_color} </label>
<input type="checkbox" name="omb_clr[]" id="omb_clr_{$color}" value="{$color}" {$checked} />
EOD;

		}
		$metabox_html .= "</p>";


		$metabox_html .= <<<EOD
<p>
	<label>{$label5}: </label>
EOD;
		foreach ( $sports as $sport ) {
			$_sport       = ucwords( $sport );
			$checked      = ( $saved_sport == $sport ) ? 'checked="checked"' : '';
			$metabox_html .= <<<EOD
<label for="omb_sport_{$sport}">{$_sport}</label>
<input type="radio" name="omb_sport" id="omb_sport_{$sport}" value="{$sport}" {$checked} />
EOD;

		}

		$metabox_html .= "</p>";


		echo $metabox_html;

	}


	public function omb_save_metabox( $post_id ) {
		if ( ! $this->is_sercured( 'omb_location_field', 'omb_location', $post_id ) ) {
			return $post_id;
		}
		$location    = isset( $_POST['omb_location'] ) ? $_POST['omb_location'] : '';
		$country     = isset( $_POST['omb_country'] ) ? $_POST['omb_country'] : '';
		$is_favorite = isset( $_POST['omb_is_favorite'] ) ? $_POST['omb_is_favorite'] : 0;
		$colors      = isset( $_POST['omb_clr'] ) ? $_POST['omb_clr'] : array();
		$sport       = isset( $_POST['omb_sport'] ) ? $_POST['omb_sport'] : '';
		if ( '' == $location || '' == $country ) {
			return $post_id;
		}
		$location = sanitize_text_field( $location );
		$country  = sanitize_text_field( $country );
		$sport    = sanitize_text_field( $sport );
		update_post_meta( $post_id, 'omb_location', $location );
		update_post_meta( $post_id, 'omb_country', $country );
		update_post_meta( $post_id, 'omb_is_favorite', $is_favorite );
		update_post_meta( $post_id, 'omb_clr', $colors );
		update_post_meta( $post_id, 'omb_sport', $sport );
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