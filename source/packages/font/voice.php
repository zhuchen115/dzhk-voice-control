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
 * Get the input from speech text 
 * 
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package SmartHome
 * @subpackage Voice-JSON-Frontend
 */
class voice implements IPackage {

    public function get_includes($action) {
        $co = ['voice/voice_en_us', 'homeseer/hs_base', 'member/IAuth', 'member/authz','member/SH_User'];
        return $co;
    }

    public function get_function($action) {
        switch ($action) {
            case 'msg':
            default:
                return 'inmsg';
        }
    }

    public function inmsg() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['userobj'])) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Not Logged In')]);
        } else {

            list($msg) = core::get_safe_input(INPUT_REQUEST, ['msg'], ['raw']);
            $hobj = new voice_en_us($msg);
            $hobj->perform();
            $hcol = new HSobj_collection();
            $rslt = $hobj->get_result();
            if (isset($rslt['location'])) {
                foreach ($rslt['location'] as $lo) {
                    $hcol->find_by_location($lo);
                }
            }
            $hcol->find_by_name($rslt['name']);
            $ok = 0;
            $fail = 0;
            $error = 0;
            $husr = unserialize($_SESSION['userobj']);
            $value = isset($rslt['label']) ? $rslt['label'] : $rslt['value'];
            foreach ($hcol->get_objects() as $obj) {
                try {
                    if ($obj->access_action($husr, $value)) {
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
            if ($ok) {
                $code = 1001;
            } else {
                $code = 4003;
            }
            echo json_encode(['errno' => $code, 'errmsg' => _('Success in Control').' : ' . $ok . '; ' . $fail .'  '. _('Object Returned Permission Deined')], JSON_UNESCAPED_UNICODE);
        }
    }

}
