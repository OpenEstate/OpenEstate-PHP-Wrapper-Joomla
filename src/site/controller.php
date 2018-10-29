<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2018 OpenEstate.org
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

class OpenestateController extends JControllerLegacy
{
    /**
     * Typical view method for MVC based architecture
     *
     * This function is provide as a default implementation, in most cases
     * you will need to override it in your own controllers.
     *
     * @param boolean $cachable
     * If true, the view output will be cached
     *
     * @param array $urlparams
     * An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return \JControllerLegacy
     * A \JControllerLegacy object to support chaining.
     *
     * @since 3.0
     */
    public function display($cachable = false, $urlparams = array())
    {
        parent::display();
        return $this;
    }
}