<?php

namespace Application\Form;

use Zend\Form\Form;

class GroupMinutesLimitForm extends Form
{
    public function __construct()
    {
        parent::__construct('group');
        $this->setAttribute('method', 'post');

        $this->add([
            'name'       => 'daily',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'daily',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'weekly',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'weekly',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'monthly',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'monthly',
                'class'    => 'form-control'
            ]
        ]);

    }
}
