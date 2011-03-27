<?php
namespace Application\NewsLetterBundle\Entity;
    
class NewsRequest{

    /**
     * @validation:Email
     * @validation:NotBlank
     */
    protected $mail;
    
    public function getMail(){
        return $this->mail;
    }

    public function setMail( $mail){
        $this->mail = $mail;
    }
    
    public function addsender($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	
        
        $query ='update insert  <news_tuple id="N{count(document("/db/orga/news.xml")//news_tuple)+1}" 
        email="'.$this->getMail().'" idref="'.$id.'"/> into document("/db/orga/news.xml")//news ';
        
        $result = $db->xquery($query) or preg_match('/No data found/', $db->getError()) or  die($db->getError());            
    }
    
    public function deletesender($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	        
        $query ='for $i in document("/db/orga/news.xml")//news_tuple[@idref = "'.$id.'"]  
        return update delete $i';
        
        $result = $db->xquery($query) or (preg_match('/No data found/', $db->getError()) or die($db->getError()));            
    }
    
    public function getSenders(){
        $db = new eXist();	
        $db->connect() or die ($db->getError());
        
        $query = '<results> { for $i in document("/db/orga/news.xml")//news_tuple return <result>{$i/@email}
                </result> } </results>';
        
        $result = $db->xquery($query) or (preg_match('/No data found/', $db->getError()) or die($db->getError()));             
        $xml = simplexml_load_string($result["XML"]);         
        return $xml;
    }
    
    
}
    