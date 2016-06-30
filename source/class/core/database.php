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
 * @package ZFramework
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @version 0.1.10
 */
if (!defined('MY_ROOT')) {
    die('Access Denied');
}
require_once(MY_ROOT . '/source/class/db/mysql.php');

/**
 * Mysql SQL maker
 * @static
 */
class DB {

    /**
     * The Database handler
     * @var mysql 
     */
    public static $db;

    /**
     * Get DB Server Connected
     * DO NOT call this function Other than initialization phase 
     * @param array $config
     */
    public static function init($config = array()) {
        self::$db = new mysql($config);
    }

    /**
     * Run Select Command on DB
     * @param string $table The Table name without prefix and quotes
     * @param string $where The Query Condtion, NO FILTER ON THIS ARGUMENTS
     * @see DB::get_str() for filtering strings
     * @param string $order SQL ordered by Default order id 
     * @param bool $first Whether to rerun the first row in 1-D array default false
     * @param string $col The column name of select default *
     * @return array 1-D associated or 2-D 
     */
    public static function select($table, $where, $order = '`id`', $first = false, $col = "*") {
        $sql = "SELECT " . $col . " FROM `" . self::$db->tpre($table) . "` WHERE " . $where . " ORDER BY " . $order;
        if ($first) {
            $result = self::$db->fetch_first($sql);
        } else {
            $result = self::$db->fetch_all($sql);
        }
        return $result;
    }

    /**
     * Update (a) row(s) in the table  
     * @param string $table The Table name
     * @param array $sets 1-D associated array
     * @param string $where The condtion 
     * @param bool $filter Whether to filter values in query default enabled
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function update($table, $sets, $where, $filter = 1) {
        $sql = "UPDATE `" . self::$db->tpre($table) . "` SET ";
        foreach ($sets as $key => $value) {
            if ($filter) {
                if (!is_numeric($value)) {
                    $value = self::$db->get_str($value);
                }
            }
            $sql.="`" . $key . "` =" . $value . " ,";
        }
        $sql = substr($sql, 0, -1);
        if ($filter && (empty(trim($where) || (trim($where) == '1')))) {
            throw new InvalidArgumentException("Error in using DB::update, the condtion is null or always true, Turn off filter to false query");
        }
        $sql.="WHERE " . $where;
        return self::$db->query($sql);
    }

    /**
     * Insert data to the table
     * @param string $table The table name 
     * @param array $sets 1-D associated or 2-D associated
     * @return bool
     */
    public static function insert($table, $sets = array()) {
        $sql = "INSERT INTO `" . self::$db->tpre($table) . "`(";
        if (@is_array($sets[0])) {
            foreach (array_keys($sets[0]) as $key) {
                $sql.='`' . $key . '`,';
            }
            $sql = substr($sql, 0, -1);
            $sql.=') VALUES ';
            foreach ($sets as $set) {
                $sql.="(";
                foreach ($set as $value) {
                    if (!is_numeric($value)) {
                        $sql.=self::$db->get_str($value);
                    } else {
                        $sql.=$value;
                    }
                    $sql.= ',';
                }
                $sql = substr($sql, 0, -1);
                $sql.="),";
            }
            $sql = substr($sql, 0, -1);
        } else {
            foreach (array_keys($sets) as $key) {
                $sql.='`' . $key . '`,';
            }
            $sql = substr($sql, 0, -1);
            $sql.=") VALUES (";
            foreach ($sets as $value) {
                if (!is_numeric($value)) {
                    $sql.=self::$db->get_str($value);
                } else {
                    $sql.=$value;
                }
                $sql.=",";
            }
            $sql = substr($sql, 0, -1);
            $sql.=')';
        }

        return self::$db->query($sql);
    }

    /**
     * Delete a record from table
     * @param string $table Table Name
     * @param string $condition Where condition
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function delete($table, $condition) {
        if (empty(trim($condition)) || trim($condition) == '1') {
            throw new InvalidArgumentException('Error in query delete, Your operation will delete all the table, terminated');
        }
        $sql = "DELETE FROM `" . self::$db->tpre($table) . "` WHERE " . $condition;
        return self::$db->query($sql);
    }

    /**
     * Optimize a table with MyISAM storage
     * @param string $table
     * @return bool
     */
    public static function opt($table) {
        $table = trim($table);
        $sql = "OPTIMIZE TABLE `" . $table . "`";
        return self::$db->query($sql);
    }

    /**
     * Query An Inner Join
     * @param string $stable Source Table, 
     * @param string $atable The second source table 
     * @param string $acol The column to be selected in the second table
     * @param string $acolw The Inner join column in second table
     * @param string $scol The column name of first table for linking the value with $aclow
     * @param string $cond The Query Condition
     * @param string $order result Order
     * @param bool $first Whether just return the first line
     * @return array 1-D or 2-D associated 
     */
    public static function ijoin($stable, $atable, $acol, $acolw, $scol, $cond, $order = 'id', $first = false) {
        $sql = "SELECT " . self::$db->tpre($stable) . ".*,`" . self::$db->tpre($atable) . "`.`" . $acol . "` FROM `" . self::$db->tpre($stable) . "` INNER JOIN `" . self::$db->tpre($atable) . "` ON `" . $atable . "`.`" . $acolw . "` = `" . self::$db->tpre($stable) . "`.`" . $scol . "` WHERE " . $cond . " ORDER BY `" . self::$db->tpre($stable) . '`.`' . $order . '`';
        if ($first) {
            $result = self::$db->fetch_first($sql);
        } else {
            $result = self::$db->fetch_all($sql);
        }
        return $result;
    }

    /**
     * Query an SQL directly
     * @param string $sql
     * @return MySQL Rescources
     */
    public static function query($sql) {
        return self::$db->query($sql);
    }

    /**
     * Filter the data to query safely
     * @param string $str
     * @return string
     */
    public static function get_str($str) {
        return self::$db->get_str($str);
    }

    /**
     * To use the prefix of table name
     * @param string $table
     * @return string
     */
    public static function tpre($table) {
        return self::$db->tpre($table);
    }

}
