<?php
namespace Application\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\UserBundle\Entity\eXist;

use Application\UserBundle\Entity\GroupRequest;
use Application\UserBundle\Entity\GroupForm;


class GroupController extends Controller{

    public function createGroupAction(){
        
        $session = $this->get('request')->getSession();              
        if($session->get('id') == null){
           return $this->forward('UserBundle:User:login');       
        }       
        $GroupRequest = new GroupRequest();	
        $id = $session->get('id');   
        $id = substr($id, 1);        
		
		$form = GroupForm::create($this->get('form.context'),'group');		
		$form->bind($this->get('request'), $GroupRequest);			
		if ($form->isValid()) {				
			$GroupRequest->createGroup($id);                      
            return $this->forward('UserBundle:User:index');
		}
        return $this->render('UserBundle:Group:Group.html.twig',array('form' => $form));         
	}
    
    public function listGroupAction(){
    
        $session = $this->get('request')->getSession();              
        if($session->get('id') == null){
           return $this->forward('UserBundle:User:login');       
        } 
        
        $GroupRequest = new GroupRequest();	
        $id = $session->get('id');   
        $id = substr($id, 1);        
        $groupXML = $GroupRequest->getGroups($id);               
        return $this->render('UserBundle:Group:ListGroup.html.twig',array('form' => $groupXML->result)); 
        
        
    }
    
    public function editGroupAction(){    
        $gid = $this->get('request')->get('gid');            
        $session = $this->get('request')->getSession();        
        
        if($session->get('id') == null){
           return $this->forward('UserBundle:User:login');       
        }                  
                
        $GroupRequest = new GroupRequest();	
        $id = $session->get('id');   
        $id = substr($id, 1);        
        
        $groupXML = $GroupRequest->getGroup($id,$gid);
        $GroupRequest->setAttributes($groupXML);         
         
        $form = GroupForm::create($this->get('form.context'), 'Group');
        $form->bind($this->get('request'), $GroupRequest); 
        
        if ($form->isValid()) {
           $GroupRequest->editGroup($id,$gid);
           return $this->forward('UserBundle:Group:listGroup');       
            
        }
        return $this->render('UserBundle:Group:EditGroup.html.twig',array('form' => $form));         
        
    }
    
    public function deleteGroupAction(){    
        $gid = $this->get('request')->get('gid');            
        $session = $this->get('request')->getSession();        
        
        if($session->get('id') == null){
           return $this->forward('UserBundle:User:login');       
        }                  
                
        $GroupRequest = new GroupRequest();	        

        $GroupRequest->deleteGroup($gid);
        return $this->forward('UserBundle:Group:listGroup');       
    
    }
    
}