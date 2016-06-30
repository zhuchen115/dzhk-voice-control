<?php

/*
 * Copyright (C) 2014 Zhu Chen
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
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 * @version 0.1.12
 */
if (!defined('MY_ROOT')) {
    die('Access Denied');
}

/**
 * The base class for register, login, change password, forget password 
 */
abstract class member {

    /**
     * Whether to operate by uid instead of username
     * @var bool if true  login and password function will use uid instead of username
     */
    protected $use_uid = FALSE;
    private $ucol = 'username';

    /**
     * Construct Function
     * @param bool $use_uid Whether to use uid instead of username
     */
    protected function __construct($use_uid = FALSE) {
        $this->use_uid = $use_uid;
        if ($use_uid) {
            $this->ucol = 'id';
        }
    }

    /**
     * Set The use uid
     * @param bool $use_uid
     */
    protected function set_use($use_uid) {
        $this->use_uid = $use_uid;
        if ($use_uid) {
            $this->ucol = 'id';
        }
    }

    /**
     * Regeister a user account 
     * @param string $user  Username
     * @param string $pass  Password
     * @param string $email User's email necessary for reset password 
     * @param array $extinfo Extra data to be added, serialized storage, only for display
     * @param array $ext Extra data with direct colum, will overwrite the previous data @since 0.1.12
     * @return boolean
     */
    public function register($user, $pass, $email, $extinfo = array(), array $ext = array()) {
        $user = trim($user);
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        if (!$this->check_avail($user)) {
            return -1;
        }
        if (!check_email($email)) {
            return -2;
        }
        $cont['username'] = $user;
        $cont['password'] = $pass;
        $cont['email'] = $email;
        $cont['extra'] = serialize($extinfo);
        $cont = array_merge($cont, $ext);
        
        DB::insert('member', $cont);
        $r = DB::select('member', '`username` = '.DB::get_str($user), '`id`', true);
        return $r['id'];
    }

    /**
     * Check whether username is available
     * @param string $user username to register
     * @return boolean whether this username can be registered 
     */
    public function check_avail($user) {
        $user = trim($user);
        $cond = "`username`=" . DB::get_str($user);
        $result = DB::select('member', $cond, '`id`', true);
        if ($result) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Login by username and password 
     * @param string $user the username
     * @param string $pass the password
     * @param array &$result the user info 
     * @return int the userid or 0 for failure
     */
    public function login($user, $pass, &$result = NULL) {
        $user = trim($user);
        if($this->use_uid){
            $cond = "`" . $this->ucol . "`=" . intval($user);
        }else{
            $cond = "`" . $this->ucol . "`=" . DB::get_str($user);
        }
        
        $result = DB::select('member', $cond, '`id`', true);
        $chk = password_verify($pass, $result['password']);
        if (!$chk) {
            return 0;
        }
        return $result['id'];
    }

    /**
     * Change the user's password 
     * @param string $user username
     * @param string $pass new password
     */
    protected function passwd($user, $pass) {
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        $user = trim($user);
        $con['password'] = $pass;
        if($this->use_uid){
            $cond = "`" . $this->ucol . "`=" . intval($user);
        }else{
            $cond = "`" . $this->ucol . "`=" . DB::get_str($user);
        }
        DB::update('member', $con, $cond);
    }

    /**
     * User Forget Password Stage 1
     * @param string $user
     */
    public function forgetpass($user) {
        $user = DB::get_str(trim($user));
        if($this->use_uid){
            $cond = "`" . $this->ucol . "`=" . intval($user);
        }else{
            $cond = "`" . $this->ucol . "`=" . DB::get_str($user);
        }
        $result = DB::select('member', $cond, '`id`', true);
        $seccode = generate_secret_key($result['uid']);
        MAIL::forgetpass($result['email'], $seccode);
    }

    /**
     * User change the password
     * @param string  $user username 
     * @param string $old  the old password of the user
     * @param string $new  the new password of the user
     * @return int the result of change password
     *        <ol start="-2"><li> Old password incorrect</li><li>Old password the same as new one</li><li value="1">Success</li></ol>
     */
    public function chpasswd($user, $old, $new) {
        if ($old == $new) {
            return -1;
        }
        $user = trim($user);
        if($this->use_uid){
            $cond = "`" . $this->ucol . "`=" . intval($user);
        }else{
            $cond = "`" . $this->ucol . "`=" . DB::get_str($user);
        }
        $result = DB::select('member', $cond, '`id`', true);
        if (!password_verify($old, $result['password'])) {
            return -2;
        }
        $this->passwd($user, $new);
        return 1;
    }

    /**
     * User forget password stage 2 , reset password
     * @param string $user The username
     * @param string $hash The Emailed Hash
     * @param string $new New Password
     * @return int 1 for success  0 for failure
     */
    public function fpasswd($user, $hash, $new) {
        $time = getdate();
        $user = trim($user);
        if (check_secret_key($hash)) {
            $this->passwd($user, $new);
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Logout the user
     */
    public function logout() {
        session_destroy();
    }
    /**
     * Change the user information
     * @param string|int $user The uid or username
     * @param mixed $info The info to be changed
     * @throws InvalidArgumentException
     */
    public function usermod($user,&$info=[]){
        if($this->use_uid){
            $cond = "`" . $this->ucol . "`=" . intval($user);
        }else{
            $cond = "`" . $this->ucol . "`=" . DB::get_str($user);
        }
        $uinfo = DB::select('member', $cond, '`id`', true);
        $exta = unserialize($uinfo['extra']);
        foreach($info as $k=>$in){
            if($k == 'id'){
                throw new InvalidArgumentException('The UID is not Allowed to change',5013);
            }
            if($k == 'password'){
                throw new InvalidArgumentException('Password Cannot Changed by this function',5014);
            }
            if(array_key_exists($k, $uinfo)){
                $uinfo[$k] = $in;
            }else{
                $exta[$k]=$in;
            }
        }
        $uinfo['extra'] = serialize($exta);
        DB::update('member', $uinfo,'`id`='.$uinfo['id']);
        $info=$uinfo;
    }

}
