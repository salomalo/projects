<?php
defined('_JEXEC') or die;
$listOrder    = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction'));
?>
<tr>
    <th width="1%" class="hidden-phone">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::sprintf('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width="5%">
        <?php echo JHtml::_('grid.sort', 'JSTATUS', '`state`', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_PROJECTS_HEAD_TITLE', '`title`', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_PROJECTS_HEAD_TITLE_PRICE_DESC', '`price`', $listDirn, $listOrder); ?>
    </th>
    <th width="1%">
        <?php echo JHtml::_('grid.sort', 'ID', '`id`', $listDirn, $listOrder); ?>
    </th>
</tr>