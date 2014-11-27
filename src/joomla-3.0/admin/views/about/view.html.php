<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: view.html.php 2071 2013-02-13 14:46:18Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2013, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OpenestateViewAbout extends JViewLegacy {
  function display($tpl = null) {
    require_once( JPATH_COMPONENT.'/helpers/openestate.php' );

    // allgemeine Komponenten
    OpenestateHelper::addTitle( 'about' );
    $this->sidebar = OpenestateHelper::buildSidebar( 'about' );
    $this->infobar = OpenestateHelper::buildInfobar( 'about' );

    // Template rendern
    parent::display( $tpl );
  }
}
?>