<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
?>
<tr>
    <td colspan="7" style="text-align: right;">
        <?php echo JText::sprintf('COM_PROJECTS_HEAD_ITEM_PRICE_RUB');?>
    </td>
    <td>
        <?php echo number_format($this->items['amount']['rub'], 0, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['payments']['rub'], 0, '.', " ");?>
    </td>
    <td colspan="3">
        <?php echo number_format($this->items['debt']['rub'], 0, '.', " ");?>
    </td>
</tr>
<tr>
    <td colspan="7" style="text-align: right;">
        <?php echo JText::sprintf('COM_PROJECTS_HEAD_ITEM_PRICE_USD');?>
    </td>
    <td>
        <?php echo number_format($this->items['amount']['usd'], 0, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['payments']['usd'], 0, '.', " ");?>
    </td>
    <td colspan="3">
        <?php echo number_format($this->items['debt']['usd'], 0, '.', " ");?>
    </td>
</tr>
<tr>
    <td colspan="7" style="text-align: right;">
        <?php echo JText::sprintf('COM_PROJECTS_HEAD_ITEM_PRICE_EUR');?>
    </td>
    <td>
        <?php echo number_format($this->items['amount']['eur'], 0, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['payments']['eur'], 0, '.', " ");?>
    </td>
    <td colspan="3">
        <?php echo number_format($this->items['debt']['eur'], 0, '.', " ");?>
    </td>
</tr>