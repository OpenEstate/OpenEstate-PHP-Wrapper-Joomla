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

class OpenestateController extends JControllerLegacy {

  /**
   * @var         string  The default view.
   * @since   1.6
   */
  protected $default_view = 'wrapper';

  /**
   * Method to display a view.
   *
   * @param   boolean                     If true, the view output will be cached
   * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
   *
   * @return  JController         This object to support chaining.
   * @since   1.5
   */
  public function display($cachable = false, $urlparams = false) {
    parent::display();
    return $this;
  }

}
