<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class ProjectsViewStand extends HtmlView {
    protected $item, $form, $script, $id, $isAdmin;

    public function display($tmp = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->script = $this->get('Script');
        $this->isAdmin = ProjectsHelper::canDo('projects.exec.edit');

        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    protected function addToolbar() {
        //JFactory::getApplication()->input->set('hidemainmenu', true);
        $title =$this->item->title;

        JToolbarHelper::title($title, '');
	    JToolBarHelper::apply('stand.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('stand.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('stand.cancel', 'JTOOLBAR_CLOSE');
    }

    protected function setDocument() {
        JHtml::_('bootstrap.framework');
    }
}