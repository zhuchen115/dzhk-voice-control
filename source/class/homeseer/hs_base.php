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

/**
 * Description of hs_base
 * @package HomeSeer
 * @author Zhu Chen <zhuchen@greatqq.com>
 */
require_once dirname(__FILE__) . '/hs_api.php';
require_once dirname(__FILE__) . '/hs_exception.php';

/**
 * Describe a homeseer controlled device
 */
class hs_object {

    /**
     * @var string the name of the object
     */
    public $name;

    /**
     * @var array the location of the object
     */
    public $location;

    /**
     * @var int the reference id of HS Server 
     */
    protected $hsref;

    /**
     * @var array the array of controls
     */
    protected $action;

    public function __construct($ref) {
        $this->hsref = intval($ref);
        $this->get_control_pairs();
    }

    /**
     * Sync the control pairs with HS Server
     */
    public function sync_control() {
        $this->get_control_pairs();
    }

    /**
     * Get Control Pairs from HS Server
     */
    private function get_control_pairs() {
        $hsapi = HS_API::getinstance();
        $hsctrl = $hsapi->get_control(['ref' => $this->hsref]);
        $i = 0;
        if(!array_key_exists('ControlPairs', $hsctrl)){
            return ;
        }      
        foreach ($hsctrl['ControlPairs'] as $ctrl) {
            list($label, $ctype) = $this->prase_label($ctrl['Label']);
            $this->action[$i]['label'] = strtolower($label);
            switch ($ctrl['ControlType']) {
                case 2:
                    $this->action[$i]['type'] = 'numeric';
                    $this->action[$i]['check'] = 'is_numeric';
                    break;
                case 3:
                case 4:
                case 8:
                case 11:
                    $this->action[$i]['type'] = 'string';
                    $this->action[$i]['check'] = 'in_array';
                    $this->action[$i]['check_args'] = $ctrl['ControlStringList'];
                    break;
                case 5:
                    $this->action[$i]['type'] = 'bool';
                    $this->action[$i]['check'] = 'true';
                    break;
                case 6:
                case 7:
                    $this->action[$i]['type'] = 'numeric';
                    $this->action[$i]['check'] = 'in_range';
                    $this->action[$i]['check_args'] = [intval($ctrl['Range']['RangeStart']), intval($ctrl['Range']['RangeEnd'])];
                    break;
                default:
            }
            $i++;
        }
        $this->name = $hsctrl['name'];
        $this->location = [$hsctrl['location'], $hsctrl['location2']];
    }

    /**
     * Prase the Lable for mathcing
     * @param string $label
     * @param array $range
     * @return string the lable for storage
     */
    private function prase_label($label, $range = NULL) {

        if (!$range) {
            return [trim($label),true];
        }
        if (!empty($range['RangeStatusPrefix'])) {
            return [trim($range['RangeStatusPrefix']), true];
        }
        if (!empty($range['RangeStatusSuffix'])) {
            $str = strrev(str_replace(strrev($range['RangeStatusSuffix']), strrev($label), 1));  //Make Sure just remove 1 suffix
            return [trim(str_replace('(value)', $str)), false];
        }
        return [trim(str_replace('(value)', $label)), false];
    }

    /**
     * Control the device 
     * @param string $value
     */
    protected function do_action($value) {
        $label = '';
        foreach ($this->action as $act) {
            if ($act['label'] == trim(strtolower($value))) {
                $label = $act['label'];
                break;
            }
        }
        $req = hs_api::getinstance();
        $arg['ref'] = $this->hsref;
        try {
            if ($label == '') {
                $arg['value'] = $this->check_value($value);
                $req->control_device_by_value($arg);
            } else {
                $arg['label'] = $label;
                $req->control_device_by_label($arg);
            }
        } catch (hs_exception $e) {
            throw $e;
        }
    }

    /**
     * Check the value of control
     * @param type $value
     * @return type
     */
    private function check_value($value) {
        $type = gettype($value);
        if (($type == 'integer') || ($type == 'double')) {
            $type = 'numeric';
        }
        foreach ($this->action as $act) {
            if ($act['type'] == $type) {
                $func = $act['check'];
                if (isset($act['check_args'])) {
                    if ($func($value, $act['check_args'])) {
                        return $value;
                    } else {
                        continue;
                    }
                } else {
                    if ($func($value)) {
                        return $value;
                    } else {
                        continue;
                    }
                }
            }
        }
        throw new hs_exception('hs_base', 'Type of Value match nothing', 4002);
    }

