<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class ProjectsModelBuilding extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'title_ru_short',
                'contract',
                'freeze',
                'search',
                'project',
                'sq',
                'manager',
                'standtype',
                'standstatus',
            );
        }
        parent::__construct($config);
    }

    protected function _getListQuery()
    {
        $db =& $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('`s`.`id` as `standID`, `s`.`number` as `stand`, IF(`s`.`freeze` IS NULL,0,1) as `freeze`, `s`.`tip`, `s`.`status`')
            ->select("`e`.`title_ru_full`, `e`.`title_ru_short`, `e`.`title_en`, `e`.`id` as `exponentID`")
            ->select("`c`.`number` as `contract`, `c`.`id` as `contractID`")
            ->select("`u`.`name` as `manager`")
            ->select("IFNULL((SELECT SUM(`value`) FROM `#__prj_stat` WHERE `contractID` = `c`.`id` AND `is_sq`=1),0) as `sq`")
            ->from("`#__prj_stands` as `s`")
            ->leftJoin("`#__prj_contracts` as `c` ON `c`.`id` = `s`.`contractID`")
            ->leftJoin("`#__prj_exp` as `e` ON `e`.`id` = `c`.`expID`")
            ->leftJoin("`#__users` as `u` ON `u`.`id` = `c`.`managerID`")
            ->where("`c`.`status` = 1")
            ->where("`s`.`number` IS NOT NULL");
        /* Фильтр */
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('(`title_ru_full` LIKE ' . $search . 'OR `title_ru_short` LIKE ' . $search . 'OR `title_en` LIKE ' . $search . ')');
        }
        // Фильтруем по проекту.
        $project = $this->getState('filter.project');
        if (is_numeric($project)) {
            $query->where('`c`.`prjID` = ' . (int)$project);
        }
        // Фильтруем по менеджеру.
        $manager = $this->getState('filter.manager');
        if (is_numeric($manager)) {
            $query->where('`c`.`managerID` = ' . (int)$manager);
        }
        // Фильтруем по типу стенда.
        $standtype = $this->getState('filter.standtype');
        if (is_numeric($standtype)) {
            $query->where('`s`.`tip` = ' . (int)$standtype);
        }
        // Фильтруем по статусу стенда.
        $standstatus = $this->getState('filter.standstatus');
        if (is_numeric($standstatus)) {
            $query->where('`s`.`status` = ' . (int)$standstatus);
        }

        /* Сортировка */
        $orderCol  = $this->state->get('list.ordering', '`title_ru_short`');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    public function getItems($raw = false)
    {
        $items = parent::getItems();
        $results = array();
        $stands = array(); //Массив контракт - массив стендов
        $itog = array(); //Итоговый массив
        $return = base64_encode(JUri::base() . "index.php?option=com_projects&view=building");
        foreach ($items as $item) {
            $url = JRoute::_("index.php?option=com_projects&amp;task=exhibitor.edit&amp;id={$item->exponentID}&amp;return={$return}");
            $exhibitor = ProjectsHelper::getExpTitle($item->title_ru_short, $item->title_ru_full, $item->title_en);
            $link = JHtml::link($url, $exhibitor);
            $arr['exhibitor'] = $link;
            $url = JRoute::_("index.php?option=com_projects&amp;task=stand.edit&amp;id={$item->standID}&amp;return={$return}");
            $title = sprintf("%s (%s, %s)", $item->stand, ProjectsHelper::getStandType($item->tip), ProjectsHelper::getStandStatus($item->status));
            $stands[$item->contractID][] = JHtml::link($url, $title);
            $number = (!empty($item->contract)) ? JText::sprintf('COM_PROJECTS_HEAD_TODO_DOGOVOR_N', $item->contract) : JText::sprintf('COM_PROJECTS_TITLE_CONTRACT_WITHOUT_NUMBER');
            $url = JRoute::_("index.php?option=com_projects&amp;task=contract.edit&amp;id={$item->contractID}&amp;return={$return}");
            $arr['contract'] = JHtml::link($url,$number);
            $arr['sq'] = sprintf("%s %s", $item->sq , JText::sprintf('COM_PROJECTS_HEAD_ITEM_UNIT_SQM'));
            $arr['contractID'] = $item->contractID;
            $arr['manager'] = $item->manager;
            $arr['freeze'] = JText::sprintf(($item->freeze != 0) ? 'JYES' : 'JNO');
            $results[] = $arr;
        }
        foreach ($results as $result)
        {
            if (!isset($itog[$result['contractID']]))
            {
                $itog[$result['contractID']] = $result;
                $itog[$result['contractID']]['stands'] = implode(' / ', $stands[$result['contractID']]);
            }
        }
        return $itog;
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $project = $this->getUserStateFromRequest($this->context . '.filter.project', 'filter_project');
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $standtype = $this->getUserStateFromRequest($this->context . '.filter.standtype', 'filter_standtype');
        $standstatus = $this->getUserStateFromRequest($this->context . '.filter.standstatus', 'filter_standstatus');
        $this->setState('filter.search', $search);
        $this->setState('filter.project', $project);
        $this->setState('filter.manager', $manager);
        $this->setState('filter.standtype', $standtype);
        $this->setState('filter.standstatus', $standstatus);
        parent::populateState('`title_ru_short`', 'asc');
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.project');
        $id .= ':' . $this->getState('filter.manager');
        $id .= ':' . $this->getState('filter.standtype');
        $id .= ':' . $this->getState('filter.standstatus');
        return parent::getStoreId($id);
    }
}