<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: filter.php 711 2011-02-16 22:23:59Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.form.formfield' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_openestate'.DS.'openestate.wrapper.php' );

class JFormFieldFilter extends JFormField {

  /**
   * The form field type.
   *
   * @var         string
   * @since       1.6
   */
  public $type = 'Filter';

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

    // Übersetzungen ermitteln
    $translations = array();
    $jLang = &JFactory::getLanguage();
    $lang = OpenEstateWrapper::loadTranslations( $jLang->getTag(), $translations );

    // Filter-Werte ermitteln
    $values = OpenEstateWrapper::parseValuesFromTxt( $this->value );
    //echo '<pre>'; print_r( $values ); echo '</pre>';

    // Widgets der vorhandenen Filter erzeugen
    $filterIds = array();
    $output .= '<table style="float:left;" cellpadding="0" cellspacing="0">';
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
      $filterWidget = str_replace( '<select ', '<select onchange="build_tag();" ', $filterWidget );
      $filterWidget = str_replace( '<input ', '<input onchange="build_tag();" ', $filterWidget );
      $filterWidget = str_replace( '<label ', '<label style="display:inline; clear:none;" ', $filterWidget );
      //$filterWidget = str_replace( '</label>', '</span>', $filterWidget );
      $output .= '<tr>';
      $output .= '<td style="padding-bottom:4px;">' . $filterWidget . '</td>';
      $output .= '</tr>';
      $filterIds[] = '\''.$key.'\'';
    }
    $output .= '</table>';
    $output .= '<textarea id="'.$this->id.'" name="'.$this->name.'"'.'" cols="10" rows="5" style="clear:both; width:100%; visibility:hidden; position:absolute;">'.$this->value.'</textarea>';
    $output .= '<script type="text/javascript">
<!--
function build_tag()
{
  //alert( \'build_tag\' );
  var obj = document.getElementById(\''.$this->id.'\');
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
    if (val!=\'\')
    {
      if (params.length>0) params += \'|||\';
      params += filters[i] + \'=\' + val;
    }
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