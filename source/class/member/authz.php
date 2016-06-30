<?php

/*
 * Copyright (C) 2013 Zhu Chen
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
 * The authencation class for secret keys
 * All types of authencation methods must be able to tansform into secret code except nobody account.
 * @see IAuth::generate_seckey()
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */
class authz implements IAuth {

    private $seccode;
    private $auth;
    public $authtype;

    /**
     * Receive an Secret Code for Doing Action.
     * @param type $seccode
     * @throws authz_exception
     */
    public function __construct($seccode = '') {
        $this->seccode = !empty($seccode) ? $seccode : NULL;
        if (empty($seccode)) {
            throw new authz_exception('Secret Key', 'No Secrect Key', 4001);
        }
        $this->getauth();
    }

    /**
     * Get The Authencation Information by Secret Key
     * @throws authz_exception
     */
    private function getauth() {
        $authd = DB::select('authz', '`seckey`=' . DB::get_str($this->seccode), '`id`', true);
        if (!$authd) {
            $this->auth = array();
            throw new authz_exception('Secret Key', 'Not Exist', 4004);
        } else {
            if ($authd['timeout'] < time()) {
                $this->auth = array();
                DB::delete('authz', '`id`=' . DB::get_str($authd['id']));
                throw new authz_exception('Secret Key', 'Time Out', 4002);
            } else {
                $this->auth = unserialize($authd['authz']);
                $this->authtype = $authd['type'];
            }
        }
    }

    /**
     * Get the description of Authencation Type
     * @return string The Description Text
     */
    public function get_type() {
        switch ($this->authtype) {
            case SecType::AuthUser:
                return 'User';
            case SecType::AuthDevice:
                return 'Device';
        }
    }

    /**
     * Added Function for Smart Home 
     * @param AuthType $type
     * @return boolean|array
     * @throws Exception
     */
    public function get_access($type) {
        if (empty($this->auth)) {
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
                throw new Exception("Error in using authz::get_access, AuthType unknown");
        }
    }

use Auth_location_object {
        get_access_location as private;
        get_access_object as private;
    }

    /**
     * The Secret Key Don't generate any auth just indicate a security object
     * @return boolean
     */
    private function get_access_global() {
        return isset($this->auth['global']) ? true : false;
    }

    /**
     * @todo Regenerate a seccode
     * @return type
     */
    public function generate_seckey() {
        return $this->seccode;
    }

    /**
     * Get the SecKey
     */
    public function get_seckey() {
        return $this->seccode;
    }

    /**
     * Implements of IAuth, Not Suggested
     * @return string
     */
    public function get_unique_id() {
        return 'sec_' . $this->seccode;
    }

    /**
     * Delete current Secret Code
     */
    public function logout() {
        DB::delete('authz', '`seckey`=' . DB::get_str($this->seccode));
    }

    /**
     * Get the array of auth
     * @return array 
     */
    public function get_authd() {
        return $this->auth;
    }

    /**
     * Interface Implements No use it
     * @param authz $authz
     * @return \authz
     */
    public static function instance_from_authz(authz $authz) {
        return $authz;
    }

}
