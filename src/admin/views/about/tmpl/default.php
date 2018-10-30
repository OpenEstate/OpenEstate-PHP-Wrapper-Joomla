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

// no direct access
defined('_JEXEC') or die('Restricted access');
$mainContainerClass = (!empty($this->sidebar)) ? 'span10' : '';
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
    <h2><?php echo JText::_('COM_OPENESTATE_ABOUT_TITLE'); ?></h2>
    <p><?php echo JText::sprintf('COM_OPENESTATE_ABOUT_INFO', 'http://openestate.org'); ?></p>
    <ul>
        <li>
            <?php echo JText::_('COM_OPENESTATE_ABOUT_LICENSE'); ?>:
            <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPL2 (or later)</a>
        </li>
        <li>
            <?php echo JText::_('COM_OPENESTATE_ABOUT_AUTHORS'); ?>:
            Andreas Rudolph &amp; Walter Wagner
        </li>
    </ul>
    <h3><?php echo JText::_('COM_OPENESTATE_ABOUT_SUPPORTUS'); ?></h3>
    <p><?php echo JText::_('COM_OPENESTATE_ABOUT_SUPPORTUS_TEXT'); ?></p>
    <ul>
        <li><?php echo JText::_('COM_OPENESTATE_ABOUT_SUPPORTUS_BY_RECOMMENDATION'); ?></li>
        <li><?php echo JText::_('COM_OPENESTATE_ABOUT_SUPPORTUS_BY_TRANSLATION'); ?></li>
        <li><?php echo JText::_('COM_OPENESTATE_ABOUT_SUPPORTUS_BY_SPONSORING'); ?></li>
    </ul>
</div>