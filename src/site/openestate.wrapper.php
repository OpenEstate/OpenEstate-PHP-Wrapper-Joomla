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

//error_reporting( E_ALL );
//ini_set('display_errors','1');

// define name of URL parameters for the wrapped scripts
if (!defined('IMMOTOOL_PARAM_LANG')) {
    define('IMMOTOOL_PARAM_LANG', 'wrapped_lang');
}
if (!defined('IMMOTOOL_PARAM_FAV')) {
    define('IMMOTOOL_PARAM_FAV', 'wrapped_fav');
}
if (!defined('IMMOTOOL_PARAM_INDEX_PAGE')) {
    define('IMMOTOOL_PARAM_INDEX_PAGE', 'wrapped_page');
}
if (!defined('IMMOTOOL_PARAM_INDEX_RESET')) {
    define('IMMOTOOL_PARAM_INDEX_RESET', 'wrapped_reset');
}
if (!defined('IMMOTOOL_PARAM_INDEX_ORDER')) {
    define('IMMOTOOL_PARAM_INDEX_ORDER', 'wrapped_order');
}
if (!defined('IMMOTOOL_PARAM_INDEX_FILTER')) {
    define('IMMOTOOL_PARAM_INDEX_FILTER', 'wrapped_filter');
}
if (!defined('IMMOTOOL_PARAM_INDEX_FILTER_CLEAR')) {
    define('IMMOTOOL_PARAM_INDEX_FILTER_CLEAR', 'wrapped_clearFilters');
}
if (!defined('IMMOTOOL_PARAM_INDEX_VIEW')) {
    define('IMMOTOOL_PARAM_INDEX_VIEW', 'wrapped_view');
}
if (!defined('IMMOTOOL_PARAM_INDEX_MODE')) {
    define('IMMOTOOL_PARAM_INDEX_MODE', 'wrapped_mode');
}
if (!defined('IMMOTOOL_PARAM_EXPOSE_ID')) {
    define('IMMOTOOL_PARAM_EXPOSE_ID', 'wrapped_id');
}
if (!defined('IMMOTOOL_PARAM_EXPOSE_VIEW')) {
    define('IMMOTOOL_PARAM_EXPOSE_VIEW', 'wrapped_view');
}
if (!defined('IMMOTOOL_PARAM_EXPOSE_IMG')) {
    define('IMMOTOOL_PARAM_EXPOSE_IMG', 'wrapped_img');
}
if (!defined('IMMOTOOL_PARAM_EXPOSE_CONTACT')) {
    define('IMMOTOOL_PARAM_EXPOSE_CONTACT', 'wrapped_contact');
}
if (!defined('IMMOTOOL_PARAM_EXPOSE_CAPTCHA')) {
    define('IMMOTOOL_PARAM_EXPOSE_CAPTCHA', 'wrapped_captchacode');
}
if (!defined('OPENESTATE_WRAPPER')) {
    define('OPENESTATE_WRAPPER', '1');
}

class OpenEstateWrapper
{
    public static function getParameters()
    {
        return JComponentHelper::getParams('com_openestate')->toArray();
    }

    public static function getScriptPath($params)
    {
        return (is_array($params) && isset($params['script_path'])) ?
            $params['script_path'] : '';
    }

    public static function getScriptUrl($params)
    {
        return (is_array($params) && isset($params['script_url'])) ?
            $params['script_url'] : '';
    }

    public static function initEnvironment($scriptPath, $doInclude = true)
    {
        if (defined('IMMOTOOL_BASE_PATH')) {
            return false;
        }
        $environmentFiles = array('config.php', 'private.php', 'include/functions.php', 'data/language.php');
        define('IMMOTOOL_BASE_PATH', $scriptPath);
        foreach ($environmentFiles as $file) {
            if (!is_file(IMMOTOOL_BASE_PATH . $file)) {
                return 'File \'' . $file . '\' was not found in export directory!';
            }
        }
        if ($doInclude === true) {
            define('IN_WEBSITE', 1);
            foreach ($environmentFiles as $file) {
                //echo IMMOTOOL_BASE_PATH . $file . '<hr/>';
                /** @noinspection PhpIncludeInspection */
                include(IMMOTOOL_BASE_PATH . $file);
            }
            if (!defined('IMMOTOOL_SCRIPT_VERSION')) {
                return 'Can\'t load version of the PHP export!';
            }
        }
        return true;
    }

