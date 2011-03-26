<?php
namespace Application\UserBundle\Entity;

class LoginRequest
{
	/**
     * @validation:NotBlank
     */
    protected $login;

	/**
     * @validation:NotBlank
     */
    protected $pass;
    
    protected $id;
    
    protected $firstname;
    
    protected $surname;
    
    protected $avatar;

  

    public function setLogin($login)
    {
        $this->login = $login;
    }
	
	public function setPass($pass)
    {
        $this->pass = $pass;
    }

    public function getLogin()
    {
        return $this->login;
    }
	
	public function getPass()
    {
        return $this->pass;
    }  
    public function getUserId(){
        return $this->id;
    }
    
    public function setUserId($id){
        $this->id = $id;
    }
    
    public function getFirstName(){
        return $this->firstname;
    }
    
    public function setFirstName($fn){
        $this->firstname = $fn;
    }
    
    public function getSurName(){
        return $this->surname;
    }
    
    public function setSurName($sn){
        $this->surname = $sn;
    }
    
    
    
    public function getAvatar(){
        return $this->avatar;
    }
    public function setAvatar($image){
        $this->avatar = $image;    
    }
	
    public function toLogin(){    
        $db = new eXist();	
        $db->connect() or die ($db->getError());	
        $query ='for $i in document("/db/orga/users.xml")//user[./login="'.$this->getLogin().'"] return <result> {$i/password} {$i/firstname } {$i/surname}  {$i/avatar} <id> {$i/@id} </id> </result>';                
        $result = $db->xquery($query) or die ($db->getError());    
        $xml = simplexml_load_string($result["XML"]);                         
        $realpass = $xml[0]->password;               
        $checkpass = hash('sha512',$this->getPass());            
        if ($realpass == $checkpass){                                
            $this->setFirstName($xml[0]->firstname);
            $this->setSurName($xml[0]->surname);
            $this->setAvatar($xml[0]->avatar);
            $this->setUserId($xml->id->attributes()->id);            
            return true;            
        }else{
            return false;
        }
    }
    
}
?>