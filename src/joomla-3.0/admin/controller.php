<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: controller.php 2071 2013-02-13 14:46:18Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2013, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

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
?>