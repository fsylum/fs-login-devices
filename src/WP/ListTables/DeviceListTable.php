<?php

namespace Fsylum\LoginDevices\WP\ListTables;

use DateTimeZone;
use WP_List_Table;
use Fsylum\LoginDevices\Helper;
use Fsylum\LoginDevices\WP\Admin;
use Fsylum\LoginDevices\Models\Device;
use Fsylum\LoginDevices\Factories\DeviceFactory;

class DeviceListTable extends WP_List_Table
{
    public function prepare_items()
    {
        $this->process_bulk_action();

        $per_page = get_user_meta(
            get_current_user_id(),
            get_current_screen()->get_option('per_page', 'option'),
            true
        );

        $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];
        $result                = (new DeviceFactory($_REQUEST, $this->get_pagenum(), absint($per_page)))->get();
        $this->items           = $result['items'];

        $this->set_pagination_args([
            'total_items' => $result['total_items'],
            'per_page'    => $result['per_page'],
        ]);
    }

    public function no_items()
    {
        _e('No entries found.', 'fs-login-devices');
    }

    public function get_columns()
    {
        return [
            'cb'         => '<input type="checkbox">',
            'user_id'    => __('User', 'fs-login-devices'),
            'user_agent' => __('User Agent', 'fs-login-devices'),
            'login_at'   => __('Login Datetime', 'fs-login-devices'),
            'logout_at'  => __('Logout Datetime', 'fs-login-devices'),
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'user_agent' => ['user_agent', false],
            'login_at'   => ['login_at', false],
            'logout_at'  => ['logout_at', false],
        ];
    }

    public function column_user_id($item)
    {
        $delete_url = add_query_arg([
            'action' => 'fsld_delete_device',
            'id'     => absint($item['id']),
        ], admin_url('admin-post.php'));

        $actions = [
            'edit'   => sprintf('<a href="%s">' . __('Edit User', 'fs-login-devices') . '</a>', get_edit_user_link($item['user_id'])),
            'delete' => sprintf('<a href="%s" class="js-delete-login-device">' . __('Delete Entry', 'fs-login-devices') . '</a>', wp_nonce_url($delete_url, 'fs-login-devices-delete-nonce')),
        ];

        $userdata = get_userdata($item['user_id']);
        $column   = sprintf('<a href="%s" class="row-title">%s</a>', get_edit_user_link($item['user_id']), $userdata->display_name);

        return sprintf('%s %s', $column, $this->row_actions($actions));
    }

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="ids[]" value="%d">', $item['id']);
    }

    public function column_default($item, $column_name)
    {
        switch($column_name) {
            case 'login_at':
            case 'logout_at':
                if (empty($item[$column_name])) {
                    return '-';
                }

                return wp_date(
                    sprintf('%s %s', get_option('date_format'), get_option('time_format')),
                    strtotime($item[$column_name])
                );
                break;

            default:
                return $item[$column_name];
                break;
        }
    }

    protected function get_bulk_actions()
    {
        return [
            'delete' => __('Delete', 'fs-login-devices'),
        ];
    }

    protected function extra_tablenav($which)
    {
        if ($which === 'bottom') {
            return;
        }

        ob_start();
        ?>
            <div class="alignleft actions">
                <label for="filter-login-start-date" class="screen-reader-text"><?php _e('Filter by login start date', 'fs-login-devices'); ?></label>
                <input type="text" id="filter-login-start-date" placeholder="<?php _e('Select a login start date', 'fs-login-devices'); ?>" name="login_start_date" value="<?php echo esc_attr(sanitize_text_field($_GET['login_start_date'] ?? '')) ?>">
                <label for="filter-login-end-date" class="screen-reader-text"><?php _e('Filter by login end date', 'fs-login-devices'); ?></label>
                <input type="text" id="filter-login-end-date" placeholder="<?php _e('Select a login end date', 'fs-login-devices'); ?>" name="login_end_date" value="<?php echo esc_attr(sanitize_text_field($_GET['login_end_date'] ?? '')) ?>">
                <label for="filter-logout-start-date" class="screen-reader-text"><?php _e('Filter by logout start date', 'fs-login-devices'); ?></label>
                <input type="text" id="filter-logout-start-date" placeholder="<?php _e('Select a logout start date', 'fs-login-devices'); ?>" name="logout_start_date" value="<?php echo esc_attr(sanitize_text_field($_GET['logout_start_date'] ?? '')) ?>">
                <label for="filter-logout-end-date" class="screen-reader-text"><?php _e('Filter by logout end date', 'fs-login-devices'); ?></label>
                <input type="text" id="filter-logout-end-date" placeholder="<?php _e('Select a logout end date', 'fs-login-devices'); ?>" name="logout_end_date" value="<?php echo esc_attr(sanitize_text_field($_GET['logout_end_date'] ?? '')) ?>">
                <input type="submit" class="button" value="<?php _e('Filter', 'fs-login-devices'); ?>">
            </div>
        <?php

        echo ob_get_clean();
    }

    private function process_bulk_action()
    {
        if ($this->current_action() === 'delete') {
            if (!empty($_REQUEST['ids'])) {
                (new Device)->bulkDelete(array_map('absint', $_REQUEST['ids']));
            }

            add_settings_error(Admin::KEY, Admin::KEY, __('Selected entries have been deleted.'), 'success');
        }
    }
}
