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

class JFormFieldLanguage extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     * @since 1.6
     */
    public $type = 'Language';

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

        // build widget for language selection
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