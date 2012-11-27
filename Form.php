<?php

class Creogen_Form extends Zend_Form
{
    public $viewScript = null;

    public $leftLabelElement = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag'=> 'div')),
        'Label',
        array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'field')),
    );

    public $fullwidthLeftLabelElement = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag'=> 'div', 'class' => 'field-inner fullwidth')),
        'Label',
//        array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'itext fullwidth')),
    );

    public $noLabelElement = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag'=> 'div')),
        array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'field')),
    );

    public $noLabelNoWrapperElement = array(
        'ViewHelper',
    );

    public $underLabelElement = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag'=> 'div', 'class' => 'field-inner')),
        'Label',
        array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'field block')),
    );

    public $underLabelFullwidthElement = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag'=> 'div', 'class' => 'field-inner fullwidth')),
        'Label',
        array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'field block')),
    );

    public function loadDefaultDecorators()
    {
        if ($this->viewScript) {
            $this->setDecorators(array(
                array('ViewScript', array('viewScript' => $this->viewScript))
            ));
        } else {
            $this->setDecorators(array(
                'FormElements',
                'Fieldset',
                'Form',
            ));
        }
    }
}