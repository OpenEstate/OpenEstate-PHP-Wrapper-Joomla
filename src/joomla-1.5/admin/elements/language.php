<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: language.php 586 2010-12-10 07:42:45Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class JElementLanguage extends JElement {
  /**
   * Element name
   *
   * @access      protected
   * @var         string
   */
  var $_name = 'Language';

  function fetchElement($name, $value, &$node, $control_name) {
    
    // Skript-Umgebung ggf. einbinden
    if (!defined('IMMOTOOL_BASE_PATH')) {
      $parameters = OpenEstateWrapper::getParameters();
      if ($parameters==null) return '';
      $scriptPath = OpenEstateWrapper::getScriptPath( $parameters );
      if (!is_dir($scriptPath)) return JText::_( 'WRAPPER_ERROR_PATH_INVALID' );
      $result = OpenEstateWrapper::initEnvironment( $scriptPath );
      if (is_string($result)) return $result;
    }

    $class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'inputbox';
    $output = '<select id="'.$control_name . '[' . $name . ']"'.'" name="'.$control_name . '[' . $name . ']"'.'" class="'.$class.'">';
    foreach (immotool_functions::get_language_codes() as $code) {
      $selected = ($value==$code)? 'selected="selected"': '';
      $output .= '<option value="' . $code . '" ' . $selected . '>' . immotool_functions::get_language_name( $code ) . '</option>';
    }
    $output .= '</select>';
    return $output;
  }
}
?>