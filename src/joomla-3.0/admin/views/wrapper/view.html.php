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
jimport( 'joomla.html.parameter' );

class OpenestateViewWrapper extends JViewLegacy {
  function display($tpl = null) {
    require_once( JPATH_COMPONENT.'/helpers/openestate.php' );
    require_once( JPATH_ROOT.'/components/com_openestate/openestate.wrapper.php' );

    // Parameter der Komponente ermitteln
    $params = OpenEstateWrapper::getParameters();
    //$params = JComponentHelper::getParams('com_openestate')->toArray();

    // allgemeine Komponenten
    OpenestateHelper::addTitle( 'wrapper' );
    $this->sidebar = OpenestateHelper::buildSidebar( 'wrapper' );
    $this->infobar = OpenestateHelper::buildInfobar( 'wrapper' );

    // Formular zur Einbindung der Skript-Umgebung verarbeiten
    if (strtoupper($_SERVER['REQUEST_METHOD'])=='POST') {

      // Eintrag in der Komponenten-Tabelle
      $table = &JTable::getInstance('extension');
      if (!$table->load(array('name'=>'com_openestate'))) {
        JError::raiseWarning(500, 'Not a valid component');
        return false;
      }

      $post = JRequest::get('post');
      if (!is_array($post)) $post = array();
      //echo 'POST:<pre>' . print_r( $params, true ) . '</pre>';

      // Pfad muss ein '/' am Ende haben
      $scriptPath = (isset($post['main']['script_path']))? $post['main']['script_path']: '';
      $len = strlen($scriptPath);
      if ($len>0 && $scriptPath[$len-1]!='/') $scriptPath .= '/';
      $params['script_path'] = $scriptPath;

      // URL muss ein '/' am Ende haben
      $scriptUrl = (isset($post['main']['script_url']))? $post['main']['script_url']: '';
      $len = strlen($scriptUrl);
      if ($len>0 && $scriptUrl[$len-1]!='/') $scriptUrl .= '/';
      $params['script_url'] = $scriptUrl;

      if (!isset($table->params) || !is_array($table->params)) $table->params = array();
      $table->bind( array( 'params' => $params ) );

      //-- Pre-save checks
      if ( ! $table->check()) {
        die( 'CHECK FAILED!!!' );
        JError::raiseWarning(500, $table->getError());
        return false;
      }
      //-- Save the changes
      if ( ! $table->store()) {
        die( 'STORE FAILED!!!' );
        JError::raiseWarning(500, $table->getError());
        return false;
      }
    }

    //echo 'PARAMS:<pre>' . print_r( $params, true ) . '</pre>';

    // Formular mit aktueller Konfiguration erzeugen
    $this->form = &JForm::getInstance( 'wrapper', JPATH_COMPONENT_ADMINISTRATOR.'/form.wrapper.xml' );
    foreach ($params as $key=>$value) {
      $this->form->setValue( $key, 'main', $value );
    }

    // Prüfung der Eingaben
    $this->errors = array();

    // Prüfung der Eingaben, Pfad
    $translations = null;
    $scriptPath = OpenEstateWrapper::getScriptPath( $params );
    if (!is_string($scriptPath) || strlen(trim($scriptPath))==0) {
      $this->errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_PATH_EMPTY' );
    }
    else if (!is_dir($scriptPath)) {
      $this->errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID' );
    }
    else {
      // ImmoTool-Umgebung einbinden
      $environmentFiles = array( 'config.php', 'include/functions.php', 'data/language.php' );
      define('IMMOTOOL_BASE_PATH', $scriptPath);
      foreach ($environmentFiles as $file) {
        if (!is_file(IMMOTOOL_BASE_PATH.$file))
          $this->errors[] = JText::sprintf( 'COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_FILE', $file );
      }
      if (count($this->errors)==0) {
        define('IN_WEBSITE', 1);
        foreach ($environmentFiles as $file) {
          //echo IMMOTOOL_BASE_PATH . $file . '<hr/>';
          include(IMMOTOOL_BASE_PATH.$file);
        }
        if (!defined('IMMOTOOL_SCRIPT_VERSION')) {
          $this->errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_VERSION' );
        }

        // Übersetzungen ermitteln
        $translations = array();
        $jLang = &JFactory::getLanguage();
        $lang = OpenEstateWrapper::loadTranslations( $jLang->getTag(), $translations );
        if ($translations==null || count($translations)==0) {
          $this->errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_TRANSLATION' );
        }
      }
    }

    // Prüfung der Eingaben, URL
    $scriptUrl = OpenEstateWrapper::getScriptUrl( $params );
    if (!is_string($scriptUrl) || strlen(trim($scriptUrl))==0)
      $this->errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_URL_EMPTY' );
    else if (strpos(strtolower($scriptUrl),'http://')!==0 && strpos(strtolower($scriptUrl),'https://')!==0)
      $this->errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_URL_INVALID' );

    // Template rendern
    parent::display( $tpl );
  }
}
?>