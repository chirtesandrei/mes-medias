<?php
if(!defined('WP_UNINSTALL_PLUGIN')) { exit; }
delete_post_meta_by_key('_msmd_inventory');
delete_post_meta_by_key('_msmd_bytes');
delete_option('msmd_cursor');
delete_transient('mesplugins_release_mes-medias');
