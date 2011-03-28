<?php
namespace Application\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\UserBundle\Entity\UserForm;
use Application\UserBundle\Entity\XQueryUser;
use Application\UserBundle\Entity\XQueryUserManager;
use Symfony\Component\Security\Core\SecurityContext;


class UserController extends Controller {

    public function loginAction() {
        // get the error if any (works with forward and redirect -- see below)
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('UserBundle:Login:login.html.twig',
                   array(
                       'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
                        'error' => $error));
    }

    public function indexAction() {
        $userRequest = new XQueryUser();
        $session     = $this->get('request')->getSession();
        $id          = $session->get('id');
        $id          = substr($id, 1);
        $userXML     = $this->get('usermanager')->getUser($id);

        return $this->render('UserBundle:User:UserIndex.html.twig',
                             array('firstname'=>$userXML[0]->firstname,
                                   'surname'=>$userXML[0]->surname,
                                   'image'=>$userXML[0]->avatar));
    }

    public function addUserAction() {
        $newuser = new XQueryUser();
        $form = UserForm::create($this->get('form.context'), 'newuser');
        $form->bind($this->get('request'), $newuser);

        if ($form->isValid()) {
            $this->get('usermanager')->addUser($newuser);
            return $this->forward('UserBundle:User:login');

        }

        return $this->render('UserBundle:User:Add.html.twig',
                                 array('form' => $form));
    }

    public function editUserAction() {
        $user = $this->get('security.context')->getToken()->getUser();

        $form = UserForm::create($this->get('form.context'), 'edituser');
        $form->bind($this->get('request'), $user);

        if ($form->isValid()) {
            $this->get('usermanager')->editUser($user);
            return $this->render('UserBundle:User:UserIndex.html.twig',
                                 array(
                                      'firstname'=>$user->getFirstName(),
                                      'surname'=>$user->getSurName(),
                                      'image'=>$user->getAvatar()));
        }

        return $this->render('UserBundle:User:Edit.html.twig',
                             array('form' => $form));
    }
}