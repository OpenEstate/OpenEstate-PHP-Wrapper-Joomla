<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: openestate.wrapper.php 1708 2012-08-15 15:00:41Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2012, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Definition der zu verwendenden Parameter.
 */

if (!defined('IMMOTOOL_PARAM_LANG'))
  define('IMMOTOOL_PARAM_LANG', 'wrapped_lang');
if (!defined('IMMOTOOL_PARAM_FAV'))
  define('IMMOTOOL_PARAM_FAV', 'wrapped_fav');
if (!defined('IMMOTOOL_PARAM_INDEX_PAGE'))
  define('IMMOTOOL_PARAM_INDEX_PAGE', 'wrapped_page');
if (!defined('IMMOTOOL_PARAM_INDEX_RESET'))
  define('IMMOTOOL_PARAM_INDEX_RESET', 'wrapped_reset');
if (!defined('IMMOTOOL_PARAM_INDEX_ORDER'))
  define('IMMOTOOL_PARAM_INDEX_ORDER', 'wrapped_order');
if (!defined('IMMOTOOL_PARAM_INDEX_FILTER'))
  define('IMMOTOOL_PARAM_INDEX_FILTER', 'wrapped_filter');
if (!defined('IMMOTOOL_PARAM_INDEX_FILTER_CLEAR'))
  define('IMMOTOOL_PARAM_INDEX_FILTER_CLEAR', 'wrapped_clearFilters');
if (!defined('IMMOTOOL_PARAM_INDEX_VIEW'))
  define('IMMOTOOL_PARAM_INDEX_VIEW', 'wrapped_view');
if (!defined('IMMOTOOL_PARAM_INDEX_MODE'))
  define('IMMOTOOL_PARAM_INDEX_MODE', 'wrapped_mode');
if (!defined('IMMOTOOL_PARAM_EXPOSE_ID'))
  define('IMMOTOOL_PARAM_EXPOSE_ID', 'wrapped_id');
if (!defined('IMMOTOOL_PARAM_EXPOSE_VIEW'))
  define('IMMOTOOL_PARAM_EXPOSE_VIEW', 'wrapped_view');
if (!defined('IMMOTOOL_PARAM_EXPOSE_IMG'))
  define('IMMOTOOL_PARAM_EXPOSE_IMG', 'wrapped_img');
if (!defined('IMMOTOOL_PARAM_EXPOSE_CONTACT'))
  define('IMMOTOOL_PARAM_EXPOSE_CONTACT', 'wrapped_contact');
if (!defined('IMMOTOOL_PARAM_EXPOSE_CAPTCHA'))
  define('IMMOTOOL_PARAM_EXPOSE_CAPTCHA', 'wrapped_captchacode');
if (!defined('OPENESTATE_WRAPPER'))
  define('OPENESTATE_WRAPPER', '1');

class OpenEstateWrapper {

  function getParameters() {
    jimport( 'joomla.html.parameter' );

    // Joomla 1.5: Eintrag in der Komponenten-Tabelle
    //$table = JTable::getInstance('component');
    //if( !$table->loadByOption('com_openestate') ) {
    //  JError::raiseWarning(500, 'Not a valid component');
    //  return null;
    //}

    // Joomla 1.6: Eintrag in der Komponenten-Tabelle
		$table = &JTable::getInstance('extension');
    //if (!$table->load(array('name'=>'openestate'))) {
		if (!$table->load(array('name'=>'com_openestate'))) {
      JError::raiseWarning(500, 'Not a valid component');
      return false;
	  }

    return new JParameter( $table->params,
            JPATH_ADMINISTRATOR.DS.'components'.DS.'com_openestate'.DS.'config.wrapper.xml');
  }

  function getScriptPath( &$params ) {
    return $params->get( 'script_path' );
  }

  function getScriptUrl( &$params ) {
    return $params->get( 'script_url' );
  }

