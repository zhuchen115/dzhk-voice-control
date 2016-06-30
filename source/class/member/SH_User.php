<?php

/*
 * Copyright 2016 Zhu Chen
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

 *   http://www.apache.org/licenses/LICENSE-2.0

 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once __DIR__ . '/member.php';

/**
 * Indicate a Smart Home User
 * @package SmartHome
 * @author Zhu Chen <zhuchen@greatqq.com>
 */
class SH_User extends member implements IAuth {

    /**
     * Describe a user's id
     * @var int 
     */
    public $uid = NULL;

    /**
     * The user will inherit all the auth from group
     * Unless the auth is overrided
     * @var array int 
     */
    public $group;
    private $auth;

    /**
     * Initialize a user from uid, No Auth for device
     * @param int $uid
     */
    public function __construct($uid = NULL) {
        $this->uid = $uid;
        parent::__construct();
    }

    /**
     * Covert Seckey to basic user type
     * @param authz $authz The authz type, must be a user based
     * @throws authz_exception
     */
    public static function instance_from_authz(authz $authz) {
        if ($authz->get_type() == 'User') {
            $obj = new SH_User();
            $obj->auth = $authz->get_authd();
        } else {
            throw new authz_exception('User', 'Cannot Covert AuthType(Seckey) To AuthType(User), Type mismatch', 3001);
        }
        if (check_secret_key($authz->get_seckey(), $info)) {
            $this->uid = intval(substr($info, 4));
        } else {
            throw new authz_exception('Authz', 'Auth Code Vaild Failure', 5002);
        }
    }

    /**
     * Change The password of a user
     * @param string $old Old password
     * @param string $new new password
     * @return bool if it is success
     * @throws authz_exception
     */
    public function chpasswda($old, $new) {
        if ($this->uid > 0) {
            parent::set_use(true);
            $r = parent::chpasswd($this->uid, $old, $new);
            parent::set_use(false);
        } else {
            throw new authz_exception('User', 'Uid Incrrectly Setted', 5003);
        }
        return $r;
    }

    /**
     * Get User Logged in
     * @since 0.0.1
     * @param string $user
     * @param string $pass
     * @param array $userinfo
     * @return int
     */
    public function login($user, $pass, &$userinfo = []) {
        $uid = parent::login($user, $pass, $userinfo);
        if (!$uid) {
            return 0;
        }
        $this->uid = $uid;
        $this->auth = unserialize($userinfo['authz']);
        $this->group = explode(',', $userinfo['groups']);
        $cond = '';
        foreach ($this->group as $gp) {
            $cond.='`id`=' . intval($gp) . ' OR ';
        }
        $cond = substr($cond, 0, -3);
        $groupinfo = DB::select('groups', $cond);
        foreach ($groupinfo as $ginfo) {
            $gauthz = unserialize($ginfo['authz']);
            if (is_array($gauthz)) {
                $this->auth = array_merge($gauthz, $this->auth);
            }
        }
        return $this->uid;
    }

    /**
     * Get the unique id of user
     * @since 0.1.1
     * @return string
     */
    public function get_unique_id() {
        if (!empty($this->uid)) {
            return 'uid_' . $this->uid;
        } else {
            return NULL;
        }
    }

    /**
     * Implement the IAuth 
     * @param AuthType $type
     * @return array|boolean
     * @throws Exception
     */
    public function get_access($type) {
        if ($this->uid == NULL) {
            return false;
        }
        switch ($type) {
            case AuthType::AuthGlobal :
                return $this->get_access_global();
            case AuthType::AuthLocation :
                return $this->get_access_location();
            case AuthType::AuthObject :
                return $this->get_access_object();
            default:
                throw new Exception("Error in using SH_User::get_access, AuthType unknown");
        }
    }

