<?php

namespace Application\Form;

use Zend\Form\Form;

class GroupForm extends Form
{
    public function __construct()
    {
        parent::__construct('group');
        $this->setAttribute('method', 'post');

        $this->add([
            'name'       => 'name',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'name',
                'maxlength' => 64,
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'description',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'description',
                'maxlength' => 255,
                'class'    => 'form-control'
            ]
        ]);

    }
}
