<?php

/*
 * Copyright (C) 2014 Zhu Chen
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * This auth class use session, this is frontend service
 *
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 * @subpackage JSON-FrontEnd
 * @version 0.1.10
 */
class auth implements IPackage {

    /**
     * Implement the interface
     * @param string $action
     * @return string[]
     */
    public function get_includes($action) {
        $co = ['member/IAuth', 'member/SH_User'];
        return $co;
    }

    /**
     * Get the entry of the class
     * @param string $action
     * @return string
     */
    public function get_function($action) {
        switch ($action) {
            case 'login':
            case 'userlogin':
                return 'login';
            case 'logout':
                return 'logout';
            case 'passwd':
                return 'change_password';
            case 'test':
                return 'showmsg';
            case 'usermod':
            case 'userinfo':
                return 'usermod';
        }
    }

    /**
     * Logged in by using username and password
     */
    public function login() {
        list($user, $pass) = core::get_safe_input(INPUT_REQUEST, ['username', 'password'], ['fname', 'raw']);
        $usrctrl = core::get_module('SH_User');
        if (!($uid = $usrctrl->login($user, $pass, $info))) {
            echo json_encode(['errno' => 4001, 'errmsg' => _('Incorrect Username or Password')], JSON_UNESCAPED_UNICODE);
        } else {
            $_SESSION['username'] = $user;
            $_SESSION['uid'] = $uid;
            $_SESSION['userobj'] = serialize($usrctrl);
            $_SESSION['uinfo'] = $info;

            if ($info['id'] < 1000 || $this->group_admin($info['groups'])) {
                $_SESSION['admin'] = true;
                $_SESSION['admin_type'] = $this->admin_type($uid, $info['groups']);
            }
            echo json_encode(['errno' => 1001, 'errmsg' => _('Success')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Log out the user clean the session
     */
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            session_destroy();
            echo json_encode(['errno' => 1001, 'errmsg' => _("Success")]);
        }
    }

    /**
     * For Debug Gettext
     * @internal 
     */
    public function showmsg() {
        echo _("Hello");
    }

    /**
     * Change The Password of User
     */
    public function change_password() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            $husr = unserialize($_SESSION['userobj']);
            list($old, $pass) = core::get_safe_input(INPUT_REQUEST, ['oldp', 'newp'], ['raw', 'raw']);
            $r = $husr->chpasswda($old, $pass);
            switch ($r) {
                case 1:
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Password Changed')], JSON_UNESCAPED_UNICODE);
                    break;
                case -1:
                    echo json_encode(['errno' => 4005, 'errmsg' => _('New Password and Old Password are same')], JSON_UNESCAPED_UNICODE);
                    break;
                case -2:
                    echo json_encode(['errno' => 4003, 'errmsg' => _('Old Password mismatch')]);
                    break;
                default:
                    echo json_encode(['errno' => 5001, 'errmsg' => _('Unknown Error')]);
            }
        }
    }

    /**
     * Check the group id
     * @param string $groups
     * @return boolean 
     */
    private function group_admin($groups) {
        $gps = explode(',', $groups);
        foreach ($gps as $gp) {
            if ($gp < 1000) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the type of Administration
     * @param int $uid
     * @param int[] $groups
     * @return unsigned char
     */
    private function admin_type($uid, $groups) {
        if ($uid == 1) {
            return AuthType::AuthRoot;
        }
        $r = 0;
        foreach ($groups as $gid) {
            switch ($gid) {
                case 1:
                    $r|=AuthType::AuthRoot;
                    break;
                case 2:
                    $r|=AuthType::AuthAdminUser;
                    break;
                case 3:
                    $r|=AuthType::AuthAdminDevice;
                    break;
            }
        }
        return $r;
    }

    /**
     * Change the user setting 
     */
    public function usermod() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            $husr = unserialize($_SESSION['userobj']);
            list($type, $data) = core::get_safe_input(INPUT_REQUEST, ['type', 'data'], ['fname', 'raw'], true);
            $ds = [];
            if (is_array($type)) {
                foreach ($type as $k => $t) {
                    $ds[$t] = $data[$k];
                }
            } else {
                $ds[$type] = $data;
            }
            try {
                $husr->usermod($_SESSION['username'], $ds);
                $_SESSION['uinfo'] = $ds;
                core::tpl()->clearAllCache();
                echo json_encode(['errno' => 1001, 'errmsg' => _('Success')]);
            } catch (Exception $ex) {
                echo json_encode(['errno' => $ex->getCode(), 'errmsg' => $ex->getMessage()]);
            }
        }
    }

}
