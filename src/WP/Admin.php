<?php

namespace Fsylum\LoginDevices\WP;

use Fsylum\LoginDevices\Helper;
use Fsylum\LoginDevices\Models\Device;
use Fsylum\LoginDevices\Contracts\Runnable;
use Fsylum\LoginDevices\WP\ListTables\DeviceListTable;

class Admin implements Runnable
{
    const QS_KEY     = 'fsld-action';
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

        if (!isset($_GET[self::QS_KEY])) {
            return;
        }

        switch (sanitize_text_field($_GET[self::QS_KEY])) {
            case 'deleted':
                $message = __('Selected entry have been deleted.', 'fs-login-devices');
                break;

            case 'bulk-deleted':
                $message = __('Selected entries have been deleted.', 'fs-login-devices');
                break;
        }

        printf(
            '<div class="updated notice is-dismissible"><p>%s</p></div>',
            $message
        );
    }

    public function deleteLoginDevice()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'fs-login-devices-delete-nonce')) {
            wp_die(__('Invalid request', 'fs-login-devices'));
        }

        $result   = (new Device)->delete(absint($_REQUEST['id']));
        $redirect = $_SERVER['HTTP_REFERER'];

        if (empty($redirect)) {
            $redirect = Helper::listUrl();
        }

        $redirect = add_query_arg([
            self::QS_KEY => 'deleted',
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
