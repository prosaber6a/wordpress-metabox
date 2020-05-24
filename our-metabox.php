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
		add_action('admin_enqueue_scripts', array($this, 'omb_admin_assets'));
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

		add_meta_box(
			'omb_book_info',
			__( 'Book Info', 'our-metabox' ),
			array( $this, 'omb_book_metabox' ),
			array( 'book' ),
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
		$saved_car    = get_post_meta( $post->ID, 'omb_car', true );

		$checked = $is_favorite == 1 ? 'checked' : '';
		$label1  = __( 'Location', 'our-metabox' );
		$label2  = __( 'Country', 'our-metabox' );
		$label3  = __( 'Is Favorite', 'our-metabox' );
		$label4  = __( 'Colors', 'our-metabox' );
		$label5  = __( 'Sports', 'our-metabox' );
		$label6  = __( 'Cars', 'our-metabox' );

		$colors = array( 'red', 'blue', 'green', 'yellow', 'magenta', 'pink', 'black' );
		$sports = array( 'Cricked', 'Football', 'Basket Ball', 'Athletics', 'Swimming' );
		$cars   = array( 'Volvo', 'Audi', 'BMW', 'RR', 'TATA' );

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

EOD;

//		Check Box
		$metabox_html .= <<<EOD
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

// radio button

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

//		Select box
		$metabox_html .= <<<EOD
<p>
<label for="omb_car">{$label6}: </label>
<select name="omb_car" id="omb_car">
EOD;
		foreach ( $cars as $car ) {
			$_car         = ucwords( $car );
			$selected     = ( $saved_car == $car ) ? 'selected="selected"' : '';
			$metabox_html .= <<<EOD
			<option value="{$car}" {$selected}>{$_car}</option>
EOD;

		}

		$metabox_html .= "</select></p>";


		echo $metabox_html;

	}

	public function omb_book_metabox() {
		wp_nonce_field( 'omb_book_info', 'omb_book_nonce' );
		$metabox_html = <<<EOD
<div class="fields">
	<div class="field_c">
		<div class="label_c" >
			<label for="book_author">Book Author</label>
		</div>
		<div class="input_c">
			<input type="text" class="widefat" id="book_author">
		</div>
		<div class="float-clear"></div>
	</div>
	<div class="field_c">
		<div class="label_c">
			<label for="book_isbn">Book ISBN</label>
		</div>
		<div class="input_c">
			<input type="text" id="book_isbn">
		</div>
		<div class="float-clear"></div>
	</div>
	
</div>
EOD;
		
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
		$car         = isset( $_POST['omb_car'] ) ? $_POST['omb_car'] : '';
		if ( '' == $location || '' == $country ) {
			return $post_id;
		}
		$location = sanitize_text_field( $location );
		$country  = sanitize_text_field( $country );
		$sport    = sanitize_text_field( $sport );
		$car      = sanitize_text_field( $car );

		update_post_meta( $post_id, 'omb_location', $location );
		update_post_meta( $post_id, 'omb_country', $country );
		update_post_meta( $post_id, 'omb_is_favorite', $is_favorite );
		update_post_meta( $post_id, 'omb_clr', $colors );
		update_post_meta( $post_id, 'omb_sport', $sport );
		update_post_meta( $post_id, 'omb_car', $car );
	}


	public function omb_admin_assets () {
		wp_enqueue_style('omb-admin-style', plugin_dir_url(__FILE__) . 'assets/admin/css/style.css', null, time());
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