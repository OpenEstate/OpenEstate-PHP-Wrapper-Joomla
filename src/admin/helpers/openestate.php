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

defined('_JEXEC') or die;

class OpenestateHelper
{
    public static function addTitle($vName)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        JToolbarHelper::title(JText::_('COM_OPENESTATE_TITLE'), 'openestate');
    }

    public static function buildSidebar($vName)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        JHtmlSidebar::addEntry(
            JText::_('COM_OPENESTATE_WRAPPER'), 'index.php?option=com_openestate&view=wrapper', $vName == 'wrapper'
        );
        /** @noinspection PhpUndefinedMethodInspection */
        JHtmlSidebar::addEntry(
            JText::_('COM_OPENESTATE_ABOUT'), 'index.php?option=com_openestate&view=about', $vName == 'about'
        );

        return JHtmlSidebar::render();
    }

    public static function buildInfobar($vName)
    {
        $jLang = JFactory::getLanguage();
        $lang = explode('-', $jLang->getTag());
        $bar = '';
        $bar .= '<div style="float:right;">';
        /** @noinspection HtmlUnknownTarget */
        $bar .= '<a href="https://openestate.org" target="_blank">'
            . '<img src="./components/com_openestate/images/openestate_logo.png" alt="OpenEstate.org" title="OpenEstate.org" border="0" />'
            . '</a>';

        $bar .= '<div style="margin-top:2em; text-align:right;">';

        // PayPal (german)
        if (strtolower($lang[0]) == 'de') {
            $bar .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
            $bar .= '<input type="hidden" name="cmd" value="_s-xclick">';
            $bar .= '<input type="hidden" name="hosted_button_id" value="11005790">';
            $bar .= '<input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal." style="border:none;">';
            $bar .= '<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">';
            $bar .= '</form>';
        } // PayPal (english)
        else {
            $bar .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
            $bar .= '<input type="hidden" name="cmd" value="_s-xclick" />';
            $bar .= '<input type="hidden" name="hosted_button_id" value="7B3J85G4NKY3E" />';
            $bar .= '<input type="image" src="https://www.paypal.com/en_US/DE/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" style="border:none;"/>';
            $bar .= '<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1" />';
            $bar .= '</form>';
        }
        $bar .= '</div>';
        $bar .= '</div>';
        return $bar;
    }
}