    /**
     * Call this function do perform an action on the device
     * @param IAuth $user
     * @param string $action
     * @return boolean Whether is successed to do this action
     */
    public function access_action(IAuth $user, $action) {
        if ($this->check_auth($user)) {
            $this->do_action($action);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Call this function set a label on device
     * @param IAuth $user
     * @param type $label
     * @return boolean Whether is successed to do this action
     */
    public function access_label(IAuth $user, $label) {
        if ($this->check_auth($user)) {
            $this->do_action($label);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Call this function to set a value on device
     * @param IAuth $user
     * @param string $value
     * @return boolean
     */
    public function accss_value(IAuth $user, $value) {
        if ($this->check_auth($user)) {
            $this->do_action($value);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check the user have access to this object
     * @param IAuth $user The authencation
     * @return boolean
     */
    private function check_auth(IAuth $user) {
        $authed = false;
        for ($i = 0; $i < 3; $i++) {
            $access = $user->get_access((1 << $i));/** @see AuthType */
            if (is_array($access)) {
                if (in_array($this->hsref, $access) && $i == 0) {                          //Check Object
                    $authed = true;
                    break;
                }
                if ($i == 1) {                                                      //Check Location
                    foreach ($this->location as $lo) {
                        if (in_array($lo, $access)) {
                            $authed = true;
                            break 2;
                        }
                    }
                }
            } else {                                                              //Check Root
                if ($access) {
                    $authed = true;
                    break;
                }
            }
        }
        return $authed;
    }

}

/**
 * The Collection and Finder of hs_object 
 * @see hs_object
 */
class HSobj_collection {

    private $dscond = NULL;

    /**
     * Find objects by name
     * @param array|boolean $name 1-D array for OR operation or just equals
     * @return \HSobj_collection    this content
     *      Use $obj->find_by_*->find_by_* for AND 
     * @throws InvalidArgumentException
     */
    public function find_by_name($name) {
        if (!($this->dscond == '')) {
            $this->dscond = $this->dscond . ' AND ';
        }
        if (is_array($name)) {
            $qs = '(';
            foreach ($name as $n) {
                if (!is_string($n)) {                                           //2-D array is  not allowed
                    throw new InvalidArgumentException('Error in Using HSobj_collection::find_by_name, only 1-D array or string can be used', 5001);
                }
                $qs = ' `name`=' . DB::get_str($n) . 'OR';
            }
            $qs = substr($qs, 0, -2) . ')';
        } else {
            $qs = '`name`=' . DB::get_str($name);
        }
        $this->dscond.=$qs;
        return $this;
    }

    /**
     * Find object by locations
     * @param mixed $loc
     * @return \HSobj_collection
     * @throws InvalidArgumentException
     */
    public function find_by_location($loc) {
        if (!($this->dscond == '')) {
            $this->dscond = $this->dscond . ' AND ';
        }
        if (is_array($loc)) {
            $qs = '(';
            foreach ($loc as $n) {
                if (!is_string($n)) {
                    throw new InvalidArgumentException('Error in Using HSobj_collection::find_by_location, only 1-D array or string can be used', 5001);
                }
                $qs = ' `location`=' . DB::get_str($n) . 'OR `location2`=' . $n . ' OR';
            }
            $qs = substr($qs, 0, -2) . ')';
        } else {
            $qs = '(`location`=' . DB::get_str($loc) . 'OR `location2`=' . DB::get_str($loc) . ')';
        }
        $this->dscond.=$qs;
        return $this;
    }

    /**
     * Get The object from id
     * @param int $id  hsref or the stored id 
     * @param string $type id or hsref
     * @return hs_object
     */
    public function get_object_id($id, $type = 'id') {
        $rslt = DB::select('hsobjects', '`' . $type . '` = ' . intval($id), '`id`', true);
        if (!$rslt) {
            return NULL;
        }
        return unserialize($r['object']);
    }

    /**
     * Get the final object and flush the query
     * @param boolean $hsobj Whether to get the HSObject
     * @return hs_object[] 
     */
    public function get_objects($hsobj = TRUE) {
        $cond = '';
        if (empty($this->dscond)) {
            $cond = '1';
        } else {
            $cond = $this->dscond;
        }
        $rslt = DB::select('hsobjects', $cond);
        if(!$hsobj){
            return $rslt;
        }
        $ret = null;
        foreach ($rslt as $r) {
            $ret[] = unserialize($r['object']);
        }
        $this->dscond = '';
        return $ret;
    }

    /**
     * Sync the control pairs
     */
    public static function hs_sync() {
        if (php_sapi_name() != 'cli') {
            set_time_limit(0);
        }
        $hsobj = DB::select('hsobjects', '1');
        foreach ($hsobj as $o) {
            $obj = unserialize($o['object']);
            $obj->sync_control();
            $upd['object'] = serialize($obj);
            DB::update('hsobjects', $upd, '`id`=' . $o['id']);
        }
    }

    /**
     * Initialize all the objects and location
     */
    public static function hs_init() {
        if (php_sapi_name() != 'cli') {
            set_time_limit(0);
        }
        $args['ref'] = 'all';
        $args['location'] = 'all';
        $args['location2'] = 'all';
        $hsapi = hs_api::getinstance();
        try {
            $ho = $hsapi->get_status($args);
        } catch (hs_exception $e) {
            die($e->getMessage());
        }
        $hsc = array();
        $i = 0;
        if (empty($ho['Devices'])) {                                            //It may happens when HS server return a table with no devices
            throw new hs_exception('HSobj_collection', 'Error in updating devices lists, No devices found on HS server', 4001);
        }
        foreach ($ho['Devices'] as $device) {
            $hscobj = new hs_object($device['ref']);
            $hsc[$i]['name'] = strtolower($device['name']);
            $hsc[$i]['hsref'] = $device['ref'];
            $hsc[$i]['location'] = strtolower($device['location']);
            $hsc[$i]['location2'] = strtolower($device['location2']);
            $hsc[$i]['object'] = serialize($hscobj);
            $i++;
        }
        DB::query('TRUNCATE TABLE `hsobjects`');
        DB::query('ALTER TABLE `hsobjects` AUTO_INCREMENT = 1');
        DB::insert('hsobjects', $hsc);
    }

    /**
     * Transfer the hsref to device name
     * @param int $hsref HomeSeer Reference ID 
     * @param boolean $nick Wheter for display name
     * @return boolean|string
     */
    public static function hsref2name($hsref, $nick = false) {
        $rslt = DB::select('hsobjects', '`hsref` =' . intval($hsref), '`id`', true);
        if (!$rslt) {
            return false;
        } else {
            if ($nick) {
                return $rslt['location2'] . '/' . $rslt['location'] . '/' . $rslt['name'];
            } else {
                return $rslt['name'];
            }
        }
    }

}
