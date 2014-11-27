<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: view.html.php 594 2010-12-12 01:37:49Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class OpenestateViewListing extends JView {
  function display($tpl = null) {

    $parameters = OpenEstateWrapper::getParameters();
    if ($parameters==null) {
      $this->assignRef( 'content', '<h2>'.JText::_( 'ERROR' ).'!</h2><p>'.JText::_( 'CANT_LOAD_COMPONENT_SETTINGS' ).'</p>' );
      parent::display($tpl);
      return;
    }
    $scriptPath = OpenEstateWrapper::getScriptPath( $parameters );
    $scriptUrl = OpenEstateWrapper::getScriptUrl( $parameters );

    // Skript-Umgebung ggf. einbinden
    if (!defined('IMMOTOOL_BASE_URL')) {
      define('IMMOTOOL_BASE_URL', $scriptUrl);
    }
    if (!defined('IMMOTOOL_BASE_PATH')) {
      if (!is_dir($scriptPath)) {
        $this->assignRef( 'content', '<h2>'.JText::_( 'ERROR' ).'!</h2><p>'.JText::_( 'CANT_FIND_SCRIPTS' ).'</p>' );
        parent::display($tpl);
        return;
      }
      $result = OpenEstateWrapper::initEnvironment( $scriptPath, false );
      if (is_string($result)) {
        $this->assignRef( 'content', '<h2>Fehler!</h2><p>' . $result . '</p>' );
        parent::display($tpl);
        return;
      }
    }

    // Konfiguration des Menü-Eintrages ermitteln
    $itemId = JRequest::getInt( 'Itemid' );
    $menuParams = null;
    if ($itemId) {
      $menu = JSite::getMenu();
      $menuParams = $menu->getParams( $itemId );
    }
    if (!is_object($menuParams)) {
      $this->assignRef( 'content', '<h2>'.JText::_( 'ERROR' ).'!</h2><p>'.JText::_( 'CANT_LOAD_MENU_SETTINGS' ).'</p>' );
      parent::display($tpl);
      return;
    }
    //echo '<pre>'; print_r( $menuparams ); echo '</pre>';

    // Ausgabe erzeugen
    $view = JRequest::getString( 'view' );
    $baseUrl = 'index.php?option=com_openestate&amp;view='.$view.'&amp;Itemid='.$itemId;
    $hiddenParams = array(
            'option'=>'com_openestate',
            'view'=>$view,
            'Itemid'=>$itemId,);

    $content = OpenEstateWrapper::wrap( 'index', $baseUrl, $menuParams, $hiddenParams );
    $this->assignRef( 'content', $content );
    parent::display( $tpl );
  }
}
?>