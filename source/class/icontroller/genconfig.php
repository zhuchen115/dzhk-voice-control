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
 * Generate a config file of the iControllerWeb
 * @package iControllerWeb
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @link https://github.com/sebbu/iControl-Web
 */
class genconfig {

    /**
     * @var array the config settng of iControl-Web
     */
    private $cfgarray = [
        /** Debug Setting */
        'showInfoScreen' => true,
        'coloredNetworkFeedback' => true,
        'pages' => [],
        'pagesWatch' => []
    ];

    public function getcfg() {
        return json_encode($this->cfgarray);
    }

    public function genpages() {
        
    }

}
