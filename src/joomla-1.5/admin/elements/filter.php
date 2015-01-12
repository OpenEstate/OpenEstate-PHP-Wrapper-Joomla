<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2015 OpenEstate.org
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
include_once( JPATH_ROOT . DS . 'components' . DS . 'com_openestate' . DS . 'openestate.wrapper.php' );

class JElementFilter extends JElement {

  /**
   * Element name
   *
   * @access      protected
   * @var         string
   */
  var $_name = 'Filter';

  function fetchElement($name, $value, &$node, $control_name) {

    // load script environment
    if (!defined('IMMOTOOL_BASE_PATH')) {
      $parameters = OpenEstateWrapper::getParameters();
      if ($parameters == null) {
        return '';
      }
      $scriptPath = OpenEstateWrapper::getScriptPath($parameters);
      if (!is_dir($scriptPath)) {
        return JText::_('WRAPPER_ERROR_PATH_INVALID');
      }
      $result = OpenEstateWrapper::initEnvironment($scriptPath);
      if (is_string($result)) {
        return $result;
      }
    }

    // load translations
    $translations = array();
    $jLang = &JFactory::getLanguage();
    $lang = OpenEstateWrapper::loadTranslations($jLang->getTag(), $translations);

    // get current filter values
    $values = OpenEstateWrapper::parseValuesFromTxt($value);
    //echo '<pre>'; print_r( $values ); echo '</pre>';

    // show widgets for any available filter
    $filterIds = array();
    foreach (immotool_functions::list_available_filters() as $key) {
      $filterObj = immotool_functions::get_filter($key);
      if (!is_object($filterObj)) {
        //echo "Can't find filter object $key<hr/>";
        continue;
      }
      $filterValue = (isset($values[$key])) ? $values[$key] : '';
      $filterWidget = $filterObj->getWidget($filterValue, $lang, $translations, $setupIndex);
      if (!is_string($filterWidget) || strlen($filterWidget) == 0) {
        //echo "Can't create widget for filter object $key<hr/>";
        continue;
      }
      $filterWidget = str_replace('<select ', '<select onchange="build_tag();" ', $filterWidget);
      $filterWidget = str_replace('<input ', '<input onchange="build_tag();" ', $filterWidget);
      $output .= '<div style="margin-bottom:4px;">' . $filterWidget . '</div>';
      $filterIds[] = '\'' . $key . '\'';
    }

    $output .= '<textarea id="' . $control_name . '[' . $name . ']"' . '" name="' . $control_name . '[' . $name . ']"' . '" cols="50" rows="5" style="visibility:hidden; position:absolute;">' . $value . '</textarea>';
    $output .= '<script type="text/javascript">
<!--
function build_tag()
{
  //alert( \'build_tag\' );
  var obj = document.getElementById(\'' . $control_name . '[' . $name . ']' . '\');
  if (obj==null) return;

  var obj2 = null;
  var params = \'\';
  var filters = new Array(' . implode(', ', $filterIds) . ');
  for (var i=0; i<filters.length; i++)
  {
    obj2 = document.getElementById(\'filter_\'+filters[i]);
    if (obj2==null) continue;
    val = \'\';
    //alert( filters[i] + \': \' + obj2.checked );
    if (obj2.checked==true || obj2.checked==false)
    {
      if (obj2.checked==true) val = obj2.value;
    }
    else
    {
      val = obj2.value;
    }
    if (val!=\'\')
    {
      if (params.length>0) params += \'|||\';
      params += filters[i] + \'=\' + val;
    }
  }

  obj.innerHTML = params;
}
//build_tag();
-->
</script>';
    return $output;
  }

}
