<?php

namespace Fsylum\LoginDevices\WP;

class Database
{
    const KEY     = 'fs_login_devices_db_version';
    const TABLE   = 'fs_login_devices';
    const VERSION = 1;

    public static function install()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table           = $wpdb->prefix . self::TABLE;

        $sql = "CREATE TABLE {$table} (
            id bigint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(9) UNSIGNED NOT NULL,
            user_agent text DEFAULT NULL,
            login_at datetime DEFAULT NOW() NOT NULL,
            logout_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
        update_option(self::KEY, self::VERSION);
    }
}
