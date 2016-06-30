<?php

/*
 * Copyright (C) 2016 Zhu Chen
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
 * The class for administration
 * Front End Only
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */
class admin implements IPackage{
    public function get_includes($action) {
        switch($action){
            case 'user':
                return ['admin/AD_user','homeseer/hs_base'];
            case 'device':
                return ['admin/AD_device','admin/AD_user'];
        }
    }
    public function get_function($action) {
        if(!empty($_SESSION['admin'])){
            switch($action){
                case 'user';
                    return 'man_user';
                case 'device':
                    return 'man_device';
            }
        }else{
            return 'no_access';
        }
    }
    public function no_access(){
        echo '<h1>'._('You have no access to administration panel').'</h1>';
    }
    
    public function man_user(){
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['admin']||($_SESSION['admin_type']&0x20==0x20))){
            header('Location: ?action=dashboard');
        }
        $smarty= core::tpl();
        $hs = new HSobj_collection();
        $smarty->assign('groups',  AD_user::get_groups_all());
        $smarty->assign('locations',AD_user::get_location_all());
        $smarty->assign('objects',$hs->get_objects(false));
        $smarty->display('admin/user-index.tpl');
    }
    
    public function man_device() {
        if (session_status() == PHP_SESSION_NONE || empty($_SESSION['admin']||($_SESSION['admin_type']&0x40==0x40))){
            header('Location: ?action=dashboard');
        }
        $smarty= core::tpl();
        $devs = AD_device::search_device('*');
        foreach ($devs as $k=>$dev){
            $devs[$k]['owner'] = AD_user::uid2name($dev['owner']);
        }
        $smarty->assign('devices', $devs );
        $smarty->display('admin/device-index.tpl');
    }
}
