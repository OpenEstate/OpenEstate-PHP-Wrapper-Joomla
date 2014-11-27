<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: admin.openestate.php 1341 2012-01-27 17:41:29Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2012, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load the html class
require_once( JApplicationHelper::getPath( 'admin_html' ) );

// Register CSS
$document = &JFactory::getDocument();
$css = $mainframe->getSiteURL().'administrator'.DS.'components'.DS.'com_openestate'.DS.'admin.openestate.css';
$document->addStyleSheet( $css, 'text/css', null, array() );

// Load joomla log system
//jimport( 'joomla.utilities.log' );
//$log = & JLog::getInstance();
//$log->addEntry(array("level" => 0,"status"=> 1, "comment" => "openestate :".$ret));

switch ($task) {
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
?>