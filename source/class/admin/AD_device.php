<?php

/*
 * Copyright (C) 2016 zhc
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

require_once MY_ROOT . '/source/class/admin/admin_exception.php';

/**
 * Administration of User's devices
 * @package SmartHome
 * @author Zhu Chen <zhuchen@greatqq.com>
 */
class AD_device {

    /**
     * Register a new device
     * @param string $hid Hardware device address
     * @param int $owner The owner's id
     * @param string $name The device name
     * @param array $auth Authentication Array
     * @return boolean|string false if the device exist, or safe code of bind device 
     */
    public function register($hid, $owner = 0, $name = '', array $auth = ['location' => [], 'object' => []]) {
        $rslt = DB::select('devices', '`hid` = ' . DB::get_str($hid), '`id`', true);
        if ($rslt) {
            return false;
        }
        $cond['hid'] = $hid;
        $cond['owner'] = $owner;
        $cond['name'] = $name;
        $cond['authz'] = serialize($auth);
        if (!$owner) {
            $sfcode = randstr(5, true);
        } else {
            $sfcode = '';
        }
        $cond['shiftcode'] = $sfcode;
        DB::insert('devices', $cond);
        return $sfcode;
    }

    /**
     * Delete a device
     * @param int|string $id Hardware Address or ID
     * @return boolean Success or Failure False if device is not found
     */
    public function delete($id) {
        if (is_numeric($id)) {
            $rslt = DB::select('devices', '`id` = ' . intval($id), '`id`', true);
        } else {
            $rslt = DB::select('devices', '`hid` = ' . DB::get_str($id), '`id`', true);
        }
        if (!$rslt) {
            return false;
        }
        DB::delete('devices', '`id` = ' . $rslt['id']);
        return true;
    }

    /**
     * Make The device transfer to another user
     * Generate a random string as Secret Code
     * @param string $hid The hardware address
     * @param mixed $info The hardware info
     * @return boolean
     */
    public function transfer_device($hid, &$info = []) {
        if (is_numeric($hid)) {
            $hdinfo = DB::select('devices', '`id`=' . intval($hid), true);
        } else {
            $hdinfo = DB::select('devices', '`hid`=' . DB::get_str($hid), true);
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
     * Find a device
     * @param string $key Search Keywords
     * @param string $order the result order ASC or DESC
     * @return mixed 
     */
    public static function search_device($key,$order = 'DESC') {
        if (is_numeric($key)) {
            $rslt = DB::select('devices', '`id` = '. intval($key));
        } elseif(preg_match('/^[0-9a-zA-Z]{2,15}_[0-9a-fA-F]+$/',$key)){
            DB::select('devices','`hid` = '.DB::get_str($key));
        }else {
            $cond = preg_replace('/\*+/', '%', $key);
            $o = in_array($order, ['ASC','DESC']) ? $order : 'DESC';
            $rslt = DB::select('devices', '`name` LIKE ' . DB::get_str($cond) ,'`id` '.$o);
        }
        return $rslt;
    }

}
