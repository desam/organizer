<?php
namespace Application\NewsLetterBundle\Controller;

use Application\NewsLetterBundle\Entity\NewsForm;
use Application\NewsLetterBundle\Entity\NewsRequest;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\NewsLetterBundle\Entity\eXist;
use Symfony\Bundle\SwiftmailerBundle;

class NewsController extends Controller{

    public function addsenderAction(){
        $session = $this->get('request')->getSession();              
        if($session->get('id') == null){
           return $this->forward('UserBundle:User:login');       
        }       
        $NewsRequest = new NewsRequest();	
        $id = $session->get('id');   
        $id = substr($id, 1);        
        $form = NewsForm::create($this->get('form.context'),'news');		
		$form->bind($this->get('request'), $NewsRequest);			
		if ($form->isValid()) {				
			$NewsRequest->addsender($id);                      
            return $this->forward('UserBundle:User:index');
		}        
        return $this->render('NewsLetterBundle:News:Newsadd.html.twig',array('form' => $form));      
    }
    
    public function deletesenderAction(){
    
        $session = $this->get('request')->getSession();              
        if($session->get('id') == null){
           return $this->forward('UserBundle:User:login');       
        }       
        $NewsRequest = new NewsRequest();	
        $id = $session->get('id');   
        $id = substr($id, 1);
        $NewsRequest->deletesender($id);
        return $this->forward('UserBundle:User:index');       
        
    }
    
    public function sendnewsAction(){
        $NewsRequest = new NewsRequest();	
        $groupXML = $NewsRequest->getSenders();
        
        $senders = "";
        for($i= 0 ; $groupXML->result[$i]!=null; $i++){            
            $senders .= $groupXML->result[$i]->attributes()->email.',';            
        }
        $senders = substr($senders, 0, -1);        
        
        
        $mailer = $this->get('mailer');
        $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('miageoranizer@gmail.com')
        ->setTo('sathiya_r13@hotmail.com')
        ->setBody("this is a teste message");
        $mailer->send($message);
        
        die($senders);  
        
    }
    
    
    
    
}