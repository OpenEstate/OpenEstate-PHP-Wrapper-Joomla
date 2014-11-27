<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: openestate.wrapper.php 1873 2012-10-24 20:29:04Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2012, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

//error_reporting( E_ALL );
//ini_set('display_errors','1');

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
    $table = JTable::getInstance('component');
    if( !$table->loadByOption('com_openestate') ) {
      JError::raiseWarning(500, 'Not a valid component');
      return null;
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
    $document =& JFactory::getDocument();

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
        foreach ($filters as $filter=>$value) {
          if (!isset($_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ]) || !is_array($_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ])) {
            $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ] = array();
          }
          if (!isset($_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ][$filter])) {
            $_REQUEST[ IMMOTOOL_PARAM_INDEX_FILTER ][$filter] = $value;
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

    // Konfiguration ermitteln
    $setup = null;
    if ($wrap=='expose') {
      $setup = new immotool_setup_expose();
      if (is_callable(array('immotool_myconfig', 'load_config_expose'))) immotool_myconfig::load_config_expose( $setup );
    }
    else {
      $setup = new immotool_setup_index();
      if (is_callable(array('immotool_myconfig', 'load_config_index'))) immotool_myconfig::load_config_index( $setup );
    }

    // Nachträgliche Bearbeitung am Dokument
    $lang = (isset($_REQUEST[ IMMOTOOL_PARAM_LANG ]))? $_REQUEST[ IMMOTOOL_PARAM_LANG ]: $params->get( 'lang' );
    if (is_string($lang)) {
      $document->setLanguage( $lang );
      $document->setMetaData( 'language', $lang );
    }

    // Stylesheets registrieren
    $stylesheets = array( IMMOTOOL_BASE_URL . 'style.php?wrapped=1' );
    if (is_string($setup->AdditionalStylesheet) && strlen($setup->AdditionalStylesheet)>0) {
      $stylesheets[] = $setup->AdditionalStylesheet;
    }
    foreach ($stylesheets as $stylesheet) {
      $document->addStyleSheet( $stylesheet );
    }

    // Nachträgliche Bearbeitung am Dokument, Exposéansicht
    $metaDescription = null;
    $metaKeywords = null;
    if ($wrap=='expose') {
      $exposeId = (isset($_REQUEST[ IMMOTOOL_PARAM_EXPOSE_ID ]))? $_REQUEST[ IMMOTOOL_PARAM_EXPOSE_ID ]: null;
      $exposeObj = (is_string($exposeId))? immotool_functions::get_object( $exposeId ): null;
      $exposeTxt = (is_string($exposeId))? immotool_functions::get_text( $exposeId ): null;
      if (is_array($exposeObj)) {

        // Titel der Immobilie ins Dokument übernehmen
        $title = (is_string($lang) && isset($exposeObj['title'][$lang]))? $exposeObj['title'][$lang]: null;
        if (is_string($title)) {
          $title = trim( strip_tags( html_entity_decode( $title, ENT_NOQUOTES, $setup->Charset ) ) );
          $document->setTitle( $title . ' | ' . $document->getTitle() );
        }
      }
      if (is_array($exposeTxt)) {

        // Keywords aus Immobilie übernehmen
        $txt = (is_string($lang) && isset($exposeTxt['keywords'][$lang]))? $exposeTxt['keywords'][$lang]: null;
        if (is_string($txt)) {
          $metaKeywords = trim( strip_tags( html_entity_decode( $txt, ENT_NOQUOTES, $setup->Charset ) ) );
        }

        // Description aus Immobilie übernehmen
        if (is_array($setup->MetaDescriptionTexts)) {
          foreach ($setup->MetaDescriptionTexts as $attrib) {
            $txt = (isset($objectTexts[$attrib][$lang])) ? $objectTexts[$attrib][$lang] : null;
            if (is_string($txt) && strlen(trim($txt))>0) {
              $metaDescription = trim( strip_tags( html_entity_decode( $txt, ENT_NOQUOTES, $setup->Charset ) ) );
              break;
            }
            else {
              $txt = null;
            }
          }
        }
      }
    }

    // Meta-Description ggf. ins Dokument übernehmen
    if (is_string($metaDescription) && strlen(trim($metaDescription))>0) {
      $document->setMetaData( 'description', trim($metaDescription) );
    }

    // Meta-Keywords ggf. ins Dokument übernehmen
    if (is_string($metaKeywords) && strlen(trim($metaKeywords))>0) {
      $document->setMetaData( 'keywords', trim($metaKeywords) );
    }

    // Ausgabe erzeugen
    return immotool_functions::wrap_page( $page, $wrap, $scriptName, IMMOTOOL_BASE_URL, array(), $hiddenParams );
  }
}
?>