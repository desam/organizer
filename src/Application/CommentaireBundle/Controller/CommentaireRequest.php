<?php 
namespace Application\CommentaireBundle\Controller;

class CommentaireRequest
{
    protected $id;
    protected $ida;
    protected $idu;
    protected $datepublication;
    protected $titre;
    protected $message;
    
    public function __construct()
    {
    
    }
    
    public function setid($id)
    {
       $this->id = $id;    
    }
    
    public function getid()
    {
        return $this->id;
    } 
    
    public function setida($ida)
    {
        $this->ida = $ida;
    }
    
    public function getida()
    {
        return $this->ida;
    }
    
    public function getidu()
    {
        return $this->idu;
    }
    
    public function setidu($idu)
    {
        $this->idu = $idu;
    }
    
    public function settitre($titre)
    {
        $this->titre = $titre;
    }

    public function gettitre()
    {
        return $this->titre;
    }

    public function getmessage()
    {
        return $this->message;
    }
    
    public function setmessage($message)
    {
        $this->message = $message;
    }
    
    public function setdatepublication($datep)
    {
        $this->datepublication = $datep;
    }
    
    public function getdatepublication()
    {
        return $this->datepublication;
    }
    
    public function send()
    {
        echo 'formulaire validé';    
    }
}