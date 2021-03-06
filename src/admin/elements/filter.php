<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2018 OpenEstate.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// init
jimport('joomla.form.formfield');

/** @noinspection PhpIncludeInspection */
include_once(JPATH_ROOT . '/components/com_openestate/openestate.wrapper.php');

class JFormFieldFilter extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     * @since 1.6
     */
    public $type = 'Filter';

    /**
     * Method to get the field input markup.
     *
     * @return string
     * The field input markup.
     *
     * @since 11.1
     */
    protected function getInput()
    {
        // load script environment
        if (!defined('IMMOTOOL_BASE_PATH')) {
            $parameters = OpenEstateWrapper::getParameters();
            if ($parameters == null) {
                return '';
            }
            $scriptPath = OpenEstateWrapper::getScriptPath($parameters);
            if (!is_dir($scriptPath)) {
                /** @noinspection PhpUndefinedMethodInspection */
                return JText::_('COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID');
            }
            $result = OpenEstateWrapper::initEnvironment($scriptPath);
            if (is_string($result)) {
                return $result;
            }
        }
        $setup = new immotool_setup_index();
        immotool_functions::init($setup, 'load_config_index');

        // load translations
        $translations = array();
        $jLang = JFactory::getLanguage();
        $lang = OpenEstateWrapper::loadTranslations($jLang->getTag(), $translations);

        // get current filter values
        $values = OpenEstateWrapper::parseValuesFromTxt($this->value);
        //echo '<pre>'; print_r( $values ); echo '</pre>';

        // show widgets for any available filter
        $filterIds = array();
        $output = '<table style="float:left;" cellpadding="0" cellspacing="0">';
        foreach (immotool_functions::list_available_filters() as $key) {
            $filterObj = immotool_functions::get_filter($key);
            if (!is_object($filterObj)) {
                //echo "Can't find filter object $key<hr/>";
                continue;
            }
            $filterValue = (isset($values[$key])) ? $values[$key] : '';
            $filterWidget = $filterObj->getWidget($filterValue, $lang, $translations, $setup);
            if (!is_string($filterWidget) || strlen($filterWidget) == 0) {
                //echo "Can't create widget for filter object $key<hr/>";
                continue;
            }
            $filterWidget = str_replace('<select ', '<select onchange="openestate_update_tag();" ', $filterWidget);
            $filterWidget = str_replace('<input ', '<input onchange="openestate_update_tag();" ', $filterWidget);
            $filterWidget = str_replace('<label ', '<label style="display:inline; clear:none;" ', $filterWidget);
            $output .= '<tr>';
            $output .= '<td style="padding-bottom:4px;">' . $filterWidget . '</td>';
            $output .= '</tr>';
            $filterIds[] = '\'' . $key . '\'';
        }
        $output .= '</table>';
        $output .= '<textarea '
            . 'id="' . $this->id . '" '
            . 'name="' . $this->name . '"' . '" '
            . 'cols="10" rows="5" '
            . 'style="display:none;">'
            . $this->value
            . '</textarea>';

        /** @noinspection JSUnusedLocalSymbols */
        /** @noinspection JSPrimitiveTypeWrapperUsage */
        /** @noinspection ES6ConvertVarToLetConst */
        $output .= '
<script type="text/javascript">
function openestate_update_tag()
{
  //alert( \'openestate_update_tag\' );
  var obj = document.getElementById(\'' . $this->id . '\');
  if (obj==null) return;

  var obj2 = null;
  var params = \'\';
  var filters = new Array(' . implode(', ', $filterIds) . ');
  for (var i=0; i<filters.length; i++)
  {
    obj2 = document.getElementById(\'filter_\'+filters[i]);
    if (obj2==null) continue;
    var val = \'\';
    //alert( filters[i] + \': \' + obj2.checked );
    if (obj2.checked===true || obj2.checked===false)
    {
      if (obj2.checked===true) val = obj2.value;
    }
    else
    {
      val = obj2.value;
    }
    if (val!==\'\')
    {
      if (params.length>0) params += \'|||\';
      params += filters[i] + \'=\' + val;
    }
  }
  obj.innerHTML = params;
}
//openestate_update_tag();
</script>';
        return $output;
    }
}