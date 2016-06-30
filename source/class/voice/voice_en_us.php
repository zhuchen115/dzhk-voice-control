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
 * The Language Analyzer for Engish
 * Analyze the Syntax
 * Suggest use Google SyntaxNet Instead of this script
 * @package HomeSeer
 * @author zhuchen<zhuchen@greatqq.com>
 */
class voice_en_us {

    private $instr;
    private $strlist;
    private $location1;
    private $location2;
    private $names;
    private $result;

    /**
     * Initialize the class
     * @param string $in The string to be prased 
     */
    public function __construct($in) {
        $this->instr = $in;
        if (file_exists(MY_ROOT . '/data/cache/cache_locname.php')) {
            require_once MY_ROOT . '/data/cache/cache_locname.php';
            $this->location1 = $location1;
            $this->location2 = $location2;
            $this->names = $names;
        } else {
            $this->get_locname_db();
        }
        $this->pre_delete();
    }
    /**
     * Get the Locations and Names from database
     * @throws Exception
     */
    private function get_locname_db() {
        $rslt = DB::select('hsobjects', '1');
        foreach ($rslt as $r) {
            $this->location1[] = $r['location'];
            $this->location2[] = $r['location2'];
            $this->names[] = $r['name'];
        }
        $ocache = "<?php \n";
        $ocache .= '$location1 = ' . var_export($this->location1, true) . ";\n";
        $ocache .= '$location2 = ' . var_export($this->location2, true) . ";\n";
        $ocache .= '$names = ' . var_export($this->names, true) . ";\n";
        if (!file_put_contents(MY_ROOT . '/data/cache/cache_locname.php',$ocache)) {
            throw new Exception('Cannot Write Cacahe File, Make sure /data directory is writable', 5001);
        }
    }

    /**
     * Delete unused words
     */
    private function pre_delete() {
        $this->strlist[0] = preg_replace('/\b((a)?(an)?(the)?)\b/i', '', $this->instr);
        $this->strlist[0] = preg_replace('/\s{2,}/', ' ', $this->strlist[0]);
        $this->strlist[0] = preg_replace('/(s|es)\b/', '', $this->strlist[0]);
        $this->strlist[0] = strtolower($this->strlist[0]);
    }

    /**
     * Find out the locations 
     * @return \voice_en_us
     */
    private function prep_trans() {
        $atr = $this->strlist;
        $str = end($atr);
        $func = function($value) {
            return str_replace(' ', '\s', trim($value));
        };
        $loc1 = array_map($func, $this->location1);
        $loc2 = array_map($func, $this->location2);
        $reg = '/[^\b](?:in|on)?\s?(' . implode('|', $loc1) . '|' . implode('|', $loc2) . ')/';
        $mdel =[];    //String to remove
        //Find out location
        if (preg_match_all($reg, $str, $m, PREG_SET_ORDER)) {
            foreach ($m as $d) {
                $this->result['location'][] = $d[1];
                $mdel[] = $d[0];
            }
            $str = preg_replace('/\s{2,}/', ' ', str_replace($mdel, '', $str));
            
        }
        //Find out time, crontab not availiable on windows, Removed
        //if (preg_match('/in\s(january|february|march|april|may|june|july|august|september|october|november|december)/i', $str, $m)) {
            
        //}
        return $this;
    }

    /**
     * Find the verb in the sentense
     * @return \voice_en_us
     */
    private function verb_trans() {
        $atr = $this->strlist;
        $str = end($atr);
        if (preg_match('/^(switch|turn)\b\s\b(on|off)\b(.*)$/', $str, $m)) {    //Match Turn/Switch on/off ***
            $this->result['label'] = $m[2];
            $this->strlist[] = $m[3];
            return $this;
        }
        if (preg_match('/^(switch|turn)\b\s(.+?)\s(on|off)$/', $str, $m)) {       //Match Turn *** on/off
            $this->result['label'] = $m[3];
            $this->strlist[] = $m[2];
            return $this;
        }
        if (preg_match('/^(set|dim)\b\s(.+?)\sto\s(.+?)$/', $str, $m)) {            //Match Set/Dim *** to ***
            $this->result['value'] = $m[3];
            $this->strlist[] = $m[2];
            return $this;
        }
        if (preg_match('/^(close|open)\b\s(.*?)$/',$str,$m)){
            $this->result['label'] = $m[1];
            $this->strlist[]= $m[2];
            return $this;
        }
        if(preg_match('/^(lock|unlock)\b\s(.*?)$/',$str,$m)){
            $this->result['label'] = $m[1];
            $this->strlist[]= $m[2];
            return $this;
        }
        return $this;
    }
    /**
     * Find out the name of object
     * @return \voice_en_us
     */
    private function name_trans(){
        $atr = $this->strlist;
        $str = end($atr);
        $reg = '/('.implode('|',$this->names).')/';
        if(preg_match($reg,$str,$m)){
            $this->result['name']=$m[1];
            $this->strlist[]=str_replace($m[0], '', $str);
        }
        return $this;
    }
    /**
     * Perform Prase 
     * @return \voice_en_us
     */
    public function perform(){
        return $this->verb_trans()->prep_trans()->name_trans();
    }
    /**
     * Get The Result of the prasing
     * @return array
     */
    public function get_result(){
        return $this->result;
    }
}
