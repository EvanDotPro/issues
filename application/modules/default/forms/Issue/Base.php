<?php
class Default_Form_Issue_Base extends Issues_FormAbstract
{
    public function init()
    {
        $this->addElement('text', 'title', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(5, 255)),
            ),
            'required'   => true,
            'label'      => $this->translate('title')
        ));

        $this->addElement('select', 'project', array(
            'required'   => true,
            'label'      => $this->translate('project'),
            'multiOptions'   => Zend_Registry::get('Default_DiContainer')
                        ->getProjectService()
                        ->getProjectsForForm(),
        ));

        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(5)),
            ),
            'required'   => true,
        ));

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'decorators' => array('ViewHelper',array('HtmlTag', array('tag' => 'dd', 'id' => 'form-submit')))
        ));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'form')),
            array('Description', array('placement' => 'prepend', 'class' => 'error')),
            'Form'
        ));
    }
}