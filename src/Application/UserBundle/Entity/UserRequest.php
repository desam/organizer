<?php
    namespace Application\UserBundle\Entity;

    use Imagine;
    

    class UserRequest{
    
    /**
     * @validation:NotBlank
     */
    protected $login;

    /**
     * @validation:NotBlank
     */
    protected $pass;

    /**
     * @validation:NotBlank
     */
    protected  $firstname;

    /**
     * @validation:NotBlank
     */
    protected $surname;

    /**
     * @validation:Email
     * @validation:NotBlank
     */
    protected $mail;

    /**
     * @validation:NotBlank  
     * @validation:Regex("/^\d{2}-\d{2}-\d{2}-\d{2}-\d{2}$/")   
     */
    protected $phone;

     
    protected $avatar;


    public function setLogin($login){
        $this->login = $login;
    }

    public function setPass($pass){
        $this->pass = $pass;
    }

    public function getLogin(){
        return $this->login;
    }

    public function getPass(){
        return $this->pass;
    }

    public function getFirstName(){
        return $this->firstname;
    }

    public function setFirstName( $firstname){
        $this->firstname = $firstname;
    }  

    public function getSurName(){
        return $this->surname;
    }

    public function setSurName( $surname){
        $this->surname = $surname;
    }

    public function getMail(){
        return $this->mail;
    }

    public function setMail( $mail){
        $this->mail = $mail;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function setPhone( $phone){
        $this->phone = $phone;
    }

    public function getAvatar(){        
        return $this->avatar;
    }

    public function setAvatar($image){             
        $dir = realpath(__DIR__ . './../../../../web/uploads/avatar');         
        $filename = uniqid() . '.png';
        $imagine = new Imagine\Gd\Imagine();
        $image = $imagine->open($image);
        $image->thumbnail(new Imagine\Image\Box(240, $image->getSize()->getHeight()), Imagine\ImageInterface::THUMBNAIL_INSET)
            ->crop(new Imagine\Image\Point(0, 0), new Imagine\Image\Box(240, 198))
            ->save($dir . '/' . $filename);                                    
        $this->avatar = $filename;        
    }	


    public function addUser(){        
        $db = new eXist();	
        $db->connect() or die ($db->getError());	        
        $query ='update insert 
            <user id="U{count(document("/db/orga/users.xml")//user)+1}">
                <login>'.$this->getLogin().'</login>
                <password>'.hash('sha512',$this->getPass()).'</password>
                <firstname>'.$this->getFirstName().'</firstname>
                <surname>'.$this->getSurName().'</surname>
                <mail>'.$this->getMail().'</mail>
                <phone>'.$this->getPhone().'</phone>
                <avatar>'.$this->getAvatar().'</avatar>
            </user>
            into document("/db/orga/users.xml")//users ';
        $result = $db->xquery($query) or 
        (preg_match('/No data found/', $db->getError()) or
         die($db->getError()));           
    }

    public function getUser($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	
        $query = 'for $i in document("/db/orga/users.xml")//user[@id ="'.$id.'"] return $i';                     
        $result = $db->xquery($query) or die ($db->getError());         
        $xml = simplexml_load_string($result["XML"]);         
        return $xml;
    }
    

    public function setAttributes($xml){
        $this->setLogin($xml[0]->login);      
        $this->setFirstName($xml[0]->firstname);
        $this->setSurName($xml[0]->surname);
        $this->setMail($xml[0]->mail);
        $this->setPhone($xml[0]->phone);      
    }

    public function editUser($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());        
        $query ='update replace document("/db/orga/users.xml")//user[@id = "'.$id.'"] with 
            <user id="'.$id.'">                
                <login>'.$this->getLogin().'</login>
                <password>'.hash('sha512',$this->getPass()).'</password>
                <firstname>'.$this->getFirstName().'</firstname>
                <surname>'.$this->getSurName().'</surname>
                <mail>'.$this->getMail().'</mail>
                <phone>'.$this->getPhone().'</phone>
                <avatar>'.$this->getAvatar().'</avatar>
            </user>';
        $result = $db->xquery($query) or
        (preg_match('/No data found/', $db->getError()) or
         die($db->getError()));    
    }
    

}
?>