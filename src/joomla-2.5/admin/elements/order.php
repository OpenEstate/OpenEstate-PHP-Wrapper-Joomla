<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: order.php 1618 2012-07-03 08:12:06Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2012, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.form.formfield');
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class JFormFieldOrder extends JFormField {

  /**
   * The form field type.
   *
   * @var         string
   * @since       1.6
   */
  public $type = 'Order';

  protected function getInput() {

    // Skript-Umgebung ggf. einbinden
    if (!defined('IMMOTOOL_BASE_PATH')) {
      $parameters = OpenEstateWrapper::getParameters();
      if ($parameters==null) return '';
      $scriptPath = OpenEstateWrapper::getScriptPath( $parameters );
      if (!is_dir($scriptPath)) return JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID' );
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
    $orderNames = array();
    if (!is_callable(array('immotool_functions', 'list_available_orders'))) {
      // Mechanismus für ältere PHP-Exporte, um die registrierten Sortierungen zu verwenden
      $setupIndex = new immotool_setup_index();
      if (is_callable(array('immotool_functions', 'init_config'))) {
        immotool_functions::init_config($setupIndex, 'load_config_index');
      }
      if (is_array($setupIndex->OrderOptions)) {
        $orderNames = $setupIndex->OrderOptions;
      }
    }
    else {
      // alle verfügbaren Sortierungen verwenden
      $orderNames = immotool_functions::list_available_orders();
    }
    foreach ($orderNames as $key) {
      $orderObj = immotool_functions::get_order($key);
      //$by = $orderObj->getName();
      $by = $orderObj->getTitle( $translations, $lang );
      $sortedOrders[$key] = $by;
      $availableOrders[$key] = $orderObj;
    }
    asort($sortedOrders);

    // Auswahl der Sortierkriterien erzeugen
    $class = $this->element['class'] ? $this->element['class'] : 'inputbox';
    $output = '<select id="'.$this->id . '"'.'" name="'.$this->name.'" class="'.$class.'">';
    $output .= '<optgroup label="aufsteigend">';
    foreach ($sortedOrders as $key=>$by) {
      $orderObj = $availableOrders[$key];
      $o = $key . '-asc';
      $selected = ($this->value==$o)? 'selected="selected"': '';
      $output .= '<option value="' . $o . '" ' . $selected . '>&uarr; ' . $by . ' &uarr;</option>';
    }
    $output .= '</optgroup>';
    $output .= '<optgroup label="absteigend">';
    foreach ($sortedOrders as $key=>$by) {
      $orderObj = $availableOrders[$key];
      $o = $key . '-desc';
      $selected = ($this->value==$o)? 'selected="selected"': '';
      $output .= '<option value="' . $o . '" ' . $selected . '>&darr; ' . $by . ' &darr;</option>';
    }
    $output .= '</optgroup>';
    $output .= '</select>';
    return $output;
  }
}
?>