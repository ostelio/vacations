<?php

namespace Vacation\Form;

use Zend\Form\Form;


class VacationRequestForm extends Form {

    public function __construct($name = null)
    {
        parent::__construct('album');

        $this->add(array(
            'name' => 'type',
            'type' => 'Text',
            'options' => array(
                'label' => 'Type',
            ),
        ));
        $this->add(array(
            'name' => 'hours',
            'type' => 'Text',
            'options' => array(
                'label' => 'Hours',
            ),
        ));
        $this->add(array(
            'name' => 'day',
            'type' => 'Text',
            'options' => array(
                'label' => 'Day',
            ),
        ));
        $this->add(array(
            'name' => 'month',
            'type' => 'Text',
            'options' => array(
                'label' => 'Month',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }

} 