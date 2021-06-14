<?php

namespace Fsylum\LoginDevices\Factories;

use DateTime;
use DateTimeZone;
use Fsylum\LoginDevices\WP\Database;

class DeviceFactory
{
    public function __construct(array $args = [], $page = 1, $per_page = 10)
    {
        $args = wp_parse_args($args, [
            's'          => '',
            'orderby'    => 'login_at',
            'order'      => 'DESC',
            'start_date' => false,
            'end_date'   => false,
        ]);

        if (!empty($args['start_date'])) {
            $args['start_date'] = DateTime::createFromFormat(get_option('date_format') . ' H:i:s', $args['start_date'] . ' 00:00:00', wp_timezone())->setTimezone(new DateTimeZone('UTC'));
        }

        if (!empty($args['end_date'])) {
            $args['end_date'] = DateTime::createFromFormat(get_option('date_format') . ' H:i:s', $args['end_date'] . ' 23:59:59', wp_timezone())->setTimezone(new DateTimeZone('UTC'));
        }

        $args['orderby'] = in_array($args['orderby'], ['login_at', 'logout_at']) ? $args['orderby'] : 'login_at';
        $args['order']   = in_array($args['order'], ['asc', 'desc']) ? strtoupper($args['order']) : 'DESC';

        $this->args     = $args;
        $this->page     = absint($page);
        $this->per_page = empty($per_page) ? 10 : absint($per_page);

        return $this;
    }

    public function get()
    {
        global $wpdb;

        $start  = ($this->page - 1) * $this->per_page;
        $table  = $wpdb->prefix . Database::TABLE;
        $wheres = [];

        if (!empty($this->args['s'])) {
            $wheres[] = $wpdb->prepare(
                'user_agent LIKE %s',
                '%'. $wpdb->esc_like($this->args['s']) . '%'
            );
        }

        if (!empty($this->args['start_date'])) {
            $wheres[] = $wpdb->prepare(
                '(login_at >= %s)',
                $this->args['start_date']->format('Y-m-d H:i:s')
            );
        }

        if (!empty($this->args['end_date'])) {
            $wheres[] = $wpdb->prepare(
                '(login_at <= %s)',
                $this->args['end_date']->format('Y-m-d H:i:s')
            );
        }

        $wheres = implode(' AND ', $wheres);

        if (empty($wheres)) {
            $wheres = '1=1';
        }

        $total_items = $wpdb->get_var("SELECT count(id) FROM {$table} WHERE {$wheres}");
        $items       = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, user_id, user_agent, login_at, logout_at FROM {$table} WHERE {$wheres} ORDER BY {$this->args['orderby']} {$this->args['order']} LIMIT %d,%d",
                $start,
                $this->per_page
            ),
            ARRAY_A
        );

        return [
            'items'       => $items,
            'total_items' => $total_items,
            'per_page'    => $this->per_page,
        ];
    }
}
