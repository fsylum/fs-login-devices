<?php

namespace Fsylum\LoginDevices\Models;

use Fsylum\LoginDevices\WP\Database;

class Device
{
    public function delete($id)
    {
        return $this->bulkDelete([absint($id)]);
    }

    public function bulkDelete(array $ids = [])
    {
        global $wpdb;

        $ids    = array_map('absint', $ids);
        $ids    = array_filter($ids);
        $ids    = array_unique($ids);

        if (empty($ids)) {
            return;
        }

        $format = implode(', ', array_fill(0, count($ids), '%d'));
        $table  = $wpdb->prefix . Database::TABLE;

        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table} WHERE ID IN ($format)",
                $ids
            )
        );
    }
}
