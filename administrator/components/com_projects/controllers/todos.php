<?php
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Response\JsonResponse;
defined('_JEXEC') or die;

class ProjectsControllerTodos extends AdminController
{
    public function getModel($name = 'Todo', $prefix = 'ProjectsModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    public function getTodosCountOnDate()
    {
        $dat = $this->input->getString('date', null);
        $uid = $this->input->getInt('uid', 0);
        if ($dat == null)
        {
            echo new JsonResponse(array("error" => JText::sprintf('COM_PROJECTS_ERROR_EMPTY_DATE')));
            jexit();
        }
        $dat = JFactory::getDbo()->escape($dat);
        $model = $this->getModel();
        $cnt = $model->getTodosCountOnDate($dat, $uid);
        echo new JsonResponse(array("cnt" => $cnt));
        jexit();
    }

    public function close()
    {
        $model = $this->getModel();
        $model->close();
        $user = JFactory::getUser()->name;
        $dat = date("d.m.Y");
        echo new JsonResponse(array("user" => $user, "dat" => $dat));
        jexit();
    }
}
