<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: language.php 906 2011-06-16 00:23:35Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2011, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.form.formfield');
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class JFormFieldLanguage extends JFormField {

  /**
   * The form field type.
   *
   * @var         string
   * @since       1.6
   */
  public $type = 'Language';

  protected function getInput() {

    // Skript-Umgebung ggf. einbinden
    if (!defined('IMMOTOOL_BASE_PATH')) {
      $parameters = OpenEstateWrapper::getParameters();
      if ($parameters==null) return '';
      $scriptPath = OpenEstateWrapper::getScriptPath( $parameters );
      if (!is_dir($scriptPath)) return JText::_( 'COM_OPENESTATE_WRAPPER_ERROR_PATH_INVALID' );
      $result = OpenEstateWrapper::initEnvironment( $scriptPath );
      if (is_string($result)) return $result;
    }

    $class = $this->element['class'] ? $this->element['class'] : 'inputbox';
    $output = '<select id="'.$this->id.'" name="'.$this->name.'" class="'.$class.'">';
    foreach (immotool_functions::get_language_codes() as $code) {
      $selected = ($this->value==$code)? 'selected="selected"': '';
      $output .= '<option value="' . $code . '" ' . $selected . '>' . immotool_functions::get_language_name( $code ) . '</option>';
    }
    $output .= '</select>';
    return $output;
  }
}
?>