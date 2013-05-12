<?php
namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use News\Model\News;
use News\Model\Themes;
use News\Form\NewsForm;

class NewsController extends AbstractActionController
{
    protected $newsTable;
    protected $themesTable;

    public function getNewsTable() // Оъект таблица новостей
    {
        if (!$this->newsTable) {
            $sm = $this->getServiceLocator();
            $this->newsTable = $sm->get('News\Model\NewsTable');
        }
        return $this->newsTable;
    }

    public function getThemesTable() // Оъект таблица тем
    {
        if (!$this->themesTable) {
            $sm = $this->getServiceLocator();
            $this->themesTable = $sm->get('News\Model\ThemesTable');
        }
        return $this->themesTable;
    }

    public function indexAction()
    {
        $params = $this->params()->fromQuery();

        return new ViewModel(array(
            'news'    => $this->getNewsTable()->getListNews($params),
            'themes'  => $this->getThemesTable()->getArrayListThemes(),
            'archive' => $this->getNewsTable()->getTreeArchive(),
            'act_themes' => $this->getNewsTable()->getArrayThemes(),
            'pager' => $this->getNewsTable()->setPager($params),
        ));
    }

    public function addAction()
    {
        $form = new NewsForm();

        $form->get('theme_id')->setValueOptions($this->getThemesTable()->getArrayListThemes());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $news = new News();
            //$form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $news->exchangeArray($form->getData());
                $this->getNewsTable()->saveNews($news);

                // Redirect to list of albums
                return $this->redirect()->toRoute('news');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('news', array(
                'action' => 'add'
            ));
        }

        $news = $this->getNewsTable()->getOneNews($id);

        $form  = new NewsForm();
        $form->get('theme_id')->setValueOptions($this->getThemesTable()->getArrayListThemes());
        $form->bind($news);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            //$form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getNewsTable()->saveNews($form->getData());

                // Redirect to list of news
                return $this->redirect()->toRoute('news');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('news');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('news_id');
                $this->getNewsTable()->deleteNews($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('news');
        }

        return array(
            'news_id' => $id,
            'news'    => $this->getNewsTable()->getOneNews($id)
        );
    }

    public function detailAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news', array());
        }
        $new = $this->getNewsTable()->getOneNews($id);

        return array(
            'id'     => $id,
            'new'    => $new,
            'themes' => $this->getThemesTable()->getArrayListThemes(),
        );
    }
}