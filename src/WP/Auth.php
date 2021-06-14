<?php

namespace Fsylum\LoginDevices\WP;

use WP_User;
use Fsylum\LoginDevices\Helper;
use Fsylum\LoginDevices\Contracts\Runnable;

class Auth implements Runnable
{
    public function run()
    {
        add_action('wp_login', [$this, 'recordLogin'], 10, 2);
        add_action('wp_logout', [$this, 'recordLogout']);
    }

    public function recordLogin($user_login, WP_User $user)
    {
        Helper::recordLogin($user->ID);
    }

    public function recordLogout($user_id)
    {
        Helper::recordLogout($user_id);
    }
}
