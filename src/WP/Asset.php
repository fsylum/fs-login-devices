<?php

namespace Fsylum\LoginDevices\WP;

use Fsylum\LoginDevices\WP\Admin;
use Fsylum\LoginDevices\Contracts\Runnable;

class Asset implements Runnable
{
    const KEY = 'fsld-asset';

    public function run()
    {
        add_action('admin_enqueue_scripts', [$this, 'loadAssets']);
    }

    public function loadAssets(string $hook)
    {
        if ($hook !== 'users_page_' . Admin::KEY) {
            return;
        }

        wp_enqueue_script(
            self::KEY . '-js-admin',
            FSLD_PLUGIN_URL . '/assets/dist/js/admin.js',
            ['jquery', 'jquery-ui-datepicker', 'wp-i18n'],
            wp_get_environment_type() === 'production' ? FSLD_PLUGIN_VERSION : time()
        );

        wp_enqueue_style(
            self::KEY . '-css-admin',
            FSLD_PLUGIN_URL . '/assets/dist/css/admin.css',
            [],
            wp_get_environment_type() === 'production' ? FSLD_PLUGIN_VERSION : time()
        );

        wp_set_script_translations(self::KEY . '-js-admin', 'fs-login-devices');
    }
}