    /**
     * Check whether a user have a root access
     * The build in account have all root access to all devices and location
     * The build in root group number is 1000
     * The build in system account number is smaller than 1000
     * @return boolean Whether user have root access
     */
    private function get_access_global() {
        if (in_array(1000, $this->group) || $this->uid < 1000) {
            $this->auth['global'] = true;
            return true;
        } else {
            return false;
        }
    }

use Auth_location_object {
        get_access_location as private;
        get_access_object as private;
    }

    /**
     * Generate a security key instead of the user
     */
    public function generate_seckey() {
        $uid = $this->get_unique_id();
        $sec = generate_secret_key($uid);
        $cond['seckey'] = $sec;
        $cond['type'] = SecType::AuthUser;
        $cond['timeout'] = time() + core::$config['sec']['time'];
        $cond['authz'] = serialize($this->auth);
        DB::insert('authz', $cond);
        return $sec;
    }

    /**
     * Bind Device to user 
     * Note: This kind of device is non-os based device, the access of the device should have a proxy
     * @param string $hid  The hardware address
     * @param string $shift  The code used to indentify the user have the device
     * @param string $name The Device Display Name
     * @param mixed &$info The device info
     * @return int Code of  Result<ol start="-2"><li>The Device cannot change owner</li><li>The Device was not registered</li><li> The shiftcode now correct</li><li>success</li></ol>
     */
    public function bind_device($hid, $shift, $name, &$info = []) {
        if (is_numeric($hid)) {
            $hdinfo = DB::select('devices', '`id`=' . intval($hid), '`id`', true);
        } else {
            $hdinfo = DB::select('devices', '`hid`=' . DB::get_str($hid), '`id`', true);
        }
        if (!$hdinfo) {
            if (core::$config['smarthome']['device_autoreg']) {
                $cond = ['hid' => $hid, 'owner' => $this->uid, 'name' => $name];
                DB::insert('devices', $cond);
                return 1;
            } else {  //Device cannot be registered
                return -1;
            }
        } else {
            if ((!empty($hdinfo['owner'])) && (empty($hdinfo['shiftcode']))) {    //The device cannot change owner
                return -2;
            } else {
                if ($shift == $hdinfo['shiftcode']) {
                    $info = ['owner' => $this->uid, 'shiftcode' => '', 'name' => $name];
                    DB::update('devices', $info, '`id`=' . $hdinfo['id']);
                    $info['id'] = $hdinfo['id'];
                    $info['hid'] = $hdinfo['hid'];
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }

    /**
     * Make The device transfer to another user
     * Generate a random string as Secret Code
     * @param string $hid The hardware address
     * @param mixed $info The hardware info
     * @return boolean
     */
    public function transfer_device($hid, &$info = []) {
        if (is_integer($hid)) {
            $hdinfo = DB::select('devices', '`id`=' . intval($hid) . ' AND `owner` = ' . $this->uid, '`id`', true);
        } else {
            $hdinfo = DB::select('devices', '`hid`=' . DB::get_str($hid) . ' AND `owner`=' . $this->uid, '`id`', true);
        }
        if (!$hdinfo) {
            return false;
        }
        $sfcode = randstr(5, true);
        DB::update('devices', ['shiftcode' => $sfcode], '`id`=' . $hdinfo['id']);
        $info = ['id' => $hdinfo['id'], 'name' => $hdinfo['name'], 'hid' => $hdinfo['hid']];
        return $sfcode;
    }

    /**
     * Get the devices of current user
     * @return mixed The devices table of current user
     */
    public function get_devices() {
        $devs = DB::select('devices', '`owner`=' . $this->uid);
        foreach ($devs as $i => $dev) {
            $devs[$i]['authz'] = unserialize($dev['authz']);
        }
        return $devs;
    }

    /**
     * Get the User Display Name
     * @param int  The user's id
     * @return string|boolean Return the User Display Name
     */
    public static function uid2name($uid) {
        $rslt = DB::select('member', '`id`=' . intval($uid), '`id`', true);
        if (!$rslt) {
            return false;
        }
        $ns = unserialize($rslt['extra']);
        return $ns['name'];
    }

}
