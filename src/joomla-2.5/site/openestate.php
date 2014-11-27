<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2014 OpenEstate.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

// require specific controller if requested
$controller = JRequest::getWord('controller');
if($controller!=null) {
  $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
  if (file_exists($path)) {
    require_once $path;
  } else {
    $controller = '';
  }
}

// create the controller
$classname  = 'OpenEstateController'.$controller;
$controllerInstance = new $classname( );

// perform the requested task
$controllerInstance->execute( JRequest::getVar( 'task' ) );

// redirect if set by the controller
$controllerInstance->redirect();
