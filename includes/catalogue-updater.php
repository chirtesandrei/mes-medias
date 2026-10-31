<?php
/** Fixed, parameter-free HTTPS release channel. No frontend requests or telemetry. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! class_exists( 'MesPlugins_Updater_V1', false ) ) {
final class MesPlugins_Updater_V1 {
	private $slug;
	private $version;
	private $basename;
	public function __construct( $file, $slug, $version ) {
		$this->slug=$slug; $this->version=$version; $this->basename=plugin_basename($file);
		add_filter('pre_set_site_transient_update_plugins', array($this,'updates'));
		add_filter('plugins_api', array($this,'details'), 20, 3);
	}
	private function manifest() {
		if (defined('MESPLUGINS_DISABLE_UPDATE_CHECK') && MESPLUGINS_DISABLE_UPDATE_CHECK) { return false; }
		$key='mesplugins_release_' . $this->slug;
		$data=get_transient($key);
		if(false === $data) {
			$response=wp_remote_get('https://mesplugins.fr/api/catalogue/' . $this->slug . '/mise-a-jour.json', array('timeout'=>5,'redirection'=>0,'user-agent'=>'MesPlugins-Updater/1.0','limit_response_size'=>32768,'headers'=>array('Accept'=>'application/json')));
			$data= !is_wp_error($response) && 200===wp_remote_retrieve_response_code($response) ? json_decode(wp_remote_retrieve_body($response),true) : null;
			$data=$this->valid($data) ? $data : array();
			set_transient($key,$data,12*HOUR_IN_SECONDS);
		}
		return $this->valid($data) ? $data : false;
	}
	private function valid( $data ) {
		if(!is_array($data) || ($data['slug']??'')!==$this->slug || !isset($data['version'],$data['package'],$data['sha256']) || !is_string($data['version']) || !preg_match('/^\d+\.\d+\.\d+$/D',$data['version']) || !is_string($data['sha256']) || !preg_match('/^[a-f0-9]{64}$/D',$data['sha256'])) { return false; }
		return $data['package']==='https://mesplugins.fr/downloads/' . $this->slug . '-' . $data['version'] . '.zip';
	}
	public function updates( $transient ) {
		if(!is_object($transient) || !isset($transient->checked) || !is_array($transient->checked) || empty($transient->checked[$this->basename])) { return $transient; }
		$data=$this->manifest();
		if(isset($transient->response) && is_array($transient->response) && isset($transient->response[$this->basename])) { unset($transient->response[$this->basename]); }
		if($data && version_compare($data['version'],$this->version,'>')) {
			if(!isset($transient->response) || !is_array($transient->response)) { $transient->response=array(); }
			$transient->response[$this->basename]=(object)array('slug'=>$this->slug,'plugin'=>$this->basename,'new_version'=>$data['version'],'url'=>'https://mesplugins.fr/plugins/' . $this->slug . '/','package'=>$data['package'],'requires'=>'6.4','requires_php'=>'7.4','tested'=>$data['tested']??'');
		}
		return $transient;
	}
	public function details( $result, $action, $args ) {
		if('plugin_information'!==$action || !is_object($args) || ($args->slug??'')!==$this->slug) { return $result; }
		$data=$this->manifest();
		if(!$data) { return $result; }
		return (object)array('name'=>ucwords(str_replace('-',' ',$this->slug)),'slug'=>$this->slug,'version'=>$data['version'],'homepage'=>'https://mesplugins.fr/plugins/' . $this->slug . '/','download_link'=>$data['package'],'requires'=>'6.4','requires_php'=>'7.4','tested'=>$data['tested']??'','sections'=>array('description'=>'<p>Extension MesPlugins.fr. Documentation et limites disponibles sur la fiche du produit.</p>'));
	}
}
}
