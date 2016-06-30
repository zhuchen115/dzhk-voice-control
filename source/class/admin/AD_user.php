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

require_once MY_ROOT . '/source/class/member/member.php';
require_once MY_ROOT . '/source/class/admin/admin_exception.php';

/**
 * Manage the user and Groups
 * Add, Edit, Delete Users/Groups
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 * @subpackage Administration
 */
class AD_user extends member {

    public function __construct() {
        parent::__construct(true);
    }

    /**
     * Add a user account 
     * The Creation of Superuser is not allowed, but it is possible to append a user to Superuser group
     * @param string $user  Username
     * @param string $pass  Password
     * @param string $email User's email necessary for reset password 
     * @param int[] The group ID
     * @param array $extinfo Extra data to be added, serialized storage, only for display
     * @param array $ext Extra data with direct colum, will overwrite the previous data @since 0.1.12
     * @return int The code of register status <ol start="-2"><li>Email Invaild</li><li>Username Exists</li><li value="1">Success</li></ol>
     */
    public function useradd($user, $pass, $email, $groups, array $extinfo = array(), array $ext = array()) {
        if (is_array($groups)) {
            if (!in_array(1001, $groups)) {
                $groups[] = 1001;
            }
        } else {
            $g[] = intval($groups);
            $g[] = 1001;
            $groups = $g;
        }
        $ext['groups'] = implode(',', $groups);
        return parent::register($user, $pass, $email, $extinfo, $ext);
    }

    /**
     * Change a user's password forcely
     * @param string $user
     * @param string $pass 
     */
    public function passwd($user, $pass) {
        parent::passwd($user, $pass);
    }

    /**
     * Delete a user
     * @param string $user The username or userid
     * @param string $type The user type can only be 'id' or 'username', otherwise a exception will be thrown
     * @return boolean Whether successfully deleted user, Failiure if user not exist
     * @throws admin_exception
     */
    public function userdel($user, $type = 'id') {
        if ($type == 'id') {
            $cond = '`id`=' . intval($user);
        } elseif ($type == 'username') {
            $cond = '`username` = ' . DB::get_str($user);
        } else {
            throw new admin_exception('UserAdmin::userdel', 'Unknown Type', 5012);
        }
        $rslt = DB::select('member', $cond, '`id`', true);
        if (!$rslt) {
            return false;
        }
        if ($rslt['id'] == 1) {
            return false;
        }
        DB::delete('member', $cond);
        return true;
    }

    /**
     * Edit the user group
     * @param string|int $user UID or username
     * @param int[] $groups The groups numbers
     * @param string $mode Can Only be the following string 
     *          <ul><li>append(add)</li><li>change</li><li>leave</li></ul> Otherwise a Execetpion will be thrown
     * @param string $type Can ONLY be 'id' or 'username', otherwise exception will be thrown.
     * @return boolean FALSE if User Not Found
     * @throws admin_exception
     */
    public function usergroup($user, array $groups, $mode = 'append', $type = 'id') {
        if ($type == 'id') {
            $cond = '`id`=' . intval($user);
        } elseif ($type == 'username') {
            $cond = '`username` = ' . DB::get_str($user);
        } else {
            throw new admin_exception('UserAdmin::usergroup', 'Unknown Type', 5012);
        }
        $rslt = DB::select('member', $cond, '`id`', true);
        if (!$rslt) {
            return false;
        }
        $old_group = explode(',', $rslt['groups']);
        // Make Sure GID is a integer
        $old_group = array_map('intval', $old_group);
        $groups = array_map('intval', $groups);
        switch ($mode) {
            case 'append':
            case 'add':
                $newgroup = array_merge($groups, $old_group);
                break;
            case 'change':
                $newgroup = $groups;
                break;
            case 'leave':
            case 'remove':
                $newgroup = array_diff($old_group, $groups);
                break;
            default:
                throw new admin_exception('UserAdmin::usergroup', 'Unknown Mode', 5013);
        }
        unset($cond);
        $cond['groups'] = implode(',', $newgroup);
        DB::update('member',$cond, '`id` = ' . $rslt['id']);
        return true;
    }

