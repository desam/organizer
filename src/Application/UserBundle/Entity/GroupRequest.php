<?php
namespace Application\UserBundle\Entity;

class GroupRequest
{
	/**
     * @validation:NotBlank
     */
    protected $name;

	/**
     * @validation:NotBlank
     */
    protected $description;
    
    /**
     * @validation:NotBlank
    */
    public $emails = array();
    
    