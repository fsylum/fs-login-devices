<?php

namespace Fsylum\LoginDevices;

use Fsylum\LoginDevices\WP\Admin;
use Fsylum\LoginDevices\WP\Database;

class Helper
{
    public static function listUrl()
    {
        return add_query_arg([
            'page' => Admin::KEY,
        ], admin_url('users.php'));
    }

    public static function recordLogin($user_id)
    {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . Database::TABLE,
            [
                'user_id'    => absint($user_id),
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
                'login_at'   => current_time('mysql'),
            ],
            [
                '%d',
                '%s',
                '%s',
            ]
        );
    }

    public static function recordLogout($user_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . Database::TABLE;

        $row = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d AND user_agent = %s AND login_at IS NOT NULL and logout_at IS NULL ORDER BY login_at DESC LIMIT 1",
                absint($user_id),
                sanitize_text_field($_SERVER['HTTP_USER_AGENT'])
            )
        );

        if ($row) {
            $wpdb->update(
                $table,
                [
                    'logout_at' => current_time('mysql'),
                ],
                [
                    'id' => $row
                ],
                [
                    '%s'
                ],
                [
                    '%d'
                ]
            );
        }
    }
}
