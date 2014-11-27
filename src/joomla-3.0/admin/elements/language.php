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

class JFormFieldLanguage extends JFormField {

  /**
   * The form field type.
   *
   * @var         string
   * @since       1.6
   */
  public $type = 'Language';

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

    $class = $this->element['class'] ? $this->element['class'] : 'inputbox';
    $output = '<select id="' . $this->id . '" name="' . $this->name . '" class="' . $class . '">';
    foreach (immotool_functions::get_language_codes() as $code) {
      $selected = ($this->value == $code) ? 'selected="selected"' : '';
      $output .= '<option value="' . $code . '" ' . $selected . '>' . immotool_functions::get_language_name($code) . '</option>';
    }
    $output .= '</select>';
    return $output;
  }

}