    /**
     * Change the user's Auth
     * @param string $user Username or UID
     * @param array $auth Authencation to be modified
     * @param string $mode Can Only be the following string 
     *          <ul><li>append(add)</li><li>change</li><li>leave</li></ul> Otherwise a Execetpion will be thrown
     * @param type $type Can ONLY be 'id' or 'username'
     * @return boolean FALSE is not found user,
     * @throws admin_exception When the input of mode or type is not right
     */
    public function userauth($user, array $auth = [], $mode = 'append', $type = 'id') {
        if ($type == 'id') {
            $cond = '`id`=' . intval($user);
        } elseif ($type == 'username') {
            $cond = '`username` = ' . DB::get_str($user);
        } else {
            throw new admin_exception('UserAdmin::userauth', 'Unknown Type', 5012);
        }
        $rslt = DB::select('member', $cond, '`id`', true);
        if (!$rslt) {
            return false;
        }
        if (!isset($auth['location']) && !isset($auth['object']) && !isset($auth['global'])) {
            throw new admin_exception('UserAdmin::userauth', 'Empty Auth', 5014);
        }
        $arr = array_diff(array_keys($auth), ['location', 'global', 'object']);
        if (!empty($arr)) {
            throw new admin_exception('UserAdmin::userauth', 'Invaild Auth Array', 4010);
        }
        switch ($mode) {
            case 'append':
            case 'add':
                $newauth = array_merge_recursive($auth, unserialize($rslt['authz']));
                break;
            case 'remove':
            case 'leave':
                $auz = unserialize($rslt['authz']);
                $newauth = $auz;
                $newauth['object'] = isset($auth['object']) ? array_diff($auz['object'], $auth['object']) : $auz['object'];
                $newauth['location'] = isset($auth['location']) ? array_diff($auz['location'], $auth['location']) : $auz['location'];
                break;
            case 'change':
                $newauth = $auth;
                break;
            default :
                throw new admin_exception('AD_user::userauth', 'Unknown Mode', 5013);
        }
        DB::update('member', ['authz' => serialize($newauth)], '`id` = ' . $rslt['id']);
        return true;
    }

    /**
     * Add a User Group
     * @param string $name Group Name
     * @param string $desc Description
     * @param array $auth Authentication array
     * @param int $gid Specify a GID 
     * @return boolean FALSE if gid already exist
     */
    public function groupadd($name, $desc = '', array $auth = [], &$gid = 0) {
        if ($gid) {
            $rslt = DB::select('groups', '`id` = ' . intval($gid));
            if ($rslt) {
                return false;
            }
            $cond ['id'] = intval($gid);
        }
        $cond['name'] = $name;
        $cond['description'] = (string) $desc;
        $cond['authz'] = serialize($auth);
        DB::insert('groups', $cond);
        $rslt = DB::select('groups', '`name` = ' . DB::get_str($name) . ' AND `description` = ' . DB::get_str($desc), '`id` DESC', true);
        $gid = $rslt['id'];
        return true;
    }

    /**
     * Edit the authentication of Group
     * @param int $gid GID of group
     * @param array $auth  The authentication array
     * @param string $mode Can Only be the following string 
     *          <ul><li>append(add)</li><li>change</li><li>leave</li></ul> Otherwise a Execetpion will be thrown
     * @return boolean false if not found
     * @throws admin_exception Invaild arguments input
     */
    public function groupauth($gid, array $auth = [], $mode = 'change') {
        $rslt = DB::select('groups', '`id` = ' . intval($gid), '`id`', true);
        if (!$rslt) {
            return false;
        }
        if (empty($auth['location']) && empty($auth['object']) && !isset($auth['global'])) {
            throw new admin_exception('UserAdmin::groupauth', 'Empty Auth', 5014);
        }
        $arr = array_diff(array_keys($auth), ['location', 'global', 'object']);
        if (!empty($arr)) {
            throw new admin_exception('UserAdmin::groupauth', 'Invaild Auth Array', 4010);
        }
        switch ($mode) {
            case 'append':
            case 'add':
                $newauth = array_merge_recursive($auth, unserialize($rslt['authz']));
                break;
            case 'remove':
            case 'leave':
                $auz = unserialize($rslt['authz']);
                $newauth = $auz;
                $newauth['object'] = isset($auth['object']) ? array_diff($auz['object'], $auth['object']) : $auz['object'];
                $newauth['location'] = isset($auth['location']) ? array_diff($auz['location'], $auth['location']) : $auz['location'];
                break;
            case 'change':
                $newauth = $auth;
                break;
            default :
                throw new admin_exception('AD_user::userauth', 'Unknown Mode', 5013);
        }
        DB::update('groups', ['authz' => serialize($newauth)], '`id` = ' . intval($rslt['id']));
        return true;
    }

