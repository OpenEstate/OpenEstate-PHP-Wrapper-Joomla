<?php
/**
 * OpenEstate-PHP-Wrapper für Joomla.
 * $Id: default.php 2071 2013-02-13 14:46:18Z andy $
 *
 * @package OpenEstate
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2013, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$mainContainerClass = (!empty( $this->sidebar))? 'span10': '';
?>

<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
  <?php echo $this->sidebar; ?>
</div>
<?php endif;?>

<div id="j-main-container" class="<?php echo $mainContainerClass; ?>">
  <?php if (!empty( $this->infobar)) echo $this->infobar; ?>
  <h2><?php echo JText::_( 'COM_OPENESTATE_ABOUT_TITLE' ); ?></h2>
  <p><?php echo JText::sprintf( 'COM_OPENESTATE_ABOUT_INFO', 'http://openestate.org' ); ?></p>
  <ul>
    <li><?php echo JText::_( 'COM_OPENESTATE_ABOUT_LICENSE' ); ?>: <a href="./components/com_openestate/gpl-3.0-standalone.html" target="_blank">GPL3</a></li>
    <li><?php echo JText::_( 'COM_OPENESTATE_ABOUT_AUTHORS' ); ?>: Andreas Rudolph &amp; Walter Wagner</li>
  </ul>
  <h3><?php echo JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS' ); ?></h3>
  <p><?php echo JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_TEXT' ); ?></p>
  <ul>
    <li><?php echo JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_BY_RECOMMENDATION' ); ?></li>
    <li><?php echo JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_BY_TRANSLATION' ); ?></li>
    <li><?php echo JText::_( 'COM_OPENESTATE_ABOUT_SUPPORTUS_BY_SPONSORING' ); ?></li>
  </ul>
</div>