<?php

/*
 * Copyright (C) 2016 Zhu Chen
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
 * For User to add or edit device
 *
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package SmartHome
 * @subpackage JSON-FrontEnd 
 */
class device implements IPackage {

    /**
     * Get the entry of the class
     * @param string $action Action to be performed
     * @return string
     */
    public function get_function($action) {
        switch ($action) {
            case 'bind':
                return 'bind_device';
            case 'transfer':
                return 'transfer_device';
            case 'chname':
                return 'change_name';
            case 'auth':
                return 'get_auth';
            case 'notransfer':
                return 'cancel_transfer';
            case 'tfstatus':
                return 'transfer_status';
        }
    }

    /**
     * Get the required class list
     * @param type $action The action from request
     * @return string[]
     */
    public function get_includes($action) {
        switch ($action) {
            case 'bind':
            case 'transfer':
                return ['member/IAuth', 'member/SH_User'];
            case 'auth':
                return ['smarthome/SH_device_svc','homeseer/hs_base'];
            case 'chname':
            case 'notransfer':
            case 'tfstatus':
                return ['smarthome/SH_device_svc'];
        }
    }

    /**
     * Bind a device to user
     */
    public function bind_device() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            $husr = unserialize($_SESSION['userobj']);
            list($hid, $name, $shift) = core::get_safe_input(INPUT_REQUEST, ['devid', 'devname', 'devshift'], ['fname', 'raw', 'fname']);
            switch ($husr->bind_device($hid, $shift, $name, $info)) {
                case -2:
                    echo json_encode(['errno' => 4012, 'errmsg' => _('The device was forbidden to transfer to other account, Ask your Administrator for help')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    break;
                case -1:
                    echo json_encode(['errno' => 4011, 'errmsg' => _('The device was not registered and the auto registation is turn off ')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    break;
                case 0:
                    echo json_encode(['errno' => 4010, 'errmsg' => _('The device safe code error')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    break;
                case 1:
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Success'), 'id' => $info['id'], 'hid' => $info['hid'], 'name' => $name],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    break;
            }
        }
    }

    /**
     * Transfer (Release) a device to another user
     */
    public function transfer_device() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            list($hid) = core::get_safe_input(INPUT_REQUEST, ['id'], ['int']);
            $husr = unserialize($_SESSION['userobj']);
            $shift = $husr->transfer_device(intval($hid));
            if ($shift) {
                echo json_encode(['errno' => 1001, 'errmsg' => _('Successfully Transfered Device'), 'shift' => $shift],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['errno' => 4004, 'errmsg' => _('Device ID NOT Found')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }

    /**
     * Get the auth object of Device
     * Open Accessed 
     */
    public function get_auth() {
        list($hid) = core::get_safe_input(INPUT_REQUEST, ['id'], ['int']);
        try {
            $devctrl = new SH_device_svc($hid);
            $msg = array_merge(['errno' => 1001, 'errmsg' => _('Success')], $devctrl->get_auth(true));
            echo json_encode($msg,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (InvalidArgumentException $ex) {
            echo json_encode(['errno' => 4004, 'errmsg' => _('Device Not Found')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Change the name of device
     */
    public function change_name() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            list($hid, $mame) = core::get_safe_input(INPUT_REQUEST, ['id', 'name'], ['int', 'raw']);
            try {
                $devctrl = new SH_device_svc($hid, $_SESSION['uid']);
                $r = $devctrl->change_name($name);
                if ($r) {
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Success')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(['errno' => 4003, 'errmsg' => _('Access Denied')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } catch (Exception $ex) {
                echo json_encode(['errno' => 4004, 'errmsg' => _('Device Not Found')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }

    /**
     * Cancel the tranfer 
     */
    public function cancel_transfer() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            list($hid) = core::get_safe_input(INPUT_REQUEST, ['id'], ['int']);
            try {
                $devctrl = new SH_device_svc($hid, $_SESSION['uid']);
                $r = $devctrl->cancel_transfer();
                if ($r) {
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Success')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(['errno' => 4003, 'errmsg' => _('Access Denied')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } catch (Exception $ex) {
                echo json_encode(['errno' => 4004, 'errmsg' => _('Device Not Found')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }

    /**
     * Get the device transfer status
     */
    public function transfer_status() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {
            list($hid) = core::get_safe_input(INPUT_REQUEST, ['id'], ['int']);
            try {
                $devctrl = new SH_device_svc($hid, $_SESSION['uid']);
                $st = $devctrl->transfer_status();
                if ($st) {
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Success'), 'shift' => $st],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } elseif ($st === false) {
                    echo json_encode(['errno' => 1002, 'errmsg' => _('Not In Transfer')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(['errno' => 4003, 'errmsg' => _('Access Denied')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } catch (Exception $ex) {
                echo json_encode(['errno' => 4004, 'errmsg' => _('Device Not Found')],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }

}