    /**
     * Change the Name or Description of Group
     * @param type $gid
     * @param string $name The name of the group, Use '/' for not change it.
     * @param string $desc The description of group, Use '/' for not change it.
     * @return boolean
     */
    public function groupmod($gid, $name = '/', $desc = '/') {
        $rslt = DB::select('groups', '`id` = ' . intval($gid), '`id`', true);
        if (!$rslt) {
            return false;
        }
        if ($name != '/') {
            $rslt['name'] = $name;
        }
        if ($desc != '/') {
            $rslt['description'] = $desc;
        }

        DB::update('groups', $rslt, '`id` = ' . intval($gid));
        return true;
    }

    /**
     * Delete a group
     * @param int $gid The group ID
     * @return boolean FALSE if group not exist or build in Account
     */
    public function groupdel($gid) {
        $gid1 = intval($gid);
        if (in_array($gid1, [1, 2, 3, 1000, 1001])) {
            return false; //Prevent Delete Build in Account 
        }
        $rslt = DB::select('groups', '`id` = ' . intval($gid));
        if (!$rslt) {
            return false;
        }
        DB::delete('groups', '`id` = ' . $gid);
        return true;
    }

    /**
     * Find (a) User(s) 
     * @param int|string $key
     * @return mixed 
     */
    public function usersearch($key) {
        if (is_numeric($key)) {
            return DB::select('member', '`id` = ' . intval($key));
        }
        if (preg_match('/\*/', $key)) {
            $cond = str_replace('*', '%', $key);
        } else {
            $cond = $key . '%';
        }
        if (preg_match('/@/', $cond)) {
            return DB::select('member', '`email` LIKE ' . DB::get_str($cond));
        } else {
            return DB::select('member', '`username` LIKE ' . DB::get_str($cond));
        }
    }

    /**
     * Get all the groups of user
     * @param int $uid UID
     * @return mixed groups
     */
    public function listgroups($uid) {
        $uinfo = DB::select('member', '`id` = ' . intval($uid), '`id`', true);
        $ug = explode(',', $uinfo['groups']);
        return $ug;
    }

    /**
     * Get All Groups
     * @return mixed groups
     */
    public static function get_groups_all() {
        return DB::select('groups', '1');
    }

    /**
     * Get all the locations 
     * @return mixed 
     */
    public static function get_location_all() {
        $rslt = DB::select('hsobjects', '1', '`id`');
        $d = [];
        $location1 = [];
        $location2 = [];
        $i = 0;
        foreach ($rslt as $r) {
            if (!in_array($r['location'], $location1)) {
                $d[$i++] = ['type' => 'location1', 'name' => $r['location2'] . '/ ' . $r['location']];
                $location1[] = $r['location'];
            }
            if (!in_array($r['location2'], $location2)) {
                $d[$i++] = ['type' => 'location2', 'name' => $r['location2']];
                $location2[] = $r['location2'];
            }
        }
        return $d;
    }

    /**
     * Get the Authentication Array 
     * @param int $uid UID or GID
     * @param string $type can ONLY be 'user' or 'group'
     * @return mixed Authentication array.
     */
    public function get_auth($uid, $type = 'user') {
        if ($type == 'user') {
            $uinfo = DB::select('member', '`id` = ' . intval($uid), '`id`', true);
        } elseif ($type == 'group') {
            $uinfo = DB::select('groups', '`id` = ' . intval($uid), '`id`', true);
        } else {
            throw new InvalidArgumentException('Unknown Authentication Type', 5004);
        }
        return unserialize($uinfo['authz']);
    }

    /**
     * Get the User Display Name
     * @param int  The user's id
     * @return string|boolean Return the User Display Name
     */
    public static function uid2name($uid) {
        $rslt = DB::select('member', '`id`=' . intval($uid), '`id`', true);
        if (!$rslt) {
            return false;
        }
        $ns = unserialize($rslt['extra']);
        return $ns['name'];
    }

}
