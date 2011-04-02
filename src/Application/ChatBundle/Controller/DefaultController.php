<?php

namespace Application\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //must be logged in
        $user = $this->get('security.context')->getToken()->getUser();

        return $this->render('ChatBundle:Default:index.html.twig',
                             array('user' => $user));
    }
}
