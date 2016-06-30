<?php

/*
 * Copyright (C) 2016 zhc
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
 * The user center
 * User is possible to change their password and other actions
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */
class ucenter implements IPackage{
    public function get_function($action) {
        switch($action){
            case 'login':
                return 'login';
            case 'health':
                return 'health_report';
            case 'seneor':
                return 'sensor_data';
            case 'setting':
                return 'user_setting';
            case 'device':
                return 'user_device';
            case 'message':
                return 'user_message';
            case 'logout':
                return 'logout';
            case 'dashboard':
            default:
                return 'dashboard';
        }
    }
    public function get_includes($action) {
        $co =['member/messages'];
        switch($action){
            case 'login':
                return [];
            case 'device':
                $co[]='member/IAuth';
                $co[]='member/SH_User';
                return $co;
            case 'dashboard':
            default:
                return ['member/messages'];
                
        }
    }
    public function logout(){
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);
        session_destroy();
        header('Location: /');
    }
    public function login(){
        
        $smarty= core::tpl();
        $smarty->display('member/login.tpl');
    }
    public function dashboard(){
        if(empty($_SESSION['uid'])){
            return $this->login();
        }else{
            $smarty = core::tpl();
            $msg = new messages(intval($_SESSION['uid']));
            $smarty->assign('msg',$msg->query());
            $smarty->display('member/index.tpl');
        }
    }
    public function health_report(){
        if(empty($_SESSION['uid'])){
            return $this->login();
        }else{
            
        }
    }
    public function user_setting(){
        if(empty($_SESSION['uid'])){
            return $this->login();
        }else{
            $smarty = core::tpl();
            $info =$_SESSION['uinfo'];
            $info['extra']= unserialize($info['extra']);
            $smarty->assign('user',$info);
            $smarty->display('member/setting.tpl','u_'.$_SESSION['uid']);
        }
    }
    public function user_device(){
        if(empty($_SESSION['uid'])){
            return $this->login();
        }else{
            $smarty = core::tpl();
            $huser = unserialize($_SESSION['userobj']);
            $msg = new messages(intval($_SESSION['uid']));
            $smarty->assign('msg',$msg->query());
            $smarty->assign('devices',$huser->get_devices());
            $smarty->display('devices/index.tpl','u_'.$_SESSION['uid']);
        }
    }
}
