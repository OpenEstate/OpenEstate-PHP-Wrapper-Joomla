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

// init
jimport('joomla.form.formfield');
include_once( JPATH_ROOT . '/components/com_openestate/openestate.wrapper.php' );

class JFormFieldOrder extends JFormField {

  /**
   * The form field type.
   *
   * @var         string
   * @since       1.6
   */
  public $type = 'Order';

  protected function getInput() {

    // load script environment
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
    $setupIndex = new immotool_setup_index();
    if (is_callable(array('immotool_functions', 'init_config'))) {
      immotool_functions::init_config($setupIndex, 'load_config_index');
    }

    // load translations
    $translations = array();
    $jLang = &JFactory::getLanguage();
    $lang = OpenEstateWrapper::loadTranslations($jLang->getTag(), $translations);

    // load available orderings
    $sortedOrders = array();
    $availableOrders = array();
    $orderNames = array();

    // get all available order classes
    if (is_callable(array('immotool_functions', 'list_available_orders'))) {
      $orderNames = immotool_functions::list_available_orders();
    }

    // get explicitly enabled order classes
    // this mechanism is a fallback for older versions of the OpenEstate-PHP-Export,
    // that don't support immotool_functions::list_available_orders()
    else if (is_array($setupIndex->OrderOptions)) {
      $orderNames = $setupIndex->OrderOptions;
    }

    foreach ($orderNames as $key) {
      $orderObj = immotool_functions::get_order($key);
      $by = $orderObj->getTitle($translations, $lang);
      $sortedOrders[$key] = $by;
      $availableOrders[$key] = $orderObj;
    }
    asort($sortedOrders);

    // build widget for available orderings
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
