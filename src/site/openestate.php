<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2018 OpenEstate.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// require the base controller
/** @noinspection PhpIncludeInspection */
require_once(JPATH_COMPONENT . '/controller.php');

// require specific controller if requested
$controller = JRequest::getWord('controller');
if ($controller != null) {
    $path = JPATH_COMPONENT . '/controllers/' . $controller . '.php';
    if (file_exists($path)) {
        /** @noinspection PhpIncludeInspection */
        require_once $path;
    } else {
        $controller = '';
    }
}

// create the controller
$classname = 'OpenestateController' . $controller;

/** @var JControllerLegacy $controllerInstance */
$controllerInstance = new $classname();

// perform the requested task
$controllerInstance->execute(JRequest::getVar('task'));

// redirect if set by the controller
$controllerInstance->redirect();
