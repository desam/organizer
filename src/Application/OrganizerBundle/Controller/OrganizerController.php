<?php

namespace Application\OrganizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\CalendarBundle\Event\EventForm;
use Symfony\Component\Form\CsrfProvider\SessionCsrfProvider;

class OrganizerController extends Controller
{
    public function indexAction()
    {
        $request = $this->get('request');

        //must be logged in
        $user = $this->get('security.context')->getToken()->getUser();

        // get calendar from and to dates
        $start = $request->query->get('from');
        $range = $request->query->get('range');
        $range = isset($range) ? $range : 7;
        $from = new \DateTime($start);
        $to = clone($from);
        $to->add(new \DateInterval('P'. ($range - 1) .'D'));

        // generate csrf token
        $form = EventForm::create($this->get('form.context'), 'event');
        $token = $form->get($form->getCsrfFieldName())->getDisplayedData();

        return $this->render('OrganizerBundle:Organizer:index.twig.html',
                             array(
                                   'from' => $from,
                                   'to' => $to,
                                   'token' => $token,
                                   'user' => $user,
                                   'server' => $_SERVER['HTTP_HOST'],
                                   ));
    }
}
