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
 * Send Request to HomeSeer Server
 * @package HomeSeer
 * @author Zhu Chen <zhuchen@greatqq.com>
 */
class hs_api {

    private $hcurl;

    /**
     * Get the instane
     * @staticvar hs_api $obj
     * @return hs_api
     */
    public static function getinstance() {
        static $obj = NULL;
        if ($obj == NULL) {
            $obj = new hs_api();
        }
        return $obj;
    }

    /**
     * No Construction
     */
    private function __construct() {
        
    }

    /**
     * Make a GET request to HS Server
     * @param string $api
     * @return string
     */
    private function make_request($api) {
        $this->hcurl = curl_init($api);
        curl_setopt($this->hcurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->hcurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->hcurl, CURLOPT_AUTOREFERER, true);
        $result = curl_exec($this->hcurl);
        curl_close($this->hcurl);
        return $result;
    }

    /**
     * Magic Method to call generate request url;
     * @see http://php.net/manual/zh/language.oop5.overloading.php#object.call
     * @param string $name
     * @param array $arguments
     * @return type
     * @throws hs_exception
     */
    public function __call($name, $arguments) {
        $url = HS_API . '/JSON?request=' . $this->transfer_args($name) . $this->transfer_args($arguments[0]);
        $res = $this->make_request($url);
        if (!$res) {
            throw new hs_exception('HSAPI.CURL', 'Error in Make a request to Server', 5002);
        }
        if (trim($res) == 'error') {
            throw new hs_exception('HSAPI.RESPONSE', 'Server Returned Error', 5001);
        }
        if (trim($res) == 'ok') {
            return 'ok';
        }
        return json_decode($res, true);
    }

    /**
     * Transfer Argument of __call 
     * @param mixed $name
     * @return string
     */
    private function transfer_args($name) {
        if (is_string($name)) { // For Request
            return str_replace('_', '', $name);
        }
        if (is_array($name)) {//For Args
            $qstr = '';
            foreach ($name as $k => $n) {
                $qstr.='&' . $k . '=' . $n;
            }
            return $qstr;
        }
    }

}
