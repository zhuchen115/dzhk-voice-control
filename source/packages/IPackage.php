<?php

/*
 * Copyright (C) 2014 Zhu Chen
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
 * All the class to be loaded by core must implement this interface
 * @package ZFramework
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @version 0.1.2
 */
interface IPackage {

    /**
     * Get the required file list from action
     * @param string $action
     * @return string|string[] The include file(s)
     */
    public function get_includes($action);

    /**
     * Get the Function name to be called
     * @param string $action
     * @return string The function name of action
     */
    public function get_function($action);
}
