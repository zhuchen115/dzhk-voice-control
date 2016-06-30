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
 * The class of device
 * Register and Edit the device for Admin, assign  the access to the device
 * Get the auth of a device and change name of a device for a user
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package SmartHome
 */
class SH_device_svc {

    protected $id;
    private $admin = false;
    protected $authed = false;
    protected $access;
    protected $shift;

    /**
     * Create a new device control
     * @param int|string $id Device ID or Hardware Address
     * @param int $uid The user id
     */
    public function __construct($id = 0, $uid = 0) {
        if ($uid > 0 && $uid < 1000) { //For the Administrator
            $this->authed = true;
            $this->admin = true;
        }
        $dev = DB::select('devices', '`id`=' . $id, '`id`', true);
        if ((!$dev) && (!$this->admin)) {
            throw new InvalidArgumentException('Device not found', 4004);
        }
        if ($dev) {
            $this->access = unserialize($dev['authz']);
            $this->shift = $dev['shiftcode'];
            if ($dev['owner'] == $uid) { //For The device owner
                $this->authed = true;
            }
        }

        $this->id = $id;
    }

    /**
     * Change the name of a device
     * @param type $name
     * @return int
     * @throws InvalidArgumentException
     */
    public function change_name($name) {
        if ($this->authed) {
            DB::update('devices', $cond, '`id`=' . $dev['id']);
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get the transfer status of device
     * @return boolean|int|string is for transfer. 0 is not accessable
     */
    public function transfer_status() {
        if ($this->authed) {
            if (!empty($this->shift)) {
                return $this->shift;
            } else {
                return false;
            }
        } else {
            return 0;
        }
    }

    /**
     * Revoke the shift code
     * @return boolean false if not authed
     */
    public function cancel_transfer() {
        if ($this->authed) {
            $cond = ['shiftcode' => ''];
            DB::update('devices', $cond, '`id`=' . $this->id);
            $this->shift = '';
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the authencation object (build-in)
     * @param boolean $display Whether in display form
     * @return mixed The Auth Array
     */
    public function get_auth($display=false) {
        if($display){
            $authz['location']= $this->access['location'];
            $authz['global'] = $this->access['global']; // The device should not have super access for security
            foreach($this->access['object'] as $obj){
                $authz['object'][] = HSobj_collection::hsref2name($obj, true);
            }
            return $authz;
        }else{
            return $this->access;
        }
        
    }

    /**
     * Register a device in the database
     * The Class for admin should inhert this class
     * @param string $hid The hardware address 
     * @param string $type The prefix of hardware address
     * @param type $auth  The auth string 
     * @param type $owner The device will be assigned to UID
     * @return string shift code
     */
    protected function register($hid, $type, array $auth = [], $owner = 0) {
        $hhid = $type . '_' . $hid;
        if (DB::select('devices', '`hid`=' . DB::get_str($hhid))) {
            return NULL;
        }
        $shift = randstr(5, true);
        DB::insert('devices', ['hid' => $hhid, 'owner' => $owner, 'shiftcode' => $shift, 'authz' => serialize($auth)]);
        return $shift;
    }

    /**
     * Change the auth of a device
     * Inherit this class in the admin class to use this function
     * @param array $auth
     * @return boolean 
     * @throws InvalidArgumentException
     */
    protected function change_auth(array $auth) {
        if (!$this->id) {
            throw new InvalidArgumentException('id was not setted', 5002);
        }
        $cond = ['authz' => serialize($auth)];
        DB::update('device', $cond, '`id`=' . $this->id);
        return true;
    }

}