  function initEnvironment( $scriptPath, $doInclude=true ) {
    if (defined('IMMOTOOL_BASE_PATH')) return false;
    $environmentFiles = array( 'config.php', 'include/functions.php', 'data/language.php' );
    define('IMMOTOOL_BASE_PATH', $scriptPath);
    foreach ($environmentFiles as $file) {
      if (!is_file(IMMOTOOL_BASE_PATH.$file))
        return 'File \''.$file.'\' was not found in export directory!';
    }
    if ($doInclude===true) {
      define('IN_WEBSITE', 1);
      foreach ($environmentFiles as $file) {
        //echo IMMOTOOL_BASE_PATH . $file . '<hr/>';
        include(IMMOTOOL_BASE_PATH.$file);
      }
      if (!defined('IMMOTOOL_SCRIPT_VERSION'))
        return 'Can\'t load version of the PHP export!';
    }
    return true;
  }

  function loadTranslations( $preferredLang, &$translations ) {
    $setupIndex = new immotool_setup_index();
    if (!is_string($preferredLang)) {
      $preferredLang = $setupIndex->DefaultLanguage;
    }
    else if (strpos($preferredLang, '-')!==false) {
      $l = explode('-', $preferredLang);
      $preferredLang = $l[0];
    }
    $lang = immotool_functions::init_language( strtolower($preferredLang), $setupIndex->DefaultLanguage, $translations );
    if (!is_array($translations)) return null;
    return $lang;
  }

