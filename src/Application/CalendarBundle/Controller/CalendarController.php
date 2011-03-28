<?php

namespace Application\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Application\CalendarBundle\Event\EventForm;
use Application\CalendarBundle\Event\EventRequest;
use Symfony\Component\Form\CsrfProvider\SessionCsrfProvider;

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
            $events = $this->get('eventmanager')->getByGroupAndDate(
                $group, $from->format('Y-m-d'), $to->format('Y-m-d').' 23:59');
            $events = $this->get('eventmanager')->toJSON($events);

            $response = new Response($events);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $form = EventForm::create($this->get('form.context'), 'event');
        $token = $form->get($form->getCsrfFieldName())->getDisplayedData();
        /* $provider = new SessionCsrfProvider($this->get('session'), 'xpathrocks'); */
        /* $token = $provider->generateCsrfToken('Application\CalendarBundle\Event\EventForm'); */

        return $this->render('CalendarBundle:Calendar:index.twig.html',
                             array(
                                   'from' => $from,
                                   'to' => $to,
                                   'token' => $token,
                                   ));
    }

    public function addAction()
    {
        $eventRequest = new EventRequest();
        $form = EventForm::create($this->get('form.context'), 'event');

        if('POST' === $this->get('request')->getMethod()) {
            $form->bind($this->get('request'), $eventRequest);

            if($form->isValid()) {
                $event = $this->get('eventmanager')->hash2xml($eventRequest->toHash());

                $this->get('eventmanager')->insert($event);
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
        $this->get('eventmanager')->deleteByID($id);
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
                $event = $this->get('eventmanager')->hash2xml($eventRequest->toHash());

                $this->get('eventmanager')->updateByID($id, $event);
                return $this->redirect('/');
            }
        } else { //GET
            $e = $this->get('eventmanager')->getByID($id);
            $hash = $this->get('eventmanager')->xml2hash($e);

            $r = EventRequest::fromHash($hash);
            $form->bind($this->get('request'), $r);
        }

        return $this->render('CalendarBundle:Calendar:newevent.twig.html',
                             array(
                                   'form' => $form
                                   ));
    }
}
