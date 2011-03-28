<?php 
namespace Application\ArticlesBundle\Controller;

class ArticleRequest
{
    protected $ida;
    protected $idu;
    protected $titre;
    protected $description;
    protected $corps;
    protected $categories;
    
    public function __construct()
    {
    
    }
    
    public function getidu()
    {
        return $this->idu;        
    }
    
    public function setidu($iduser)
    {
        $this->idu = $iduser;    
    }
    
    public function getida()
    {
        return $this->ida;
    }
    
    public function setida($idarticle)
    {
        $this->ida = $idarticle;    
    }
    
    public function settitre($titre)
    {
        $this->titre = $titre;
    }

    public function gettitre()
    {
        return $this->titre;
    }

    public function getdescription()
    {
        return $this->description;
    }
    
    public function setdescription($desc)
    {
        $this->description = $desc;
    }
      
    public function getcorps()
    {
        return $this->corps;
    }
    
    public function setcorps($corps)
    {
        $this->corps = $corps;    
    }
    
    public function getcategories()
    {
        return $this->categories;
    }
    
    public function setcategories($categories)
    {
        $this->categories = $categories;    
    }
    
    public function send()
    {
        echo "formulaire validé";    
    }
    
    public function toFillArticle($hash)
    {
        $new = new ArticleRequest();
        
        if(isset($hash[0]->titre)) $new->settitre($hash[0]->titre);
        if(isset($hash[0]->description)) $new->setdescription($hash[0]->description);
        if(isset($hash[0]->corps)) $new->setcorps($hash[0]->corps);
        if(isset($hash[0]->user)) $new->setidu($hash[0]->user);
        if(isset($hash[0]->categorie)) $new->setcategories($hash[0]->categorie);
        if(isset($hash[0]->idarticle)) $new->setida($hash[0]->idarticle);
        
        return $new;
    }
}
