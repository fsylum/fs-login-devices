<?php

namespace Fsylum\LoginDevices;

use Fsylum\LoginDevices\WP\Database;

class Helper
{
    public static function jsRedirect($url = '')
    {
        echo '<script>window.location = "' . $url . '"</script>';
        exit;
    }

    public function recordLogin($user_id)
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

    public function recordLogout($user_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . Database::TABLE;

        $row = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d AND user_agent = %s AND login_at IS NOT NULL and logout_at IS NULL LIMIT 1",
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
