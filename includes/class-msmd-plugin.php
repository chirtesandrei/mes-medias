<?php
if ( ! defined('ABSPATH') ) { exit; }
final class MSMD_Plugin {
	const PAGE='mes-medias';
	const STATE='msmd_cursor';
	public static function boot() {
		add_action('admin_menu',function(){add_management_page('Mes Médias','Mes Médias','manage_options',self::PAGE,array(__CLASS__,'render'));});
		add_action('admin_post_msmd_scan',array(__CLASS__,'scan'));
		add_action('admin_post_msmd_reset',array(__CLASS__,'reset'));
	}
	/** Resolve only existing regular files inside this site's local uploads. */
	public static function local_file($path,$base) {
		if(!is_string($path) || !is_string($base) || false!==strpos($path,'://') || false!==strpos($path,"\0")) { return false; }
		$root=realpath($base); $file=realpath($path);
		if(false===$root || false===$file || !is_file($file) || !is_readable($file)) { return false; }
		$root=rtrim(wp_normalize_path($root),'/') . '/'; $file=wp_normalize_path($file);
		return 0===strpos($file,$root) ? $file : false;
	}
	public static function inventory($id) {
		$uploads=wp_upload_dir(null,false);
		$attached=get_attached_file($id,true);
		$base=$uploads['basedir'];
		$main=self::local_file($attached,$base);
		if(!$main) { return array('bytes'=>-1,'files'=>0,'status'=>'indisponible'); }
		$files=array($main); $partial=false;
		$meta=wp_get_attachment_metadata($id,true);
		$names=array();
		if(is_array($meta)) {
			if(isset($meta['sizes']) && is_array($meta['sizes'])) { if(count($meta['sizes'])>100) { $partial=true; } foreach(array_slice($meta['sizes'],0,100) as $size) { if(is_array($size) && isset($size['file'])) { $names[]=$size['file']; } } }
			if(isset($meta['original_image'])) { $names[]=$meta['original_image']; }
		}
		foreach($names as $name) {
			if(!is_string($name) || basename($name)!==$name || false!==strpos($name,'\\') || '.'===$name || '..'===$name) { $partial=true; continue; }
			$file=self::local_file(dirname($main) . '/' . $name,$base);
			if($file) { $files[]=$file; } else { $partial=true; }
		}
		$total=0; $count=0;
		foreach(array_unique($files) as $file) { $size=filesize($file); if(false===$size) { $partial=true; } else { $total+=$size; ++$count; } }
		return array('bytes'=>$total,'files'=>$count,'status'=>$partial?'partiel':'complet');
	}
	public static function scan() {
		MesPlugins_Tools_V1::guard('msmd_index');
		global $wpdb;
		$cursor=max(0,(int)get_option(self::STATE,0));
		$ids=$wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type=%s AND post_status IN (%s,%s,%s) AND ID > %d ORDER BY ID ASC LIMIT 50",'attachment','inherit','private','publish',$cursor));
		foreach($ids as $id) {
			$report=self::inventory((int)$id);
			update_post_meta($id,'_msmd_inventory',$report);
			update_post_meta($id,'_msmd_bytes',$report['bytes']);
			$cursor=(int)$id;
		}
		update_option(self::STATE,$cursor,false);
		MesPlugins_Tools_V1::end(self::PAGE,'indexed');
	}
	public static function reset() {
		MesPlugins_Tools_V1::guard('msmd_index');
		delete_post_meta_by_key('_msmd_inventory'); delete_post_meta_by_key('_msmd_bytes');
		delete_option(self::STATE);
		MesPlugins_Tools_V1::end(self::PAGE,'reset');
	}
	public static function render() {
		MesPlugins_Tools_V1::head('Mes Médias');
		global $wpdb;
		$cursor=max(0,(int)get_option(self::STATE,0));
		$remaining=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type=%s AND post_status IN (%s,%s,%s) AND ID > %d",'attachment','inherit','private','publish',$cursor));
		echo '<p>Repérez les fichiers les plus lourds et le poids de leurs variantes locales. Aucune image n’est modifiée ou supprimée. Les fichiers distants ou manquants sont signalés, pas comptés comme zéro.</p>';
		echo '<p>' . esc_html((string)$remaining) . ' média(s) à analyser. Chaque clic traite au plus 50 médias. Réinitialisez puis recommencez après une modification de fichiers existants.</p>';
		MesPlugins_Tools_V1::form('msmd_scan','msmd_index','Analyser les 50 suivants');
		MesPlugins_Tools_V1::form('msmd_reset','msmd_index','Réinitialiser uniquement cet index');
		$paged=isset($_GET['mp_page']) && is_scalar($_GET['mp_page']) ? max(1,min(100000,absint($_GET['mp_page']))) : 1;
		$q=new WP_Query(array('post_type'=>'attachment','post_status'=>array('inherit','private','publish'),'posts_per_page'=>20,'paged'=>$paged,'meta_key'=>'_msmd_bytes','orderby'=>array('meta_value_num'=>'DESC','ID'=>'ASC')));
		echo '<h2>Poids décroissant des médias analysés</h2><table class="widefat striped"><thead><tr><th>Média</th><th>Fichiers locaux</th><th>Poids cumulé</th><th>Mesure</th></tr></thead><tbody>';
		foreach($q->posts as $post) {
			$r=get_post_meta($post->ID,'_msmd_inventory',true);
			if(!is_array($r) || !isset($r['bytes'],$r['files'],$r['status'])) { continue; }
			echo '<tr><td><a href="' . esc_url(get_edit_post_link($post->ID)) . '">' . esc_html(get_the_title($post->ID)) . '</a></td><td>' . esc_html((string)$r['files']) . '</td><td>' . esc_html($r['bytes']<0?'Inconnu':size_format($r['bytes'],1)) . '</td><td>' . esc_html($r['status']) . '</td></tr>';
		}
		echo '</tbody></table>';
		for($p=max(1,$paged-1);$p<=min($q->max_num_pages,$paged+1);++$p) { echo '<a class="button" href="' . esc_url(add_query_arg(array('page'=>self::PAGE,'mp_page'=>$p),admin_url('tools.php'))) . '">' . esc_html((string)$p) . '</a> '; }
		echo '<p>Le poids stocké n’est pas le poids transféré par une page : le navigateur ne charge pas toutes les variantes. Aucun diagnostic de fichiers inutilisés n’est déduit de cet inventaire.</p></div>';
	}
}
