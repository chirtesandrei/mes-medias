<?php
/**
 * Plugin Name: Mes Médias
 * Plugin URI: https://mesplugins.fr/plugins/mes-medias/
 * Description: Repérez les médias les plus lourds et le poids de leurs variantes locales.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: MesPlugins.fr
 * Author URI: https://mesplugins.fr/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mes-medias
 * Update URI: https://mesplugins.fr/plugins/mes-medias/
 */
if(!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/includes/catalogue-common.php';
require_once __DIR__ . '/includes/class-msmd-plugin.php';
register_activation_hook(__FILE__,array('MesPlugins_Tools_V1','activate'));
MSMD_Plugin::boot();
if(is_admin() || (defined('DOING_CRON') && DOING_CRON) || (defined('WP_CLI') && WP_CLI)) {
 require_once __DIR__ . '/includes/catalogue-updater.php';
 new MesPlugins_Updater_V1(__FILE__,'mes-medias','0.1.0');
}
