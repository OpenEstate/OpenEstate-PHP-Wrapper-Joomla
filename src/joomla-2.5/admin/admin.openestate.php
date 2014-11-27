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
defined('_JEXEC') or die('Restricted access');

// Load the html class
require_once( JApplicationHelper::getPath('admin_html') );

// Register CSS
$uri = &JFactory::getURI();
$css = $uri->root() . 'administrator' . DS . 'components' . DS . 'com_openestate' . DS . 'admin.openestate.css';

$document = &JFactory::getDocument();
$document->addStyleSheet($css, 'text/css', null, array());

// Load joomla log system
//jimport( 'joomla.utilities.log' );
//$log = & JLog::getInstance();
//$log->addEntry(array("level" => 0,"status"=> 1, "comment" => "openestate :".$ret));

$t = JRequest::getVar('task', '');
switch ($t) {
  case 'about':
    OpenEstateViews::about();
    break;
  case 'wrapper':
    OpenEstateViews::wrapper();
    break;
  default:
    OpenEstateViews::home();
    break;
}
