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
 * The Functions to be used in API
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */

/**
 * Generate a token , use Token instead of Username and password 
 * @param int $uid
 * @return string
 */
function gentoken($uid) {  //生成API的访问密钥
    $tr = randstr(10) . time() . $uid . randstr(10);
    $sec = randstr(4);
    $st = base_convert($uid, 10, 16);
    if (strlen($st) % 2 != 0) {
        $st = '0' . $st;
    }
    $h = str_pad(hex2bin($st), 4, '0', STR_PAD_LEFT);
    $r = sha1($tr, true) . $sec . $h . ($h ^ $sec) . hex2bin(base_convert(time(), 10, 16));
    return base64_encode($r);
}

/**
 * Check whether token is outof date
 * @param string $token
 * @return boolean
 */
function checktoken($token, $outtime = 2592000) { //检查API的访问密钥
    $token = base64_decode($token);
    $token = substr($token, 20); //删除前面的sha1
    $sec = substr($token, 0, 4);
    $huid = substr($token, 4, 4);
    $enc = substr($token, 8, 4);
    $time = base_convert(bin2hex(substr($token, 12)), 16, 10);
    if (($time + $outtime) < time() || ($huid ^ $sec) != $enc) {
        return false;
    } else {
        return true;
    }
}

/**
 * Make the base64 encoded data transfer over HTTP
 * @param string $token
 * @return string
 */
function getsid($token) {
    $token = str_replace('+', '-', $token);
    $token = str_replace('/', ',', $token);
    return $token;
}
