<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class ProjectsModelContracts extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                '`id`', '`id`',
                '`c`.`dat`',  '`c`.`dat`',
                '`project`',  '`project`',
                '`manager`',  '`manager`',
                '`c`.`state`',  '`c`.`state`',
            );
        }
        parent::__construct($config);
    }

    protected function _getListQuery()
    {
        $db =& $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("`c`.`id`, DATE_FORMAT(`c`.`dat`,'%d.%m.%Y') as `dat`, `c`.`status`, `c`.`currency`, `c`.`discount`, `c`.`markup`, `c`.`state`")
            ->select("`p`.`title` as `project`")
            ->select("`e`.`title_ru_full`, `e`.`title_ru_short`, `e`.`title_en`")
            ->select("`u`.`name` as `manager`")
            ->select("`g`.`title` as `group`")
            ->from("`#__prj_contracts` as `c`")
            ->leftJoin("`#__prj_projects` AS `p` ON `p`.`id` = `c`.`prjID`")
            ->leftJoin("`#__prj_exp` as `e` ON `e`.`id` = `expID`")
            ->leftJoin("`#__users` as `u` ON `u`.`id` = `c`.`managerID`")
            ->leftJoin("`#__usergroups` as `g` ON `g`.`id` = `c`.`groupID`");

        /* Фильтр */
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('`e`.`title_ru_full` LIKE ' . $search . 'OR `e`.`title_ru_short` LIKE ' . $search . 'OR `e`.`title_en` LIKE ' . $search . 'OR `p`.`title` LIKE ' . $search);
        }
        // Фильтруем по состоянию.
        /*$published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('`c.`state` = ' . (int)$published);
        } elseif ($published === '') {
            $query->where('(`c`.`state` = 0 OR `c`.`state` = 1)');
        }*/
        // Фильтруем по проекту.
        $project = $this->getState('filter.project');
        if (is_numeric($project)) {
            $query->where('`c`.`prjID` = ' . (int)$project);
        }
        // Фильтруем по экспоненту.
        $exhibitor = $this->getState('filter.exhibitor');
        if (is_numeric($exhibitor)) {
            $query->where('`c`.`expID` = ' . (int)$exhibitor);
        }
        // Фильтруем по менеджеру.
        $manager = $this->getState('filter.manager');
        if (is_numeric($manager)) {
            $query->where('`c`.`managerID` = ' . (int)$manager);
        }
        // Фильтруем по статусу.
        $status = $this->getState('filter.status');
        if (is_numeric($status)) {
            if ($status != -1) {
                $query->where('`c`.`status` = ' . (int)$status);
            }
            else {
                $query->where('`c`.`status` IS NULL');
            }
        }
        /* Фильтр по ID проекта (только через GET) */
        $id = JFactory::getApplication()->input->getInt('id', 0);
        if ($id != 0)
        {
            $query->where("`c`.`id` = {$id}");
        }

        /* Сортировка */
        $orderCol  = $this->state->get('list.ordering', '`c`.`id`');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = array('items' => array(), 'amount' => array('rub' => 0, 'usd' => 0, 'eur' => 0));
        $ids = array();
        $format = JFactory::getApplication()->input->getString('format', 'html');
        foreach ($items as $item) {
            $ids[] = $item->id;
            $arr['id'] = $item->id;
            $arr['dat'] = $item->dat;
            $arr['project'] = $item->project;
            $arr['currency'] = $item->currency;
            $url = JRoute::_("index.php?option=com_projects&amp;view=todos&amp;filter_contract={$item->id}");
            $link = JHtml::link($url, JText::sprintf('COM_PROJECTS_HEAD_TODO_TODOS'));
            if ($format == 'html') $arr['todo'] = $link;
            $arr['exponent'] = ProjectsHelper::getExpTitle($item->title_ru_short, $item->title_ru_full, $item->title_en);
            $arr['manager']['title'] = $item->manager ?? JText::sprintf('COM_PROJECTS_HEAD_CONTRACT_MANAGER_UNDEFINED');
            $arr['manager']['class'] = (!empty($item->manager)) ? '' : 'no-data';
            $arr['group']['title'] = $item->group ?? JText::sprintf('COM_PROJECTS_HEAD_CONTRACT_PROJECT_GROUP_UNDEFINED');
            $arr['group']['class'] = (!empty($item->group)) ? '' : 'no-data';
            $arr['status'] = ProjectsHelper::getExpStatus($item->status);
            $amount = $this->getAmount($item);
            $arr['amount'] = ($format != 'html') ? $amount : sprintf("%s %s", $this->getAmount($item), $item->currency);
            $arr['debt'] = ($format != 'html') ? $amount - $this->getDebt($item->id) : sprintf("%s %s", $amount - $this->getDebt($item->id), $item->currency);
            $arr['state'] = $item->state;
            $result['items'][] = $arr;
            $result['amount'][$item->currency] += $amount;
        }
        return $result;
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $project = $this->getUserStateFromRequest($this->context . '.filter.project', 'filter_project');
        $exhibitor = $this->getUserStateFromRequest($this->context . '.filter.exhibitor', 'filter_exhibitor');
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
        $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.search', $search);
        $this->setState('filter.project', $project);
        $this->setState('filter.exhibitor', $exhibitor);
        $this->setState('filter.manager', $manager);
        $this->setState('filter.manager', $status);
        $this->setState('filter.state', $published);
        parent::populateState('`c`.`id`', 'asc');
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.project');
        $id .= ':' . $this->getState('filter.exhibitor');
        $id .= ':' . $this->getState('filter.manager');
        $id .= ':' . $this->getState('filter.status');
        $id .= ':' . $this->getState('filter.state');
        return parent::getStoreId($id);
    }

    /**
     * Расчёт стоимости договора
     * @param object $item   объект со сделкой
     * @return float
     * @since 1.2.0
     */
    private function getAmount(object $item): float
    {
        $db =& $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("ROUND(SUM(IFNULL(`i`.`price_{$item->currency}`,0)*`v`.`value`)*{$item->discount}*{$item->markup}, 2) as `amount`")
            ->from("`#__prj_contract_items` as `v`")
            ->leftJoin("`#__prc_items` as `i` ON `i`.`id` = `v`.`itemID`")
            ->where("`v`.`contractID` = {$item->id}");
        return (float) 0 + $db->setQuery($query)->loadResult();
    }

    /**
     * @param int  $contract_id ID сделки
     * @return float
     * @since 1.2.4
     */
    private function getDebt(int $contract_id): float
    {
        $db =& $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("IFNULL(SUM(`amount`),0)")
            ->from("`#__prj_scores`")
            ->where("`contractID` = {$contract_id}")
            ->where("`state` = 1");
        return (float) $db->setQuery($query)->loadResult();
    }

}