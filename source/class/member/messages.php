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
 * SMS Services between users 
 * Message can only send from user to user
 * @author zhuchen <zhuchen@greatqq.com>
 * @package ZFramework
 */
class messages {
    private $uid;
    /**
     * Instance the message by using uid or User Object
     * @param SH_User|int $uid
     * @throws authz_exception
     */
    public function __construct($uid) {
        if(is_integer($uid)){
            $this->uid =$uid;
        }
        if($uid instanceof SH_User){
            $this->uid = $uid->uid;
        }
        if(empty($this->uid)){
            throw new authz_exception("User","User Not Logged in",4003);
        }
    }
    /**
     * Send a Message to a user
     * @param type $message
     * @param int $to
     * @return boolean
     */
    public function send($message,$to){
        DB::insert('messages',['message'=>$message,'from'=>$this->uid,'target'=>intval($to),'time'=>time()]);
        return true;
    }
    /**
     * Query The user's messages
     * @return mixed The array of messages info
     */
    public function query(){
        $rslt = DB::select('messages','`target`='.$this->uid);
        $msg=[];
        foreach ($rslt as $r){
            $msg[]=['from'=>SH_User::uid2name($r['from']),'msg'=>$r['message'],'time'=>$r['time']];
        }
        return $msg;
    }
}
