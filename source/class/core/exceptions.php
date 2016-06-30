<?php

/*
 * Copyright (C) 2015 Zhu Chen
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
 * The Exceptions of Zframework
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 * @version 0.1.3
 */

/**
 * The Exceptions of mysql driver
 */
class mysqlexception extends Exception {

    private $link;

    /**
     * Throwable mysql error
     * @param mysqli $link
     * @param string $sql
     */
    public function __construct(mysqli $link, $sql = '') {
        $this->link = $link;
        if ($link->connect_errno) {
            parent::__construct($link->connect_error, $link->connect_errno);
        }
        if ($link->errno) {
            if (_DEBUG_) {
                $msg = "Error in SQL Query: \n" . $link->error . "\nMySQL Server Infomation:\n" . $link->server_info;
                $msg.="\nClient Information: " . $link->client_info . "\n Query Data: \n" . $sql;
                parent::__construct($msg, $link->errno);
            } else {
                $msg = "An error occurred in the database with error no." . $link->errno . "Please Contact with the administrator for help";
                parent::__construct($msg, $link->errno);
            }
        }
    }

    /**
     * Get the Information of Mysql Client
     * @return string
     */
    public function get_client_info() {
        return $this->link->client_info;
    }

    /**
     * Get the version of Mysql client
     * @return int
     */
    public function get_client_version() {
        return $this->link->client_version;
    }

    /**
     * Get the information of Mysql Server
     * @return string
     */
    public function get_server_info() {
        return $this->link->server_info;
    }

    /**
     * Get the version of Mysql Server connected
     * @return string
     */
    public function get_server_version() {
        return $this->link->server_version;
    }

}

class authz_exception extends Exception {

    public function __construct($method, $message, $code = 0) {
        $msg = 'Auth Failure while using ' . $method . ', ' . $message;
        parent::__construct($msg, $code);
    }

}

class loader_exception extends LogicException {

    public function __construct($class, $message, $code = 0) {
        $msg = 'Error In Loading Class "' . $class . '", ' . $message;
        parent::__construct($msg, $code);
    }

}
