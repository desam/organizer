<?php
namespace Application\UserBundle\Entity;

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
                return <result> {$i/name} {$i/description} </result>';        
        
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
 }   
 