  function parseValuesFromTxt( &$txt ) {
    $lines = array();

    // in older versions, values are splitted by \n
    if (strpos(trim($txt), "\n")!==false)
      $lines = explode( "\n", $txt );

    // in current version, values are written into one line, splitted by |||
    else
      $lines = explode( "|||", $txt );

    $values = array();
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line=='') continue;
      $pos = strpos($line,'=');
      if ($pos===false) continue;
      $key = substr( $line, 0, $pos );
      $value = substr( $line, $pos+1 );
      $values[$key] = $value;
    }
    return $values;
  }

  function wrap( $defaultView, $scriptName, &$params, &$hiddenParams ) {
    // Script ermitteln
    $wrap = (isset($_REQUEST['wrap']) && is_string($_REQUEST['wrap']))?
            $_REQUEST['wrap']: $defaultView;
    if ($wrap=='expose') {
      $wrap = 'expose';
      $script = 'expose.php';
      //return '<pre>' . print_r($_REQUEST, true) . '</pre>';

      // Standard-Konfigurationswerte beim ersten Aufruf setzen
      if (!isset($_REQUEST[ 'wrap' ])) {
        if ($params->get( 'lang', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_LANG ] = $params->get( 'lang' );
        if ($params->get( 'id', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_EXPOSE_ID ] = $params->get( 'id' );
        if ($params->get( 'view', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_EXPOSE_VIEW ] = $params->get( 'view' );
      }
    }
    else {
      $wrap = 'index';
      $script = 'index.php';
      //return '<pre>' . print_r($_REQUEST, true) . '</pre>';

      // Standard-Konfigurationswerte beim ersten Aufruf setzen
      if (!isset($_REQUEST[ 'wrap' ])) {
        $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER_CLEAR ] = '1';
        if ($params->get( 'lang', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_LANG ] = $params->get( 'lang' );
        if ($params->get( 'view', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_INDEX_VIEW ] = $params->get( 'view' );
        if ($params->get( 'mode', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_INDEX_MODE ] = $params->get( 'mode' );
        if ($params->get( 'order', null )!=null)
          $_REQUEST[ IMMOTOOL_PARAM_INDEX_ORDER ] = $params->get( 'order' );
      }

      // Zurücksetzen der gewählten Filter
      if (isset($_REQUEST[IMMOTOOL_PARAM_INDEX_RESET])) {
        unset($_REQUEST[IMMOTOOL_PARAM_INDEX_RESET]);
        $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ] = array();
        $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER_CLEAR ] = '1';
      }

      // vorgegebene Filter-Kriterien mit der Anfrage zusammenführen
      if (!isset($_REQUEST[ 'wrap' ]) || isset($_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ])) {
        $filters = OpenEstateWrapper::parseValuesFromTxt( $params->get( 'filter' ) );
        if (is_array($filters)) {
          foreach ($filters as $filter=>$value) {
            if (!is_array($_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ])) {
              $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ] = array();
            }
            if (!isset($_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ][$filter])) {
              $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ][$filter] = $value;
            }
          }
        }
      }
    }

    // Script ausführen
    //echo 'wrap: ' . IMMOTOOL_BASE_PATH . $script;
    ob_start();
    include( IMMOTOOL_BASE_PATH . $script );
    $page = ob_get_contents();
    //ob_clean();
    ob_end_clean();

    // Stylesheets
    $setup = new immotool_setup();
    if (is_callable(array('immotool_myconfig', 'load_config_default'))) immotool_myconfig::load_config_default( $setup );
    $stylesheets = array( IMMOTOOL_BASE_URL . 'style.php' );
    if (is_string($setup->AdditionalStylesheet) && strlen($setup->AdditionalStylesheet)>0)
      $stylesheets[] = $setup->AdditionalStylesheet;

    // Nachträgliche Bearbeitung am Dokument
    $document = &JFactory::getDocument();
    $lang = (isset($_REQUEST[ IMMOTOOL_PARAM_LANG ]))? $_REQUEST[ IMMOTOOL_PARAM_LANG ]: $params->get( 'lang' );
    if (is_string($lang)) {
      $document->setLanguage( $lang );
      $document->setMetaData( 'language', $lang );
    }
    foreach ($stylesheets as $stylesheet) {
      $document->addStyleSheet( $stylesheet );
    }

    // Nachträgliche Bearbeitung am Dokument, Exposéansicht
    if ($wrap=='expose') {
      $exposeId = (isset($_REQUEST[ IMMOTOOL_PARAM_EXPOSE_ID ]))? $_REQUEST[ IMMOTOOL_PARAM_EXPOSE_ID ]: null;
      $exposeObj = (is_string($exposeId))? immotool_functions::get_object( $exposeId ): null;
      $exposeTxt = (is_string($exposeId))? immotool_functions::get_text( $exposeId ): null;
      if (is_array($exposeObj)) {

        // Titel aus Immobilie übernehmen
        $title = (is_string($lang) && isset($exposeObj['title'][$lang]))? $exposeObj['title'][$lang]: null;
        if (is_string($title)) {
          $title = trim( strip_tags( html_entity_decode( $title, ENT_NOQUOTES, $setup->Charset ) ) );
          $document->setTitle( $title . ' | ' . $document->getTitle() );
        }
      }
      if (is_array($exposeTxt)) {

        // Keywords aus Immobilie übernehmen
        $keywords = (is_string($lang) && isset($exposeTxt['keywords'][$lang]))? $exposeTxt['keywords'][$lang]: null;
        if (is_string($keywords)) {
          $keywords = trim( strip_tags( html_entity_decode( $keywords, ENT_NOQUOTES, $setup->Charset ) ) );
          $document->setMetaData( 'keywords', $keywords );
        }

        // Description aus Immobilie übernehmen
        $description = (is_string($lang) && isset($exposeTxt['short_description'][$lang]))? $exposeTxt['short_description'][$lang]: null;
        if (is_null($description)) {
          $description = (is_string($lang) && isset($exposeTxt['kurz_beschr'][$lang]))? $exposeTxt['kurz_beschr'][$lang]: null;
        }
        if (is_string($description)) {
          $description = trim( strip_tags( html_entity_decode( $description, ENT_NOQUOTES, $setup->Charset ) ) );
          $document->setMetaData( 'description', $description );
        }
      }
    }

    // Ausgabe erzeugen
    return immotool_functions::wrap_page( $page, $wrap, $scriptName, IMMOTOOL_BASE_URL, array(), $hiddenParams );
  }
}
?>