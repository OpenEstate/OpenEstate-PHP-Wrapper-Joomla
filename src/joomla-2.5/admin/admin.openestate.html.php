<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: admin.openestate.html.php 2054 2013-02-12 07:55:38Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2013, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class OpenEstateViews {

  function bar() {
    $jLang = &JFactory::getLanguage();
    $lang = explode('-', $jLang->getTag());
    echo '<div style="float:right;">';
    echo '<a href="http://openestate.org" target="_blank"><img src="./components/com_openestate/images/openestate_logo.png" alt="OpenEstate.org" title="OpenEstate.org" border="0" /></a>';

    echo '<div style="margin-top:2em; text-align:right;">';
    // PayPal (german)
    if (strtolower($lang[0])=='de') {
      echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
      echo '<input type="hidden" name="cmd" value="_s-xclick">';
      echo '<input type="hidden" name="hosted_button_id" value="11005790">';
      echo '<input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal." style="border:none;">';
      echo '<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">';
      echo '</form>';
    }
    // PayPal (english)
    else {
      echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
      echo '<input type="hidden" name="cmd" value="_s-xclick" />';
      echo '<input type="hidden" name="hosted_button_id" value="7B3J85G4NKY3E" />';
      echo '<input type="image" src="https://www.paypal.com/en_US/DE/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" style="border:none;"/>';
      echo '<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1" />';
      echo '</form>';
    }
    echo '</div>';
    echo '</div>';
  }

  function submenu($task=null) {
    JSubMenuHelper::addEntry(JText::_('COM_OPENESTATE_WRAPPER'), 'index.php?option=com_openestate&task=wrapper', $task=='wrapper');
    JSubMenuHelper::addEntry(JText::_('COM_OPENESTATE_ABOUT'), 'index.php?option=com_openestate&task=about', $task=='about');
  }

  function home() {
    JToolBarHelper::title( 'OpenEstate.org', 'openestate' );
    OpenEstateViews::submenu();
    OpenEstateViews::bar();
    echo '<h2>'.JText::_( 'COM_OPENESTATE_HOME_TITLE' ).'</h2>';
    echo '<ul>';
    echo '<li><a href="?option=com_openestate&task=wrapper" style="font-size:1.2em;">'.JText::_( 'COM_OPENESTATE_HOME_GOTO_WRAPPER' ).'</a></li>';
    echo '<li><a href="?option=com_openestate&task=about" style="font-size:1.2em;">'.JText::_( 'COM_OPENESTATE_HOME_GOTO_ABOUT' ).'</a></li>';
    echo '</ul>';
  }

  function about() {
    JToolBarHelper::title( 'OpenEstate.org', 'openestate' );
    OpenEstateViews::submenu( 'about' );
    OpenEstateViews::bar();
    echo '<h2>'.JText::_( 'COM_OPENESTATE_ABOUT_TITLE' ).'</h2>';
    echo '<p>'.JText::sprintf( 'COM_OPENESTATE_ABOUT_INFO', 'http://openestate.org' ).'</p>';
    echo '<ul>';
    echo '<li>'.JText::_( 'COM_OPENESTATE_ABOUT_LICENSE' ).': <a href="./components/com_openestate/gpl-3.0-standalone.html" target="_blank">GPL3</a></li>';
    echo '<li>'.JText::_( 'COM_OPENESTATE_ABOUT_AUTHORS' ).': Andreas Rudolph &amp; Walter Wagner</li>';
    echo '</ul>';
    echo '<h3>'.JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS' ).'</h3>';
    echo '<p>'.JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_TEXT' ).'</p>';
    echo '<ul>';
    echo '<li>'.JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_BY_RECOMMENDATION' ).'</li>';
    echo '<li>'.JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_BY_TRANSLATION' ).'</li>';
    echo '<li>'.JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_BY_SPONSORING' ).'</li>';
    echo '</ul>';
  }

  function wrapper() {
    JToolBarHelper::title( 'OpenEstate.org', 'openestate' );
    OpenEstateViews::submenu( 'wrapper' );
    OpenEstateViews::bar();
    echo '<h2>'.JText::_( 'COM_OPENESTATE_WRAPPER_TITLE' ).'</h2>';
    echo '<p>'.JText::sprintf( 'COM_OPENESTATE_WRAPPER_INFO', 'http://wiki.openestate.org/Kategorie:ImmoTool_PHP-Export' ).'</p>';
    echo '<h3>'.JText::_( 'COM_OPENESTATE_WRAPPER_SETUP' ).'</h3>';

    // Eintrag in der Komponenten-Tabelle
		$table = &JTable::getInstance('extension');
    //if (!$table->load(array('name'=>'openestate'))) {
		if (!$table->load(array('name'=>'com_openestate'))) {
      JError::raiseWarning(500, 'Not a valid component');
      return false;
	  }
    //echo '<pre>'; print_r( $table ); echo '</pre>';

    // Formular zur Einbindung der Skript-Umgebung verarbeiten
    if (strtoupper($_SERVER['REQUEST_METHOD'])=='POST') {

      $post = JRequest::get('post');
      //echo '<pre>'; print_r( $post ); echo '</pre>';

      // Pfad muss ein '/' am Ende haben
      $value = $post['params']['script_path'];
      $len = strlen($value);
      if ($len>0 && $value[$len-1]!='/') $post['params']['script_path'] .= '/';

      // URL muss ein '/' am Ende haben
      $value = $post['params']['script_url'];
      $len = strlen($value);
      if ($len>0 && $value[$len-1]!='/') $post['params']['script_url'] .= '/';

      $table->bind($post);
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

    // Formular zur Einbindung der Skript-Umgebung erzeugen
    echo '<form action="index.php" method="post">';
    $parameters = new JParameter($table->params, JPATH_COMPONENT_ADMINISTRATOR.DS.'config.wrapper.xml');
    $groups = $parameters->getGroups();
    echo '<table>';
    foreach (array_keys($groups) as $group) {
      //echo '<p>'.$group.'</p>';
      //echo $parameters->render('params', $group);

      $fields = $parameters->renderToArray('params', $group);
      //echo '<pre>'; print_r( $fields ); echo '</pre>';
      foreach ($fields as $field) {
        echo '<tr>';
        echo '<td>' . $field[0] . '</td>';
        echo '<td>' . $field[1] . '</td>';
        echo '</tr>';
      }
    }
    echo '</table>';
    echo '<input type="hidden" name="option" value="com_openestate" />';
    echo '<input type="hidden" name="task" value="wrapper" />';
    echo '<input type="submit" value="'.JText::_( 'COM_OPENESTATE_WRAPPER_SUBMIT' ).'" style="margin-left:15em; margin-top:1em;" />';
    echo '</form>';

    // Prüfung der Eingaben
    $errors = array();

    // Prüfung der Eingaben, Pfad
    $translations = null;
    $scriptPath = OpenEstateWrapper::getScriptPath( $parameters );
    if (!is_string($scriptPath) || strlen(trim($scriptPath))==0) {
      $errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_PATH_EMPTY' );
    }
    else if (!is_dir($scriptPath)) {
      $errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID' );
    }
    else {
      // ImmoTool-Umgebung einbinden
      $environmentFiles = array( 'config.php', 'include/functions.php', 'data/language.php' );
      define('IMMOTOOL_BASE_PATH', $scriptPath);
      foreach ($environmentFiles as $file) {
        if (!is_file(IMMOTOOL_BASE_PATH.$file))
          $errors[] = JText::sprintf( 'COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_FILE', $file );
      }
      if (count($errors)==0) {
        define('IN_WEBSITE', 1);
        foreach ($environmentFiles as $file) {
          //echo IMMOTOOL_BASE_PATH . $file . '<hr/>';
          include(IMMOTOOL_BASE_PATH.$file);
        }
        if (!defined('IMMOTOOL_SCRIPT_VERSION')) {
          $errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_VERSION' );
        }

        // Übersetzungen ermitteln
        $translations = array();
        $jLang = &JFactory::getLanguage();
        $lang = OpenEstateWrapper::loadTranslations( $jLang->getTag(), $translations );
        if ($translations==null || count($translations)==0) {
          $errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_TRANSLATION' );
        }
      }
    }

    // Prüfung der Eingaben, URL
    $scriptUrl = OpenEstateWrapper::getScriptUrl( $parameters );
    if (!is_string($scriptUrl) || strlen(trim($scriptUrl))==0)
      $errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_URL_EMPTY' );
    else if (strpos(strtolower($scriptUrl),'http://')!==0 && strpos(strtolower($scriptUrl),'https://')!==0)
      $errors[] = JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_URL_INVALID' );

    if (count($errors)>0) {
      echo '<h3 style="color:red;">'.JText::_( 'COM_OPENESTATE_WRAPPER_ERROR' ).'</h3>';
      echo '<ul>';
      foreach ($errors as $error) echo '<li>'.$error.'</li>';
      echo '<li>'.JText::_( 'COM_OPENESTATE_WRAPPER_INFO_JOOMLA' ).'<br/><b style="font-size:1.2em; font-family:monospace;">'.JPATH_ROOT.'</b></li>';
      echo '</ul>';
      return true;
    }
    echo '<h3 style="color:green;">'.JText::_( 'COM_OPENESTATE_WRAPPER_SUCCESS' ).'</h3>';
    echo '<ul>';
    echo '<li>'.JText::_( 'COM_OPENESTATE_WRAPPER_INFO_VERSION' ).' <b>'.IMMOTOOL_SCRIPT_VERSION.'</b></li>';
    echo '<li>'.JText::sprintf( 'COM_OPENESTATE_WRAPPER_SUCCESS_MESSAGE', 'index.php?option=com_menus' ).'</li>';
    echo '</ul>';
    return true;
  }
}
?>