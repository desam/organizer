<?php
namespace Application\UserBundle\Entity;

use Imagine;

class GroupRequest
{
	/**
     * @validation:NotBlank
     */
    protected $groupname;

	/**
     * @validation:NotBlank
     */
    protected $groupdescription;
    
    /**
     * @validation:NotBlank
    */
    public $idmembre = array();
    
    
    public function getGroupName(){
        return $this->groupname;
    }

    public function setGroupName($name){
        $this->groupname = $name;
    }
    
    public function getGroupDescription(){
        return $this->groupdescription;
    }

    public function setGroupDescription($name){
        $this->groupdescription = $name;
    }
    
    public function getAvatar() {
        return $this->avatar;
    }
    
    public function setAvatar($image) {
        $dir      = realpath(__DIR__ . '/../../../../web/uploads/avatar');
        $filename = uniqid() . '.png';
        $imagine  = new Imagine\Gd\Imagine();

        $image = $imagine->open($image);
        $image->thumbnail(new Imagine\Image\Box(240, $image->getSize()->getHeight()), Imagine\ImageInterface::THUMBNAIL_INSET)
            ->crop(new Imagine\Image\Point(0, 0), new Imagine\Image\Box(240, 198))
            ->save($dir . '/' . $filename);

        $this->avatar = $filename;
    }
    
    
    public function getGroups($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());
        
        $query ='<results> { for $i in document("/db/orga/groups.xml")//group[./user/@refuser="'.$id.'"]
            return <result> {$i/@id} {$i/name} {$i/description} </result>} </results>';        
        
        $result = $db->xquery($query) or die ($db->getError());         
        $xml = simplexml_load_string($result["XML"]);                 
        return $xml;        
    }
    
    public function getGroup($id,$gid){
    
        $db = new eXist();	
        $db->connect() or die ($db->getError());

        $query ='for $i in document("/db/orga/groups.xml")//group[./user/@refuser="'.$id.'" and @id="'.$gid.'"]
                return <result> {$i/name} {$i/description} {$i/avatar} </result>';        
        
        $result = $db->xquery($query) or die ($db->getError());         
        $xml = simplexml_load_string($result["XML"]);                 
        return $xml;        
    }
    
    public function getALLGroups($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());
        
        $query ='<results> { for $i in document("/db/orga/groups.xml")//group 
                let $a:=document("/db/orga/groups.xml")//user[./@refuser="'.$id.'" and ../@id= $i/@id]
                where count($a)=0 return 
                <result> {$i/@id} {$i/name} {$i/description} {$i/user} {$i/avatar} </result>} </results>';
        
        $result = $db->xquery($query) or die ($db->getError());  

        
        
        $xml = simplexml_load_string($result["XML"]);                 
        return $xml;                
    }
   
    public function editGroup($id, $gid){
        $db = new eXist();	
        $db->connect() or die ($db->getError());  

        $query ='update replace document("/db/orga/groups.xml")//group[@id = "'.$gid.'"] with 
            <group id="'.$gid.'"> 
                <name>'.$this->getGroupName().'</name> 
                <description>'.$this->getGroupDescription().'</description>
                <user refuser="'.$id.'"/> 
                <avatar>'.$this->getAvatar().'</avatar>
            </group>';        
        
        $result = $db->xquery($query) or
        (preg_match('/No data found/', $db->getError()) or
         die($db->getError()));      
        
    }
    
    public function setAttributes($xml){        
        $this->setGroupName($xml[0]->name);
        $this->setGroupDescription($xml[0]->description);        
    }
       
    
    public function createGroup($id){        
        $db = new eXist();	
        $db->connect() or die ($db->getError());	        
        $query ='update insert 
                <group id="G{count(document("/db/orga/groups.xml")//group)+1}">
                    <name>'.$this->getGroupName().'</name>
                    <description>'.$this->getGroupDescription().'</description>
                    <user refuser="'.$id.'"/>
                    <avatar>'.$this->getAvatar().'</avatar>
                </group>
                into document("/db/orga/groups.xml")//groups';                       
        $result = $db->xquery($query) or (preg_match('/No data found/', $db->getError()) or die($db->getError()));           
    }
    
    public function deleteGroup($gid){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	        
        $query ='for $i in document("/db/orga/groups.xml")//group[@id = "'.$gid.'"] 
            return update delete $i';    
        $result = $db->xquery($query) or (preg_match('/No data found/', $db->getError()) or die($db->getError()));
    }
    
    public function subscribeGroup($id,$gid){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	        
        $query = 'update insert <user refuser="'.$id.'"/> into document("/db/orga/groups.xml")//group[@id="'.$gid.'"]';
        $result = $db->xquery($query) or (preg_match('/No data found/', $db->getError()) or die($db->getError()));
    }
    
    public function unsubscribeGroup($id,$gid){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	        
        $query ='for $i in document("/db/orga/groups.xml")//group[@id="'.$gid.'"]/user[@refuser="'.$id.'"] return update delete $i';
        $result = $db->xquery($query) or (preg_match('/No data found/', $db->getError()) or die($db->getError()));    
    }
    
    public function getsubscribedGroups($id){
        $db = new eXist();	
        $db->connect() or die ($db->getError());	     
        
        $query ='<results> { for $i in document("/db/orga/groups.xml")//group[./user/@refuser="'.$id.'"]
                return <result> {$i/@id} {$i/name} {$i/description} </result>} </results>';
        
        $result = $db->xquery($query) or die ($db->getError());         
        $xml = simplexml_load_string($result["XML"]);                 
        return $xml;                                
    }
 }   
 