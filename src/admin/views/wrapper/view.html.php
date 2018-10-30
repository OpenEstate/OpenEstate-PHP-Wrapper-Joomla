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

/** @noinspection PhpInconsistentReturnPointsInspection */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.parameter');

class OpenestateViewWrapper extends JViewLegacy
{
    public $sidebar;
    public $infobar;
    public $form;
    public $errors;

    /**
     * Execute and display a template script.
     *
     * @param string $tpl
     * The name of the template file to parse; automatically searches through the template paths.
     *
     * @return mixed
     * A string if successful, otherwise an Error object.
     *
     * @see \JViewLegacy::loadTemplate()
     * @since 3.0
     */
    function display($tpl = null)
    {
        /** @noinspection PhpIncludeInspection */
        require_once(JPATH_COMPONENT . '/helpers/openestate.php');
        /** @noinspection PhpIncludeInspection */
        require_once(JPATH_ROOT . '/components/com_openestate/openestate.wrapper.php');

        // get component settings
        $params = OpenEstateWrapper::getParameters();

        // build general components
        OpenestateHelper::addTitle('wrapper');
        $this->sidebar = OpenestateHelper::buildSidebar('wrapper');
        $this->infobar = OpenestateHelper::buildInfobar('wrapper');

        // get entry in extension table
        $table = JTable::getInstance('extension');
        if (!$table->load(array('name' => 'com_openestate'))) {
            JError::raiseWarning(500, 'Not a valid component');
            return false;
        }
        //echo '<pre>'; print_r( $table ); echo '</pre>';

        // process form for script configuration
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            $post = JRequest::get('post');
            if (!is_array($post)) {
                $post = array();
            }
            //echo '<pre>'; print_r( $post ); echo '</pre>';

            // script path must end with a /
            $scriptPath = (isset($post['main']['script_path'])) ? $post['main']['script_path'] : '';
            $len = strlen($scriptPath);
            if ($len > 0 && $scriptPath[$len - 1] != '/') {
                $scriptPath .= '/';
            }
            $params['script_path'] = $scriptPath;

            // script URL must end with a /
            $scriptUrl = (isset($post['main']['script_url'])) ? $post['main']['script_url'] : '';
            $len = strlen($scriptUrl);
            if ($len > 0 && $scriptUrl[$len - 1] != '/') {
                $scriptUrl .= '/';
            }
            $params['script_url'] = $scriptUrl;

            // bind parameters to table
            if (!isset($table->params) || !is_array($table->params)) {
                $table->params = array();
            }
            $table->bind(array('params' => $params));

            // pre-save checks
            if (!$table->check()) {
                //die('CHECK FAILED!!!');
                JError::raiseWarning(500, $table->getError());
                return false;
            }

            // save the changes
            if (!$table->store()) {
                //die('STORE FAILED!!!');
                JError::raiseWarning(500, $table->getError());
                return false;
            }
        }

        // build form for script configuration
        $this->form = JForm::getInstance('wrapper', JPATH_COMPONENT_ADMINISTRATOR . '/form.wrapper.xml');
        //echo '<pre>' . print_r( $params, true ) . '</pre>';
        foreach ($params as $key => $value) {
            $this->form->setValue($key, 'main', $value);
        }

        // check configuration
        $this->errors = array();

        // check configuration of script path
        $translations = null;
        $scriptPath = OpenEstateWrapper::getScriptPath($params);
        if (!is_string($scriptPath) || strlen(trim($scriptPath)) == 0) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->errors[] = JText::_('COM_OPENESTATE_WRAPPER_ERROR_PATH_EMPTY');
        } else if (!is_dir($scriptPath)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->errors[] = JText::_('COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID');
        } else {
            // load script environment
            $environmentFiles = array('config.php', 'private.php', 'include/functions.php', 'data/language.php');
            define('IMMOTOOL_BASE_PATH', $scriptPath);
            foreach ($environmentFiles as $file) {
                if (!is_file(IMMOTOOL_BASE_PATH . $file)) {
                    /** @noinspection PhpMethodParametersCountMismatchInspection */
                    $this->errors[] = JText::sprintf('COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_FILE', $file);
                }
            }
            if (count($this->errors) == 0) {
                define('IN_WEBSITE', 1);
                foreach ($environmentFiles as $file) {
                    //echo IMMOTOOL_BASE_PATH . $file . '<hr/>';
                    /** @noinspection PhpIncludeInspection */
                    include(IMMOTOOL_BASE_PATH . $file);
                }
                if (!defined('IMMOTOOL_SCRIPT_VERSION')) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->errors[] = JText::_('COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_VERSION');
                }

                // load translations
                $translations = array();
                $jLang = JFactory::getLanguage();
                /** @noinspection PhpUnusedLocalVariableInspection */
                $lang = OpenEstateWrapper::loadTranslations($jLang->getTag(), $translations);
                if ($translations == null || count($translations) == 0) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->errors[] = JText::_('COM_OPENESTATE_WRAPPER_ERROR_CANT_LOAD_TRANSLATION');
                }
            }
        }

        // check configuration of script URL
        $scriptUrl = OpenEstateWrapper::getScriptUrl($params);
        if (!is_string($scriptUrl) || strlen(trim($scriptUrl)) == 0) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->errors[] = JText::_('COM_OPENESTATE_WRAPPER_ERROR_URL_EMPTY');
        }

        // render page
        parent::display($tpl);
    }
}