<?php
/** Bundled support library; identical bytes in each standalone plugin. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! class_exists( 'MesPlugins_Tools_V1', false ) ) {
final class MesPlugins_Tools_V1 {
	public static function activate() { if(is_multisite()) { wp_die('Cette version est réservée aux installations WordPress simples, hors multisite.'); } }
	public static function field( $name ) {
		return isset( $_POST[$name] ) && is_string( $_POST[$name] ) ? wp_unslash( $_POST[$name] ) : '';
	}
	public static function guard( $nonce, $cap = 'manage_options' ) {
		if ( ! current_user_can( $cap ) || 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'mesplugins-tools' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( $nonce );
	}
	public static function end( $page, $notice ) {
		wp_safe_redirect( add_query_arg( array( 'page' => $page, 'mp_notice' => $notice ), admin_url( 'tools.php' ) ), 303 );
		exit;
	}
	public static function head( $title ) {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Accès non autorisé.', '', array( 'response' => 403 ) ); }
		echo '<div class="wrap"><h1>' . esc_html( $title ) . '</h1>';
		$n = isset($_GET['mp_notice']) && is_string($_GET['mp_notice']) ? sanitize_key(wp_unslash($_GET['mp_notice'])) : '';
		$messages = array('saved'=>'Modification enregistrée.', 'invalid'=>'Action refusée : vérifiez les données et vos droits.', 'indexed'=>'Lot analysé.', 'reset'=>'Index réinitialisé.', 'planned'=>'Publication reprogrammée dans cinq minutes.');
		if (isset($messages[$n])) { echo '<div class="notice notice-info"><p>' . esc_html($messages[$n]) . '</p></div>'; }
	}
	public static function form( $action, $nonce, $label, $fields = array() ) {
		echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
		echo '<input type="hidden" name="action" value="' . esc_attr($action) . '">';
		wp_nonce_field($nonce);
		foreach($fields as $name=>$value) { echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr((string)$value) . '">'; }
		echo '<button type="submit" class="button">' . esc_html($label) . '</button></form>';
	}
}
}
