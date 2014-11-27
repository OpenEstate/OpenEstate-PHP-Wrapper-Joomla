<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2014 OpenEstate.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
include_once( JPATH_ROOT . DS . 'components' . DS . 'com_openestate' . DS . 'openestate.wrapper.php' );

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
      if ($parameters == null) {
        return '';
      }
      $scriptPath = OpenEstateWrapper::getScriptPath($parameters);
      if (!is_dir($scriptPath)) {
        return JText::_('COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID');
      }
      $result = OpenEstateWrapper::initEnvironment($scriptPath);
      if (is_string($result)) {
        return $result;
      }
    }

    // Übersetzungen ermitteln
    $translations = array();
    $jLang = &JFactory::getLanguage();
    $lang = OpenEstateWrapper::loadTranslations($jLang->getTag(), $translations);

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
      $by = $orderObj->getTitle($translations, $lang);
      $sortedOrders[$key] = $by;
      $availableOrders[$key] = $orderObj;
    }
    asort($sortedOrders);

    // Auswahl der Sortierkriterien erzeugen
    $class = $this->element['class'] ? $this->element['class'] : 'inputbox';
    $output = '<select id="' . $this->id . '"' . '" name="' . $this->name . '" class="' . $class . '">';
    $output .= '<optgroup label="aufsteigend">';
    foreach ($sortedOrders as $key => $by) {
      $orderObj = $availableOrders[$key];
      $o = $key . '-asc';
      $selected = ($this->value == $o) ? 'selected="selected"' : '';
      $output .= '<option value="' . $o . '" ' . $selected . '>&uarr; ' . $by . ' &uarr;</option>';
    }
    $output .= '</optgroup>';
    $output .= '<optgroup label="absteigend">';
    foreach ($sortedOrders as $key => $by) {
      $orderObj = $availableOrders[$key];
      $o = $key . '-desc';
      $selected = ($this->value == $o) ? 'selected="selected"' : '';
      $output .= '<option value="' . $o . '" ' . $selected . '>&darr; ' . $by . ' &darr;</option>';
    }
    $output .= '</optgroup>';
    $output .= '</select>';
    return $output;
  }

}
