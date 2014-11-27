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

jimport('joomla.application.component.view');
include_once( JPATH_ROOT . DS . 'components' . DS . 'com_openestate' . DS . 'openestate.wrapper.php' );

class OpenestateViewListing extends JView {

  function display($tpl = null) {

    $parameters = OpenEstateWrapper::getParameters();
    if ($parameters == null) {
      $msg = '<h2>' . JText::_('COM_OPENESTATE_ERROR') . '</h2><p>' . JText::_('COM_OPENESTATE_CANT_LOAD_COMPONENT_SETTINGS') . '</p>';
      $this->assignRef('content', $msg);
      parent::display($tpl);
      return;
    }
    $scriptPath = OpenEstateWrapper::getScriptPath($parameters);
    $scriptUrl = OpenEstateWrapper::getScriptUrl($parameters);

    // Skript-Umgebung ggf. einbinden
    if (!defined('IMMOTOOL_BASE_URL')) {
      define('IMMOTOOL_BASE_URL', $scriptUrl);
    }
    if (!defined('IMMOTOOL_BASE_PATH')) {
      if (!is_dir($scriptPath)) {
        $msg = '<h2>' . JText::_('COM_OPENESTATE_ERROR') . '</h2><p>' . JText::_('COM_OPENESTATE_CANT_FIND_SCRIPTS') . '</p>';
        $this->assignRef('content', $msg);
        parent::display($tpl);
        return;
      }
      if (is_file($scriptPath . 'immotool.php.lock')) {
        $msg = '<h2>' . JText::_('COM_OPENESTATE_NOTICE') . '</h2><p>' . JText::_('COM_OPENESTATE_UPDATE_IS_RUNNING') . '</p>';
        $this->assignRef('content', $msg);
        parent::display($tpl);
        return;
      }
      $result = OpenEstateWrapper::initEnvironment($scriptPath, false);
      if (is_string($result)) {
        $msg = '<h2>' . JText::_('COM_OPENESTATE_ERROR') . '</h2><p>' . $result . '</p>';
        $this->assignRef('content', $msg);
        parent::display($tpl);
        return;
      }
    }
    else if (is_file(IMMOTOOL_BASE_PATH . 'immotool.php.lock')) {
      $msg = '<h2>' . JText::_('COM_OPENESTATE_NOTICE') . '</h2><p>' . JText::_('COM_OPENESTATE_UPDATE_IS_RUNNING') . '</p>';
      $this->assignRef('content', $msg);
      parent::display($tpl);
      return;
    }

    // Konfiguration des Menü-Eintrages ermitteln
    $view = JRequest::getString('view');
    $itemId = JRequest::getInt('Itemid');
    $lang = JRequest::getString('lang');
    $menuParams = null;
    if ($itemId) {
      $menu = JSite::getMenu();
      $menuParams = $menu->getParams($itemId);
    }
    if (!is_object($menuParams)) {
      $msg = '<h2>' . JText::_('COM_OPENESTATE_ERROR') . '</h2><p>' . JText::_('COM_OPENESTATE_CANT_LOAD_MENU_SETTINGS') . '</p>';
      $this->assignRef('content', $msg);
      parent::display($tpl);
      return;
    }
    //echo '<pre>'; print_r( $menuparams ); echo '</pre>';
    // Ausgabe erzeugen
    $baseUrl = null;
    $hiddenParams = array();
    $app = & JFactory::getApplication();
    //echo '<p>';
    //echo 'sef = ' . $app->getCfg('sef');
    //echo ' / sef_rewrite = ' . $app->getCfg('sef_rewrite');
    //echo ' / sef_suffix = ' . $app->getCfg('sef_suffix');
    //echo '</p>';
    if ($app->getCfg('sef') == '1') {
      if ($app->getCfg('sef_rewrite') == '1') {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI']);
        $baseUrl = $requestUrl[0];
      }
      else if (isset($_SERVER['REDIRECT_URL'])) {
        $baseUrl = $_SERVER['REDIRECT_URL'];
      }
      else {
        $baseUrl = $_SERVER['PHP_SELF'];
      }
    }
    else {
      $baseUrl = 'index.php?option=com_openestate&amp;view=' . $view . '&amp;Itemid=' . $itemId;
      $hiddenParams = array(
        'option' => 'com_openestate',
        'view' => $view,
        'Itemid' => $itemId,);
      if (is_string($lang) && strlen($lang) > 0) {
        $baseUrl .= '&amp;lang=' . $lang;
        $hiddenParams['lang'] = $lang;
      }
    }

    $content = OpenEstateWrapper::wrap('index', $baseUrl, $menuParams, $hiddenParams);
    $this->assignRef('content', $content);

    $preText = $menuParams->get('preText');
    if (is_string($preText) && strlen(trim($preText)) > 0) {
      echo '<div id="openestate_wrapper_pretext">' . trim($preText) . '</div>';
    }
    parent::display($tpl);
    $postText = $menuParams->get('postText');
    if (is_string($postText) && strlen(trim($postText)) > 0) {
      echo '<div id="openestate_wrapper_posttext">' . trim($postText) . '</div>';
    }
  }

}
