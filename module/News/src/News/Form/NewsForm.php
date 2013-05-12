<?php
namespace News\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class NewsForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('news');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'news_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array( // Список тем
            'name' => 'theme_id',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Theme title',
                'value_options' => array(),
            ),
        ));
        $this->add(array(
            'name' => 'date',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Date',
            ),
        ));
        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Text',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}