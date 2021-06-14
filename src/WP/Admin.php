<?php

namespace Fsylum\LoginDevices\WP;

use Fsylum\LoginDevices\Models\Device;
use Fsylum\LoginDevices\Contracts\Runnable;
use Fsylum\LoginDevices\WP\ListTables\DeviceListTable;

class Admin implements Runnable
{
    const CAPABILITY = 'manage_options';
    const KEY        = 'fs-login-devices';

    public function run()
    {
        add_action('admin_menu', [$this, 'addPage']);
        add_action('admin_notices', [$this, 'showNotice']);
        add_action('admin_post_fsld_delete_device', [$this, 'deleteLoginDevice']);
        add_filter('set-screen-option', [$this, 'saveScreenOption'], 10, 3);
        add_filter('plugin_action_links_'  . FSLD_PLUGIN_BASENAME, [$this, 'addPageLink']);
    }

    public function addPage()
    {
        $hook = add_submenu_page('users.php', __('Login Devices', 'fs-login-devices'), __('Login Devices', 'fs-login-devices'), self::CAPABILITY, self::KEY, [$this, 'displayPage']);

        add_action("load-$hook", [$this, 'addScreenOption']);
    }

    public function displayPage()
    {
        $listTable = new DeviceListTable;
        ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

                <form action="<?php echo esc_url(admin_url('users.php')); ?>">
                    <input type="hidden" name="page" value="<?php echo esc_attr(self::KEY); ?>">

                    <?php
                        $listTable->prepare_items();
                        $listTable->search_box(__('Search Entries', 'fs-login-devices'), 'fs-login-devices-search');
                        $listTable->display();
                    ?>
                </form>
            </div>
        <?php
    }

    public function addScreenOption()
    {
        add_screen_option('per_page', [
            'default' => 10,
            'option'  => 'login_device_per_page',
        ]);
    }

    public function saveScreenOption($screen_option, $option, $value)
    {
        if ($option === 'login_device_per_page') {
            return $value;
        }

        return $screen_option;
    }

    public function showNotice()
    {
        $screen = get_current_screen();

        if ($screen->id !== 'users_page_' . self::KEY) {
            return;
        }

        if (!isset($_GET['deleted']) || sanitize_key($_GET['deleted']) !== 'yes') {
            return;
        }
        ?>
            <div class="updated notice is-dismissible">
                <p><?php _e('Selected entries have been successfully deleted.'); ?>
                </p>
            </div>
        <?php
    }

    public function deleteLoginDevice()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'fs-login-devices-delete-nonce')) {
            wp_die(__('Invalid request', 'fs-login-devices'));
        }

        $result   = (new Device)->delete(absint($_REQUEST['id']));
        $redirect = $_SERVER['HTTP_REFERER'];

        if (empty($redirect)) {
            $redirect = admin_url(); // TODO
        }

        $redirect = add_query_arg([
            'deleted' => $result ? 'yes' : 'no',
        ], $redirect);

        wp_safe_redirect($redirect);
        exit;
    }

    public function addPageLink($links)
    {
        $url = add_query_arg([
            'page' => self::KEY,
        ], admin_url('tools.php'));

        $links[] = '<a href="'. esc_url($url) .'" target="_blank">' . __('Settings', 'fs-login-devices') . '</a>';

        return $links;
    }
}
