<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: openestate.php 2073 2013-02-13 14:54:18Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2013, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
require_once( JPATH_COMPONENT.'/controller.php' );

// Require specific controller if requested
$controller = JRequest::getWord('controller');
if($controller!=null) {
  $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
  if (file_exists($path)) {
    require_once $path;
  } else {
    $controller = '';
  }
}

// Create the controller
$classname  = 'OpenestateController'.$controller;
$controller = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
?>