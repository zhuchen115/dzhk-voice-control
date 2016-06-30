<?php

/*
 * Copyright (C) 2012 Zhu Chen
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
 *  MySQL Driver for the website, PHP7 Capable
 *  @author Chen Zhu <zhuchen@greatqq.com>
 *  @package ZFramework
 *  @version 0.1.20
 */
class mysql {

    var $link;
    var $config = array();

    function __construct($config) {
        $this->config = $config;
        $this->connect();
    }

    function connect() {
        if ($this->config['pconnect'] == 0) {
//$this->link=@mysql_connect($this->config['dbhost'],$this->config['dbuser'],$this->config['dbpass']);
            $this->link = new mysqli($this->config['dbhost'], $this->config['dbuser'], $this->config['dbpass'], $this->config['dbname']);
        } else {
            $this->link = new mysqli('p:' . $this->config['dbhost'], $this->config['dbuser'], $this->config['dbpass'], $this->config['dbname']);
        }
        if ($this->link->connect_error) {
//die("can not connect to mysql server".mysql_error());
            throw new mysqlexception($this->link);
        }
        /* if(!mysql_select_db($this->config['dbname'],$this->link))
          {
          die('Database error '.mysql_error());
          } */
        $this->query("set names 'UTF8'");
    }

    function query($sql) {
        $result = $this->link->query($sql);
        if ($this->link->error) {
            throw new mysqlexception($this->link, $sql);
        }
        return $result;
    }

    function get_str($value) {
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        $value = "'" . $this->link->real_escape_string($value) . "'";
        return $value;
    }

    function fetch_first($sql) {
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }
//$row=mysql_fetch_array($result);
        $row = $result->fetch_assoc();
        $result->free();
        return $row;
    }

    function fetch_all($sql) {
        $return = array();
        $num = 0;
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }
        while ($row = $result->fetch_assoc()) {
            $return[$num++] = $row;
        }
        $result->free();
        return $return;
    }

    function tpre($table) {
        if (!is_array($table)) {
            return $this->config['tpre'] . $table;
        } else {
            $r = "";
            foreach ($table as $t) {
                $r = $r . $this->config['tpre'] . $t . '` , `';
            }
            return substr($r, 0, -3);
        }
    }

}
