<?php

namespace Application\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Application\CalendarBundle\Event\EventManager;
use Application\CalendarBundle\Event\EventForm;
use Application\CalendarBundle\Event\EventRequest;

class CalendarController extends Controller
{
    public function indexAction()
    {
        $request = $this->get('request');
        $start = $request->query->get('from');
        $range = $request->query->get('range');
        $range = isset($range) ? $range : 7;

        $from = new \DateTime($start);
        $to = clone($from);
        $to->add(new \DateInterval('P'. ($range - 1) .'D'));

        if($request->isXmlHttpRequest()) {
            $group = $request->query->get('group');
            $events = EventManager::getByGroupAndDate(
                $group, $from->format('Y-m-d'), $to->format('Y-m-d').' 23:59');
            $events = EventManager::toJSON($events);

            $response = new Response($events);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render('CalendarBundle:Calendar:index.twig.html',
            array(
                'from' => $from,
                'to' => $to,
            ));
    }

    public function addAction()
    {
        $eventRequest = new EventRequest();
        $form = EventForm::create($this->get('form.context'), 'event');

        if('POST' === $this->get('request')->getMethod()) {
            $form->bind($this->get('request'), $eventRequest);

            if($form->isValid()) {
		$event = EventManager::hash2xml($eventRequest->toHash());

		EventManager::insert($event);
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

    //TODO check permissions
    public function updateAction($id)
    {
        $eventRequest = new EventRequest();
        $form = EventForm::create($this->get('form.context'), 'event');

        if('POST' === $this->get('request')->getMethod()) {
            $form->bind($this->get('request'), $eventRequest);

            if($form->isValid()) {
		$event = EventManager::hash2xml($eventRequest->toHash());

		EventManager::updateByID($id, $event);
                return $this->redirect('/');
            }
        } else { //GET
	    $e = EventManager::getByID($id);
	    $hash = EventManager::xml2hash($e);

	    $r = EventRequest::fromHash($hash);
	    $form->bind($this->get('request'), $r);
	}

        return $this->render('CalendarBundle:Calendar:newevent.twig.html',
            array(
                'form' => $form
            ));
    }
}
