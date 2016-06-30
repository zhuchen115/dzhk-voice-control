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
 * The Exception of Administration Class
 * 
 * @author Zhu Chen <zhuchen@greatqq.com>
 */
class admin_exception extends Exception{
    public function __construct($operator ="",$message = "", $code = 0, \Exception $previous = null) {
        parent::__construct("Error in Module $operator".$message, $code, $previous);
    }
}
