<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class ProjectsModelPlans extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                '`pl`.`id`', '`pl`.`id`',
                '`title`', '`title`',
            );
        }
        parent::__construct($config);
    }

    protected function _getListQuery()
    {
        $db =& $this->getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('`pl`.`id`, `pr`.`title`, `pl`.`path`')
            ->from("`#__prj_plans` as `pl`")
            ->leftJoin("`#__prj_projects` as `pr` ON `pr`.`id` = `pl`.`prjID`");
        // Фильтруем по проекту.
        $project = $this->getState('filter.project');
        if (is_numeric($project))
        {
            $query->where('`pl`.`prjID` = ' . (int) $project);
        }

        /* Сортировка */
        $orderCol  = $this->state->get('list.ordering', '`pl`.`id`');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = array();
        foreach ($items as $item) {
            $arr['id'] = $item->id;
            $url = JRoute::_("index.php?option=com_projects&amp;view=plan&amp;layout=edit&amp;id={$item->id}");
            $link = JHtml::link($url, $item->title);
            $arr['title'] = $link;
            $arr['path'] = $item->path;
            $result[] = $arr;
        }
        return $result;
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = null, $direction = null)
    {
        $project = $this->getUserStateFromRequest($this->context . '.filter.project', 'filter_project');
        $this->setState('filter.project', $project);
        parent::populateState('`pl`.`id`', 'asc');
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.project');
        return parent::getStoreId($id);
    }
}