<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: filter.php 586 2010-12-10 07:42:45Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class JElementFilter extends JElement {
  /**
   * Element name
   *
   * @access      protected
   * @var         string
   */
  var $_name = 'Filter';

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

    // Übersetzungen ermitteln
    $translations = array();
    $jLang = &JFactory::getLanguage();
    $lang = OpenEstateWrapper::loadTranslations( $jLang->getTag(), $translations );

    // Filter-Werte ermitteln
    $values = OpenEstateWrapper::parseValuesFromTxt( $value );
    //echo '<pre>'; print_r( $values ); echo '</pre>';

    // Widgets der vorhandenen Filter erzeugen
    $filterIds = array();
    foreach (immotool_functions::list_available_filters() as $key) {
      $filterObj = immotool_functions::get_filter( $key );
      if (!is_object($filterObj)) {
        //echo "Filter-Objekt $key nicht gefunden<hr/>";
        continue;
      }
      $filterValue = (isset($values[$key]))? $values[$key]: '';
      $filterWidget = $filterObj->getWidget( $filterValue, $lang, $translations, $setupIndex );
      if (!is_string($filterWidget) || strlen($filterWidget)==0) {
        //echo "Filter-Widget $key nicht erzeugt<hr/>";
        continue;
      }
      //$filterWidget = str_replace( 'id="filter_'.$key.'"', 'id="'.$control_name.'['.$name.']['.$key.']"', $filterWidget );
      //$filterWidget = str_replace( 'name="filter['.$key.']"', 'name="'.$control_name.'['.$name.']['.$key.']"', $filterWidget );
      $filterWidget = str_replace( '<select ', '<select onchange="build_tag();" ', $filterWidget );
      $filterWidget = str_replace( '<input ', '<input onchange="build_tag();" ', $filterWidget );
      $output .= '<div style="margin-bottom:4px;">' . $filterWidget . '</div>';
      $filterIds[] = '\''.$key.'\'';
    }

    $output .= '<textarea id="'.$control_name . '[' . $name . ']"'.'" name="'.$control_name . '[' . $name . ']"'.'" cols="50" rows="5" style="visibility:hidden; position:absolute;">'.$value.'</textarea>';
    $output .= '<script type="text/javascript">
<!--
function build_tag()
{
  //alert( \'build_tag\' );
  var obj = document.getElementById(\''.$control_name.'['.$name.']'.'\');
  if (obj==null) return;

  var obj2 = null;
  var params = \'\';
  var filters = new Array('.implode(', ', $filterIds).');
  for (var i=0; i<filters.length; i++)
  {
    obj2 = document.getElementById(\'filter_\'+filters[i]);
    if (obj2==null) continue;
    val = \'\';
    //alert( filters[i] + \': \' + obj2.checked );
    if (obj2.checked==true || obj2.checked==false)
    {
      if (obj2.checked==true) val = obj2.value;
    }
    else
    {
      val = obj2.value;
    }
    if (val!=\'\') params += filters[i] + \'=\' + val + \'\n\';
  }

  obj.innerHTML = params;
}
//build_tag();
-->
</script>';

    return $output;
  }
}
?>