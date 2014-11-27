<?php
/*
 * A Joomla module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2014 OpenEstate.org
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

$fieldSet = $this->form->getFieldset('wrapper');
$mainContainerClass = (!empty($this->sidebar)) ? 'span10' : '';

$document = & JFactory::getDocument();
$document->addStyleDeclaration('#main_script_path, #main_script_url {
  width: 400px !important;
}');
?>

<?php if (!empty($this->sidebar)) : ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
<?php endif; ?>

<div id="j-main-container" class="<?php echo $mainContainerClass; ?>">
  <?php
  if (!empty($this->infobar)) {
    echo $this->infobar;
  }
  ?>
  <h2><?php echo JText::_('COM_OPENESTATE_WRAPPER_TITLE'); ?></h2>
  <p><?php echo JText::sprintf('COM_OPENESTATE_WRAPPER_INFO', 'http://wiki.openestate.org/Kategorie:ImmoTool_PHP-Export'); ?></p>

  <h3><?php echo JText::_('COM_OPENESTATE_WRAPPER_SETUP'); ?></h3>
  <form action="index.php" method="post">
    <fieldset class="adminform">
      <table>
        <?php foreach ($fieldSet as $field): ?>
          <tr>
            <td style="padding-right:1em;"><?php echo $field->label; ?></td>
            <td><?php echo $field->input; ?></td>
          </tr>
          <tr>
            <td colspan="2" style="font-style:italic; padding-bottom:15px; color:#a0a0a0;"><?php echo JText::_($field->description); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </fieldset>
    <input type="hidden" name="option" value="com_openestate" />
    <input type="hidden" name="task" value="wrapper" />
    <input type="submit" value="<?php echo JText::_('COM_OPENESTATE_WRAPPER_SUBMIT'); ?>" style="margin-left:15em; margin-top:1em;" />
  </form>

  <?php if (isset($this->errors) && is_array($this->errors) && count($this->errors) > 0) : ?>
    <h3 style="color:red;"><?php echo JText::_('COM_OPENESTATE_WRAPPER_ERROR'); ?></h3>
    <ul>
      <?php
      foreach ($this->errors as $error) {
        echo '<li>' . $error . '</li>';
      }
      ?>
      <li><?php echo JText::_('COM_OPENESTATE_WRAPPER_INFO_JOOMLA'); ?><br/><b style="font-family:monospace;"><?php echo JPATH_ROOT; ?></b></li>
    </ul>
  <?php else : ?>
    <h3 style="color:green;"><?php echo JText::_('COM_OPENESTATE_WRAPPER_SUCCESS'); ?></h3>
    <ul>
      <li><?php echo JText::_('COM_OPENESTATE_WRAPPER_INFO_VERSION'); ?><b><?php echo IMMOTOOL_SCRIPT_VERSION; ?></b></li>
      <li><?php echo JText::sprintf('COM_OPENESTATE_WRAPPER_SUCCESS_MESSAGE', 'index.php?option=com_menus'); ?></li>
    </ul>
  <?php endif; ?>
</div>