    public static function loadTranslations($preferredLang, &$translations)
    {
        $setupIndex = new immotool_setup_index();
        if (!is_string($preferredLang)) {
            $preferredLang = $setupIndex->DefaultLanguage;
        } else if (strpos($preferredLang, '-') !== false) {
            $l = explode('-', $preferredLang);
            $preferredLang = $l[0];
        }
        $lang = immotool_functions::init_language(strtolower($preferredLang), $setupIndex->DefaultLanguage, $translations);
        if (!is_array($translations)) {
            return null;
        }
        return $lang;
    }

    public static function parseValuesFromTxt($txt)
    {
        //$lines = array();

        // in older versions, values are split by \n
        if (strpos(trim($txt), "\n") !== false) {
            $lines = explode("\n", $txt);
        } // in current version, values are written into one line, split by |||
        else {
            $lines = explode("|||", $txt);
        }

        $values = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line == '') {
                continue;
            }
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }
            $key = substr($line, 0, $pos);
            $value = substr($line, $pos + 1);
            $values[$key] = $value;
        }
        return $values;
    }

    public static function wrap($defaultView, $scriptName, $params, $hiddenParams)
    {
        //return '<pre>' . print_r($_REQUEST, true) . '</pre>';
        //return '<pre>' . print_r($_SERVER, true) . '</pre>';
        $document = JFactory::getDocument();
        $app = JFactory::getApplication();
        $menus = (is_object($app)) ? $app->getMenu() : null;
        $menu = (is_object($menus)) ? $menus->getActive() : null;

        // determine the script to load
        $script = null;
        $settings = array();
        $wrap = (isset($_REQUEST['wrap']) && is_string($_REQUEST['wrap'])) ?
            $_REQUEST['wrap'] : $defaultView;
        if ($wrap == 'expose') {
            $wrap = 'expose';
            $script = 'expose.php';

            // set default configuration values on the first request of the page
            if (!isset($_REQUEST['wrap'])) {
                if ($params->get('lang', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_LANG] = $params->get('lang');
                    $settings['lang'] = $params->get('lang');
                }
                if ($params->get('id', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_EXPOSE_ID] = $params->get('id');
                    $settings['id'] = $params->get('id');
                }
                if ($params->get('view', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_EXPOSE_VIEW] = $params->get('view');
                    $settings['view'] = $params->get('view');
                }
            }
        } else {
            $wrap = 'index';
            $script = 'index.php';

            // set default configuration values on the first request of the page
            if (!isset($_REQUEST['wrap'])) {
                $_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER_CLEAR] = '1';
                if ($params->get('lang', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_LANG] = $params->get('lang');
                    $settings['lang'] = $params->get('lang');
                }
                if ($params->get('view', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_INDEX_VIEW] = $params->get('view');
                    $settings['view'] = $params->get('view');
                }
                if ($params->get('mode', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_INDEX_MODE] = $params->get('mode');
                    $settings['mode'] = $params->get('mode');
                }
                if ($params->get('order', null) != null) {
                    $_REQUEST[IMMOTOOL_PARAM_INDEX_ORDER] = $params->get('order');
                    $settings['order'] = $params->get('order');
                }
            }

            // clear filter selections, if this is explicitly selected
            if (isset($_REQUEST[IMMOTOOL_PARAM_INDEX_RESET])) {
                unset($_REQUEST[IMMOTOOL_PARAM_INDEX_RESET]);
                $_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER] = array();
                $_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER_CLEAR] = '1';
            }

            // load configured filter criteria into the request
            if (!isset($_REQUEST['wrap']) || isset($_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER])) {
                $filter = $params->get('filter');
                $filters = OpenEstateWrapper::parseValuesFromTxt($filter);
                if (is_array($filters)) {
                    $settings['filter'] = array();
                    foreach ($filters as $filter => $value) {
                        if (!isset($_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER]) || !is_array($_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER])) {
                            $_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER] = array();
                        }
                        if (!isset($_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER][$filter])) {
                            $_REQUEST[IMMOTOOL_PARAM_INDEX_FILTER][$filter] = $value;
                            $settings['filter'][$filter] = $value;
                        }
                    }
                }
            }
        }

        // keep wrapper settings in a global variable for further use
        $settings['wrap'] = $wrap;
        $GLOBALS['openestate_wrapper_settings'] = $settings;

        // execute the script
        //echo 'wrap: ' . IMMOTOOL_BASE_PATH . $script;
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include(IMMOTOOL_BASE_PATH . $script);
        $page = ob_get_contents();
        ob_end_clean();

        // load configuration
        $setup = null;
        if ($wrap == 'expose') {
            $setup = new immotool_setup_expose();
            if (is_callable(array('immotool_myconfig', 'load_config_expose'))) {
                immotool_myconfig::load_config_expose($setup);
            }
        } else {
            $setup = new immotool_setup_index();
            if (is_callable(array('immotool_myconfig', 'load_config_index'))) {
                immotool_myconfig::load_config_index($setup);
            }
        }

        // make some modifications to the current document
        $lang = (isset($_REQUEST[IMMOTOOL_PARAM_LANG])) ? $_REQUEST[IMMOTOOL_PARAM_LANG] : $params->get('lang');
        if (is_string($lang)) {
            $document->setLanguage($lang);
            $document->setMetaData('language', $lang);
        }

        // add stylesheets to the current document
        $stylesheets = array(IMMOTOOL_BASE_URL . 'style.php?wrapped=1');
        if (is_string($setup->AdditionalStylesheet) && strlen($setup->AdditionalStylesheet) > 0) {
            $stylesheets[] = $setup->AdditionalStylesheet;
        }
        foreach ($stylesheets as $stylesheet) {
            $document->addStyleSheet($stylesheet);
        }

        // set default meta data from the menu configuration
        $metaDescription = (is_object($menu)) ? $menu->params->get('menu-meta_description') : null;
        $metaKeywords = (is_object($menu)) ? $menu->params->get('menu-meta_keywords') : null;

        // further modifications to the current document in expose view
        if ($wrap == 'expose') {
            $exposeId = (isset($_REQUEST[IMMOTOOL_PARAM_EXPOSE_ID])) ? $_REQUEST[IMMOTOOL_PARAM_EXPOSE_ID] : null;
            $exposeObj = (is_string($exposeId)) ? immotool_functions::get_object($exposeId) : null;
            $exposeTxt = (is_string($exposeId)) ? immotool_functions::get_text($exposeId) : null;
            if (is_array($exposeObj)) {

                // add title of the requested property to the current document
                $title = (is_string($lang) && isset($exposeObj['title'][$lang])) ? $exposeObj['title'][$lang] : null;
                if (is_string($title)) {
                    $title = trim(strip_tags(html_entity_decode($title, ENT_NOQUOTES, $setup->Charset)));
                    $document->setTitle($title . ' | ' . $document->getTitle());
                }
            }
            if (is_array($exposeTxt)) {

                // use keywords of the requested property as meta keywords
                $txt = (is_string($lang) && isset($exposeTxt['keywords'][$lang])) ? $exposeTxt['keywords'][$lang] : null;
                if (is_string($txt)) {
                    $metaKeywords = trim(strip_tags(html_entity_decode($txt, ENT_NOQUOTES, $setup->Charset)));
                }

                // use description of the requested property as meta description
                if (is_array($setup->MetaDescriptionTexts)) {
                    foreach ($setup->MetaDescriptionTexts as $attrib) {
                        $txt = (isset($objectTexts) && isset($objectTexts[$attrib][$lang])) ? $objectTexts[$attrib][$lang] : null;
                        if (is_string($txt) && strlen(trim($txt)) > 0) {
                            $metaDescription = trim(strip_tags(html_entity_decode($txt, ENT_NOQUOTES, $setup->Charset)));
                            break;
                        }
                    }
                }
            }
        }

        // add meta description to the current document
        if (is_string($metaDescription) && strlen(trim($metaDescription)) > 0) {
            $document->setMetaData('description', trim($metaDescription));
        }

        // add meta keywords to the current document
        if (is_string($metaKeywords) && strlen(trim($metaKeywords)) > 0) {
            $document->setMetaData('keywords', trim($metaKeywords));
        }

        // convert and return wrapped content
        return immotool_functions::wrap_page(
            $page, $wrap, $scriptName, IMMOTOOL_BASE_URL, array(), $hiddenParams);
    }
}