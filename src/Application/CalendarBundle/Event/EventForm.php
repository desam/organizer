<?php

namespace Application\CalendarBundle\Event;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\DateTimeField;

/**
 * EventForm
 **/
class EventForm extends Form
{
    protected function configure()
    {
        $this->add(new TextField('title'));
        $this->add(new DateTimeField('from'));
        $this->add(new DateTimeField('to'));
        $this->add(new TextField('location'));
        //choice field guest
        //choice field group?
    }
}
