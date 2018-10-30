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

class OpenestateViewAbout extends JViewLegacy
{
    public $sidebar;
    public $infobar;

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

        // build general components
        OpenestateHelper::addTitle('about');
        $this->sidebar = OpenestateHelper::buildSidebar('about');
        $this->infobar = OpenestateHelper::buildInfobar('about');

        // render page
        parent::display($tpl);
    }
}