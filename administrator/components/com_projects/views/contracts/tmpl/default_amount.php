<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$title = JText::sprintf(is_numeric($this->state->get('filter.project')) ? 'COM_PROJECTS_HEAD_CONTRACT_SUM_TOTAL' : 'COM_PROJECTS_HEAD_CONTRACT_SUM_PAGE');
?>
<tr>
    <td colspan="<?php echo (ProjectsHelper::canDo('core.general')) ? '11' : '10'; ?>" style="text-align: right;">
        <?php echo $title, " ", JText::sprintf('COM_PROJECTS_HEAD_ITEM_PRICE_RUB');?>
    </td>
    <td>
        <?php echo number_format($this->items['amount']['total']['rub'] ?? $this->items['amount']['rub'], 2, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['payments']['total']['rub'] ?? $this->items['payments']['rub'], 2, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['debt']['total']['rub'] ?? $this->items['debt']['rub'], 2, '.', " ");?>
    </td>
</tr>
<tr>
    <td colspan="<?php echo (ProjectsHelper::canDo('core.general')) ? '11' : '10'; ?>" style="text-align: right;">
        <?php echo JText::sprintf('COM_PROJECTS_HEAD_ITEM_PRICE_USD');?>
    </td>
    <td>
        <?php echo number_format($this->items['amount']['total']['usd'] ?? $this->items['amount']['usd'], 2, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['payments']['total']['usd'] ?? $this->items['payments']['usd'], 2, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['debt']['total']['usd'] ?? $this->items['debt']['usd'], 2, '.', " ");?>
    </td>
</tr>
<tr>
    <td colspan="<?php echo (ProjectsHelper::canDo('core.general')) ? '11' : '10'; ?>" style="text-align: right;">
        <?php echo JText::sprintf('COM_PROJECTS_HEAD_ITEM_PRICE_EUR');?>
    </td>
    <td>
        <?php echo number_format($this->items['amount']['total']['eur'] ?? $this->items['amount']['eur'], 2, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['payments']['total']['eur'] ?? $this->items['payments']['eur'], 2, '.', " ");?>
    </td>
    <td>
        <?php echo number_format($this->items['debt']['total']['eur'] ?? $this->items['debt']['eur'], 2, '.', " ");?>
    </td>
</tr>