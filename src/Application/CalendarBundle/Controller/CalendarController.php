<?php

namespace Application\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Application\CalendarBundle\Event\EventManager;
use Application\CalendarBundle\Event\EventForm;
use Application\CalendarBundle\Event\EventRequest;

class CalendarController extends Controller
{
    public function indexAction($group, $start)
    {
        // $group = "1";
        // $start = "2010-09-25";
        $howlong = 7;

        //build a one-week array
        $dates = array();
        $dates[] = new \DateTime($start);
        for($i = 1; $i < $howlong; $i++) {
            $dates[$i] = clone($dates[0]);
            $dates[$i]->add(new \DateInterval('P'. $i .'D'));
        }

        if($this->get('request')->isXmlHttpRequest()) {
            $events = EventManager::getByGroupAndDate(
                $group, $dates[0]->format('Y-m-d'), $dates[6]->format('Y-m-d').' 23:59');
            $events = EventManager::toJSON($events);

            $response = new Response($events);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render('CalendarBundle:Calendar:index.twig.html',
            array(
                'dates' => $dates,
            ));
    }

    public function addAction()
    {
        $eventRequest = new EventRequest();
        $form = EventForm::create($this->get('form.context'), 'event');

        if('POST' === $this->get('request')->getMethod()) {
            $form->bind($this->get('request'), $eventRequest);

            if($form->isValid()) {
                $eventRequest->create();
                return $this->redirect('/');
            }
        }

        return $this->render('CalendarBundle:Calendar:newevent.twig.html',
            array(
                'form' => $form
            ));
    }

    public function deleteAction($id)
    {
        EventManager::deleteByID($id);
        return $this->forward('CalendarBundle:Calendar:index',
            array(
                'removed' => $id
            ));
    }

    public function replaceAction()
    {
        // if POST, replace
    }
}
