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
 * The SmartHome Gatway
 * @package SmartHome
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @version 0.0.1_HomeSeer
 * @todo Debug The Package
 */
class smarthome implements IPackage {
    /**
     * Include the required class
     * @param string $action  The action to do
     * @return string
     */
    public function get_includes($action) {
        $co = ['member/IAuth','member/authz'];
        switch ($action) {
            case 'setval':
            case 'setlabel':
                $co[] = 'homeseer/hs_base';
                return $co;
        }
    }
    /**
     * Implemnets the interface, Get the function 
     * @param type $action
     * @return string
     */
    public function get_function($action) {
        switch ($action) {
            case 'setval':
            case 'setlabel':
                return 'control_device';
        }
    }
    /**
     * Get the device controlled 
     * @return type
     */
    public function control_device() {
        if (isset($_REQUEST['ref'])) {
            list($ref, $value) = core::get_safe_input(INPUT_REQUEST, ['ref', 'value'], ['raw', 'raw']);
            return $this->ctrldev_by_ref($ref, $value);
        }
        if ((!isset($_REQUEST['location'])) && (!isset($_REQUEST['name']))) {
            echo json_encode(['errno' => 4003, 'errmsg' => _('Bad Request')], JSON_UNESCAPED_UNICODE);
            return;
        } else {
            $this->ctrldev_by_locname();
        }
    }

    /**
     * Control device by location and name
     * Non unique command
     * @return type
     */
    private function ctrldev_by_locname() {
        list($sec) = core::get_safe_input(INPUT_REQUEST, ['seckey'], [NULL]);
        try {
            $auz = new authz($sec);
        } catch (authz_exception $ex) {
            switch ($ex->getCode()) {
                case 3001:
                    echo json_encode(['errno' => 4004, 'errmsg' => _('Secret Key Generate From Device')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4004:
                    echo json_encode(['errno' => 4002, 'errmsg' => _('Secret Key Not Exist')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4002:
                    echo json_encode(['errno' => 4005, 'errmsg' => _('Secre Key Expired')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4001:
                default:
                    echo json_encode(['errno' => 4001, 'errmsg' => _('Bad Request')], JSON_UNESCAPED_UNICODE);
                    break;
            }
            return;
        }
        list($location, $name) = core::get_safe_input(INPUT_REQUEST, ['location', 'name'], ['raw', 'raw']);
        $hcol = new HSobj_collection();
        if (is_array($location)) {
            foreach ($location as $loc) {
                $hcol->find_by_location($loc);
            }
        } else {
            $hcol->find_by_location($loc);
        }
        if (is_array($name)) {
            foreach ($name as $n) {
                $hcol->find_by_name($n);
            }
        } else {
            $hcol->find_by_name($name);
        }
        list($value) = core::get_safe_input(INPUT_REQUEST, ['value'], ['raw']);
        $ok = 0; $fail = 0;$error = 0;
        foreach ($hcol->get_objects() as $obj) {
            try {
                if ($obj->access_action($auz, $value)) {
                    $ok++;
                } else {
                    $fail++;
                }
            } catch (hs_exception $ex) {
                switch ($ex->getCode()) {
                    case 5001:
                    case 5002:
                        echo json_encode(['errno' => 5001, 'errmsg' => _('Internal Server Error')]);
                        return;
                    case 4002:
                        $error++;
                }
            }
        }
        if($ok){
            $code = 1001;
        }else{
            $code = 4003;
        }
        echo json_encode(['errcode'=>$code,'errmsg'=>_('Success in Control ').$ok.'; '.$fail._('Object Returned Permission Deined')], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Control the device by id or hsref
     * @param int $ref
     * @param int $value
     * @return type
     */
    private function ctrldev_by_ref($ref, $value) {
        list($sec) = core::get_safe_input(INPUT_REQUEST, ['seckey'], [NULL]);
        try {
            $auz = new authz($sec);
        } catch (authz_exception $ex) {
            switch ($ex->getCode()) {
                case 3001:
                    echo json_encode(['errno' => 4004, 'errmsg' => _('Secret Key Generate From Device')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4004:
                    echo json_encode(['errno' => 4002, 'errmsg' => _('Secret Key Not Exist')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4002:
                    echo json_encode(['errno' => 4005, 'errmsg' => _('Secre Key Expired')], JSON_UNESCAPED_UNICODE);
                    break;
                case 4001:
                default:
                    echo json_encode(['errno' => 4001, 'errmsg' => _('Bad Request')], JSON_UNESCAPED_UNICODE);
                    break;
            }
            return;
        }
        $hcol = new HSobj_collection();
        $obj = $hcol->get_object_id($ref, 'hsref');
        if (!$obj) {
            $obj = $hcol->get_object_id($ref);
        }
        if (!$obj) {
            echo json_encode(['errno' => 4004, 'errmsg' => _('Object Not Found')], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            if ($obj->access_action($auz, $value)) {
                echo json_encode(['errno' => 1001, 'errmsg' => _('Success')]);
            } else {
                echo json_encode(['errno' => 4003, 'errmsg' => _('Permission Denied')], JSON_UNESCAPED_UNICODE);
            }
        } catch (hs_exception $ex) {
            switch ($ex->getCode()) {
                case 4002:
                    echo json_encode(['errno' => 4001, 'errmsg' => _('Error In Requested Value')], JSON_UNESCAPED_UNICODE);
                    break;
                case 5001:
                case 5002:
                    echo json_encode(['errno' => 5001, 'errmsg' => _('Internal Server Error')], JSON_UNESCAPED_UNICODE);
                    break;
            }
            return;
        }
    }

}
