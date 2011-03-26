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

        //TODO regex ou DateTimeField !
        /* $this->add(new DateTimeField('from')); */
        /* $this->add(new DateTimeField('to')); */
        $this->add(new TextField('from'));
        $this->add(new TextField('to'));

        $this->add(new TextField('location'));

        $this->add(new TextField('refgrp'));
        $this->add(new TextField('id'));
        //TODO
        //choice field guest
        //choice field group?
    }
}
