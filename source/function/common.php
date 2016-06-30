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
 * Functions to be used globally in this web
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */
if (!defined('MY_ROOT')) {
    die("Access Denied");
}

/**
 * Check a email address
 * @param string $email
 * @return boolean 
 */
function check_email($email) {
    if (preg_match('/^([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i', $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Random a string with length of Len
 * @param int $len
 * @param boolean $read Whether for escape 0 and O
 * @return string
 */
function randstr($len,$read=false) {
    if($read){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
    }else{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    }
    $pass = '';
    for ($i = 0; $i < $len; $i++) {
        $pass.= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $pass;
}

/**
 * Check value in the range of condition
 * @param type $value
 * @param array $cond
 * @return boolean
 * @throws InvalidArgumentException
 */
function in_range($value, array $cond) {
    $start = $cond[0];
    $end = $cond[1];
    if ($end < $start) {
        throw new InvalidArgumentException('Error in calling function in_range, the strat value must greater than end value');
    }
    if ($value >= $start && $value <= $end) {
        return true;
    } else {
        return false;
    }
}

/**
 * Generate a secret key
 * @param string $info
 * @return string
 */
function generate_secret_key($info) {
    $time = hex2bin(base_convert(time(), 10, 16)); //4 byte
    $in = randstr(5) . $info . randstr(5);
    $ilen = strlen($info);
    $sec = randstr(10 + $ilen);
    $code = $sec ^ $in;
    $hlen = str_pad(base_convert($ilen, 10, 16), 4, '0', STR_PAD_LEFT); //4 byte
    $ha = hash('SHA256', $in . $time, true); //32 byte
    return base64_encode($ha . $time . $hlen . $sec . $code);
}

/**
 * Check the secret key
 * @param string $key
 * @param string $info get the infomation in the secret key.
 * @return boolean
 */
function check_secret_key($key, &$info = NULL) {
    $key = base64_decode($key);
    $ha = substr($key, 0, 32);
    $key = substr($key, 32);
    $t = substr($key, 0, 4);
    $len = intval(base_convert(substr($key, 4, 4), 16, 10));
    $sec = substr($key, 8, 10 + $len);
    $code = substr($key, 18 + $len);
    if (hash('SHA256', ($code ^ $sec) . $t, true) == $ha) {
        $info = substr($code ^ $sec, 5, $len);
        return true;
    } else {
        return false;
    }
}
