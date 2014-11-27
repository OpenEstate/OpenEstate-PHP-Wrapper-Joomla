<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: order.php 586 2010-12-10 07:42:45Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class JElementOrder extends JElement {
  /**
   * Element name
   *
   * @access      protected
   * @var         string
   */
  var $_name = 'Order';

  function fetchElement($name, $value, &$node, $control_name) {

    // Skript-Umgebung ggf. einbinden
    if (!defined('IMMOTOOL_BASE_PATH')) {
      $parameters = OpenEstateWrapper::getParameters();
      if ($parameters==null) return '';
      $scriptPath = OpenEstateWrapper::getScriptPath( $parameters );
      if (!is_dir($scriptPath)) return JText::_( 'WRAPPER_ERROR_PATH_INVALID' );
      $result = OpenEstateWrapper::initEnvironment( $scriptPath );
      if (is_string($result)) return $result;
    }

    // Übersetzungen ermitteln
    $translations = array();
    $jLang = &JFactory::getLanguage();
    $lang = OpenEstateWrapper::loadTranslations( $jLang->getTag(), $translations );

    // Sortierkriterien ermitteln
    $sortedOrders = array();
    $availableOrders = array();
    $setupIndex = new immotool_setup_index();
    foreach ($setupIndex->OrderOptions as $key) {
      $orderObj = immotool_functions::get_order($key);
      //$by = $orderObj->getName();
      $by = $orderObj->getTitle( $translations, $lang );
      $sortedOrders[$key] = $by;
      $availableOrders[$key] = $orderObj;
    }
    asort($sortedOrders);

    // Auswahl der Sortierkriterien erzeugen
    $class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'inputbox';
    $output = '<select id="'.$control_name . '[' . $name . ']"'.'" name="'.$control_name . '[' . $name . ']"'.'" class="'.$class.'">';
    $output .= '<optgroup label="aufsteigend">';
    foreach ($sortedOrders as $key=>$by) {
      $orderObj = $availableOrders[$key];
      $o = $key . '-asc';
      $selected = ($value==$o)? 'selected="selected"': '';
      $output .= '<option value="' . $o . '" ' . $selected . '>&uarr; ' . $by . ' &uarr;</option>';
    }
    $output .= '</optgroup>';
    $output .= '<optgroup label="absteigend">';
    foreach ($sortedOrders as $key=>$by) {
      $orderObj = $availableOrders[$key];
      $o = $key . '-desc';
      $selected = ($value==$o)? 'selected="selected"': '';
      $output .= '<option value="' . $o . '" ' . $selected . '>&darr; ' . $by . ' &darr;</option>';
    }
    $output .= '</optgroup>';
    $output .= '</select>';
    return $output;
  }
}
?>