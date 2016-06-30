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
 * Class for manage the users and groups
 *
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */
class useradmin implements IPackage {

    /**
     *  Implements the interface
     * @param string $action
     * @return string
     */
    public function get_function($action) {
        switch ($action) {
            case 'usearch':
                return 'search_user';
            case 'passwd':
                return 'change_password';
            case 'chemail':
                return 'change_email';
            case 'chname':
                return 'change_name';
            case 'uremove':
                return 'remove_user';
            case 'lgroups':
                return 'get_groups';
            case 'uauth':
                return 'get_auth';
            case 'uadd':
                return 'user_add';
            case 'euauth':
                return 'edit_uauth';
            case 'gadd':
                return 'group_add';
            case 'egauth':
                return 'edit_gauth';
            case 'gdel':
                return 'remove_group';
            case 'gauth':
                return 'get_gauth';
            case 'ugroups':
                return 'set_ugroups';
            case 'ginfo':
                return 'edit_group';
        }
    }

    /**
     * Implements the interface 
     * @param string $action 
     * @return string[] 
     */
    public function get_includes($action) {
        switch ($action) {
            case 'uauth':
                return ['admin/AD_user', 'homeseer/hs_base'];
            default:
                return ['admin/AD_user'];
        }
    }

    /**
     * Search A User
     */
    public function search_user() {
        list($key) = core::get_safe_input(INPUT_REQUEST, ['keyword'], ['raw']);
        $hsobj = new AD_user();
        $rslt = $hsobj->usersearch($key);
        $disp = [];
        foreach ($rslt as $r) {
            $r['extra'] = unserialize($r['extra']);
            $gps = (strlen($r['groups']) > 10) ? substr($r['groups'], 0, 8) . '...' : $r['groups'];
            $disp[] = ['id' => $r['id'], 'username' => $r['username'], 'email' => $r['email'], 'groups' => $gps, 'name' => isset($r['extra']['name']) ? $r['extra']['name'] : ''];
        }
        echo json_encode($disp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Change the password of User
     */
    public function change_password() {
        list($uid, $pass) = core::get_safe_input(INPUT_REQUEST, ['uid', 'newpass'], ['int', 'raw']);
        $hsobj = new AD_user();
        $hsobj->passwd(intval($uid), $pass);
        echo json_encode(['errno' => 1001, 'errmsg' => _('Password Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Change the Email of user
     */
    public function change_email() {
        list($uid, $email) = core::get_safe_input(INPUT_REQUEST, ['uid', 'email'], ['int', 'email']);
        if (!check_email($email)) {
            echo json_encode(['errno' => 4002, 'errmsg' => _('Wrong Email Address')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $hsobj = new AD_user();
            try {
                $d = ['email' => $email];
                $hsobj->usermod($uid, $d);
                echo json_encode(['errno' => 1001, 'errmsg' => _('Email Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } catch (InvalidArgumentException $ex) {
                echo json_encode(['errno' => 4004, 'errmsg' => _('User Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * Change the name of group
     */
    public function change_name() {
        list($uid, $name) = core::get_safe_input(INPUT_REQUEST, ['uid', 'name'], ['int', 'raw']);
        $usobj = new AD_user();
        try {
            $d = ['name' => $name];
            $usobj->usermod(intval($uid), $d);
            echo json_encode(['errno' => 1001, 'errmsg' => _('Name Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (InvalidArgumentException $ex) {
            echo json_encode(['errno' => 4004, 'errmsg' => _('User Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Remove the user
     */
    public function remove_user() {
        list($uid) = core::get_safe_input(INPUT_REQUEST, ['uid'], ['int'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $usobj = new AD_user();
        if ($usobj->userdel($uid)) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('User Deleted')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4004, 'errmsg' => _('User Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get the user's group
     */
    public function get_groups() {
        list($uid) = core::get_safe_input(INPUT_REQUEST, ['uid'], ['int']);
        $usobj = new AD_user();
        $rslt = $usobj->listgroups($uid);
        echo json_encode($rslt, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the auth of a user
     */
    public function get_auth() {
        list($uid) = core::get_safe_input(INPUT_REQUEST, ['uid'], ['int']);
        $usobj = new AD_user();
        $rslt = $usobj->get_auth($uid);

        echo json_encode($rslt, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a user
     */
    public function user_add() {
        list($user, $pass, $email,$name) = core::get_safe_input(INPUT_REQUEST, ['username', 'password', 'email','name'], ['fname', 'raw', 'email','raw']);
        list($groups) = core::get_safe_input(INPUT_REQUEST, ['groups'], ['int'], true);
        $usobj = new AD_user();
        $r = $usobj->useradd($user, $pass, $email, $groups,['name'=>$name]);
        switch ($r) {
            case -1:
                echo json_encode(['errno' => 4002, 'errmsg' => _('User Existes')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                break;
            case -2:
                echo json_encode(['errno' => 4001, 'errmsg' => _('Email Invaild')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                break;
            default:
                if ($r > 1) {
                    echo json_encode(['errno' => 1001, 'errmsg' => _('Success'), 'uid' => $r], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
        }
    }

    /**
     * Edit the access of a user
     */
    public function edit_uauth() {
        list($uid, $glob) = core::get_safe_input(INPUT_REQUEST, ['uid', 'global'], ['int', 'int']);
        list($location, $objects) = core::get_safe_input(INPUT_REQUEST, ['location', 'objects'], ['raw', 'int'], true);
        if (is_array($location)) {
            foreach ($location as $k => $loc) {
                if (preg_match('/^(.*?)\s?\/\s(.*?)$/', $loc, $m)) {
                    $location[$k] = $m[2];
                }
            }
        } else {
            if (preg_match('/^(.*?)\s?\/\s(.*?)$/', $location, $m)) {
                $location[0] = $m[2];
            }
        }
        $usobj = new AD_user();
        if ($usobj->userauth($uid, ['location' => $location, 'object' => $objects], 'change')) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Auth Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4004, 'errmsg' => _('User Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Assign a user to a group
     */
    public function set_ugroups() {
        list($groups) = core::get_safe_input(INPUT_REQUEST, ['groups'], ['int'], true);
        list($uid) = core::get_safe_input(INPUT_REQUEST, ['uid'], ['int']);
        $usobj = new AD_user();
        if ($usobj->usergroup($uid, $groups, 'change')) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Groups Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4004, 'errmsg' => _('User Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Add a group
     */
    public function group_add() {
        list($gid, $gname, $gdesc, $global) = core::get_safe_input(INPUT_REQUEST, ['gid', 'gname', 'desc', 'global'], ['int', 'words', 'words', 'int']);
        list($location, $object) = core::get_safe_input(INPUT_REQUEST, ['location', 'object'], ['words', 'int'], true);
        $usobj = new AD_user();
        if ($usobj->groupadd($gname, $gdesc, ['global' => (boolean) $global, 'location' => $location, 'object' => $object], $gid)) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Group Added'), 'gid' => $gid], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4002, 'errmsg' => _('GID Exists')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Edit the access of a group
     */
    public function edit_gauth() {
        list($gid, $glob) = core::get_safe_input(INPUT_REQUEST, ['gid', 'global'], ['int', 'int']);
        list($location, $objects) = core::get_safe_input(INPUT_REQUEST, ['location', 'objects'], ['words', 'int'], true);
        $usobj = new AD_user();
        if ($usobj->groupauth($gid, ['location' => $location, 'object' => $objects, 'global' => (boolean) $glob], 'change')) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Auth Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4004, 'errmsg' => _('Group Not found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Remove a group
     */
    public function remove_group() {
        list($gid) = core::get_safe_input(INPUT_REQUEST, ['gid'], ['int'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $usobj = new AD_user();
        if ($usobj->groupdel($gid)) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Group Deleted')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4004, 'errmsg' => _('Group Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get the auth of a group
     */
    public function get_gauth() {
        list($gid) = core::get_safe_input(INPUT_REQUEST, ['gid'], ['int']);
        $usobj = new AD_user();
        $rslt = $usobj->get_auth($gid, 'group');
        echo json_encode($rslt, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Edit the group name and description
     */
    public function edit_group() {
        list($gid, $name, $desc) = core::get_safe_input(INPUT_REQUEST, ['gid', 'name', 'desc'], ['int', 'raw', 'raw']);
        $usobj = new AD_user();
        if ($usobj->groupmod($gid, $name, $desc)) {
            echo json_encode(['errno' => 1001, 'errmsg' => _('Group Changed')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['errno' => 4004, 'errmsg' => _('Group Not Found')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

}
