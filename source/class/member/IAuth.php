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
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package SmartHome
 * @version 0.1.1
 */

/**
 * Auth Interface
 */
interface IAuth {

    /**
     * Every Auth Subject must have an unique id
     * @return string 
     */
    public function get_unique_id();

    /**
     * Get The Auth Table of Current Auth Subject
     * @param AuthType $type a const value of AuthType
     *      @see AuthType
     * @return string[]|int[]|boolean Auth Items
     *        Return a boolean with AuthType::AuthGlobal.<br/>
     *        Return a 1-D array (numeric indexed)with name of location.<br/>
     *        Return a 1-D array (numeric indexed) with the {@see hs_object::$hsref} HomeSeer Reference ID .<br/>
     */
    public function get_access($type);

    /**
     * Generate a security key for authencation
     */
    public function generate_seckey();

    /**
     * Get The base type 
     * @param authz $authz
     */
    public static function instance_from_authz(authz $authz);
}

/**
 * The Auth Type Enum
 */
class AuthType {

    /**
     * Global Access, For All Location and Objects
     */
    const AuthGlobal = 0x04;

    /**
     * Access for specific location
     */
    const AuthLocation = 0x02;

    /**
     * Access for specific object
     */
    const AuthObject = 0x01;

    /**
     * Access to User Admin Panel
     */
    const AuthAdminUser = 0x20;

    /**
     * Access to Device Admin Panel
     */
    const AuthAdminDevice = 0x40;
    
    /**
     * Acess to both Admin Panel
     */
    const AuthAdminAll =0x60;

    /**
     * Have all the access
     */
    const AuthRoot = 0xF4;

}

/**
 * The type of Security key Enum of Security 
 */
class SecType {

    /**
     * Access by using User Name and Password 
     * @see SH_User
     */
    const AuthUser = 1;
    const AuthDevice = 2;

}

trait Auth_location_object {

    /**
     * Check whether a user have an access to a location
     * @return boolean|string[]
     */
    public function get_access_location() {
        if (isset($this->auth['location']) && is_array($this->auth['location'])) {
            return $this->auth['location'];
        } else {
            return false;
        }
    }

    /**
     * Check whether a user have an access of an object
     * @return boolean|int[]
     */
    public function get_access_object() {
        if (isset($this->auth['object']) && is_array($this->auth['object'])) {
            return $this->auth['object'];
        } else {
            return false;
        }
    }

}
