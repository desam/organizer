<?php

namespace Application\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\CalendarBundle\Entity\eXist;

class CalendarController extends Controller
{
    public function indexAction()
    {
        $db = new eXist();
        $db->connect() or die($db->getError());

        $query = '
            <results>
            { 
            for $i in document("td4/book.xml")//section[contains(title, "Database")]
            return 
            <result>
            {$i/@*}{$i/title}
            </result>
            }
            </results>
        ';
            
        $result = $db->xquery($query) or die($db->getError());

        return $this->render('CalendarBundle:Calendar:section.twig.html', array('xml' => $result['XML']));
    }
}
