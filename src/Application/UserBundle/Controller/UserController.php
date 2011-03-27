<?php
namespace Application\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\UserBundle\Entity\eXist;

use Application\UserBundle\Entity\LoginForm;
use Application\UserBundle\Entity\LoginRequest;

use Application\UserBundle\Entity\NewsForm;
use Application\UserBundle\Entity\NewsRequest;

use Application\UserBundle\Entity\UserForm;
use Application\UserBundle\Entity\User;


class UserController extends Controller {

    public function loginAction() {
        $loginRequest = new LoginRequest();
        $form = LoginForm::create($this->get('form.context'),'login');
        $form->bind($this->get('request'), $loginRequest);

        if ($form->isValid()) {

            if($loginRequest->toLogin() == true) {
                $session = $this->get('request')->getSession();
                $session->set('id', '_'. $loginRequest->getUserId());

                return $this->forward('UserBundle:User:index');
            }
        }
        return $this->render('UserBundle:Login:Login.html.twig', array('form' => $form));        
    }

    public function indexAction() {
        $userRequest = new User();
        $session     = $this->get('request')->getSession();
        $id          = $session->get('id');
        $id          = substr($id, 1);
        $userXML     = $userRequest->getUser($id);

        return $this->render('UserBundle:User:UserIndex.html.twig',
                             array('firstname'=>$userXML[0]->firstname,
                                   'surname'=>$userXML[0]->surname,
                                   'image'=>$userXML[0]->avatar));
    }


    public function addUserAction() {
        $userRequest = new User();
        $form = UserForm::create($this->get('form.context'), 'user');
        $form->bind($this->get('request'), $userRequest);

        if ($form->isValid()) {
            $userRequest->addUser();
            $form = LoginForm::create($this->get('form.context'),'login');
            return $this->render('UserBundle:Login:Login.html.twig',array('form' => $form));    
        }
        else{
            return $this->render('UserBundle:User:Add.html.twig',array('form' => $form));            
        }

    }

    public function editUserAction() {

        $session = $this->get('request')->getSession();

        if($session->get('id') == null){
            return $this->forward('UserBundle:User:login');
        }

        $UserRequest = new UserRequest();
        $id = $session->get('id');
        $id = substr($id, 1);
        $userXML = $UserRequest->getUser($id);

        $UserRequest->setAttributes($userXML);
        $form = UserForm::create($this->get('form.context'), 'User');
        $form->bind($this->get('request'), $UserRequest);

        if ($form->isValid()) {
            $UserRequest->editUser($id);
            return $this->render('UserBundle:User:UserIndex.html.twig', array(
                                      'firstname'=>$UserRequest->getFirstName(),
                                      'surname'=>$UserRequest->getSurName(),
                                      'image'=>$UserRequest->getAvatar()));
        }
        return $this->render('UserBundle:User:Edit.html.twig', array(
                                                              'form' => $form));
    }


    public function deconnexionAction(){
        $session = $this->get('request')->getSession();
        $session->remove('id');

        return $this->forward('UserBundle:User:login');
    }
}