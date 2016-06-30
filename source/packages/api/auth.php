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
 * Description of auth
 *
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 * @subpackage APIjson
 * @version 0.1.10
 */
class auth implements IPackage {

    public function get_includes($action) {
        $co = ['member/IAuth'];
        switch ($action) {
            case 'userlogin':
                $co[] = 'member/SH_User';
                return $co;
            case 'devlogin':
                $co[] = 'member/device';
                return $co;
            case 'logout':
                $co[] = 'member/authz';
                return $co;
            default:
                return $co;
        }
    }

    /**
     * Get the entry of the class
     * @param string $action
     * @return string
     */
    public function get_function($action) {
        switch ($action) {
            case 'userlogin':
                return 'login';
            case 'devlogin':
                return;
            case 'logout':
                return 'logout';
            case 'passwd':
                return 'change_password';
            case 'test':
                return 'showmsg';
        }
    }

    /**
     * Logged in by using username and password
     */
    public function login() {
        list($user, $pass) = core::get_safe_input(INPUT_REQUEST, ['username', 'password'], [NULL, NULL]);
        $usrctrl = core::get_module('SH_User');
        if (!($uid = $usrctrl->login($user, $pass))) {
            echo json_encode(['errno' => 4001, 'errmsg' => _('Incorrect Username or Password')], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Success'), 'seckey' => $usrctrl->generate_seckey(), 'timeout' => time() + core::$config['sec']['time']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Revoke the seckey
     */
    public function logout() {
        list($sec) = core::get_safe_input(INPUT_REQUEST, ['seckey'], [NULL]);
        try {
            $hdr = new authz($sec);
            $hdr->logout();
            echo json_encode(['errno' => 1001, 'errmsg' => _('Success')]);
        } catch (authz_exception $ex) {
            switch ($ex->getCode()) {
                case 4004:
                    echo json_encode(['errno' => 4002, 'errmsg' => _('Secret Key Not Exist')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4002:
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Success Deleted Expired')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4001:
                default:
                    echo json_encode(['errno' => 4003, 'errmsg' => _('Bad Request')], JSON_UNESCAPED_UNICODE);
                    break;
            }
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
    public function change_passwd() {
        list($sec) = core::get_safe_input(INPUT_REQUEST, ['seckey'], [NULL]);
        try {
            $husr = SH_User::instance_from_authz(new authz($sec));
        } catch (authz_exception $ex) {
            switch ($ex->getCode()) {
                case 3001:
                    echo json_encode(['errno' => 4004, 'errmsg' => _('Secret Key Generate From Device')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4004:
                    echo json_encode(['errno' => 4002, 'errmsg' => _('Secret Key Not Exist')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4002:
                    echo json_encode(['errno' => 4005, 'errmsg' => _('Success Deleted Expired')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4001:
                default:
                    echo json_encode(['errno' => 4003, 'errmsg' => _('Bad Request')], JSON_UNESCAPED_UNICODE);
                    break;
            }
        }
        list($old, $pass) = core::get_safe_input(INPUT_REQUEST, ['old', 'pass'], NULL);
        $r = $husr->chpasswd($old, $pass);
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
