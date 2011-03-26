<?php

namespace Application\CalendarBundle\Event;

use Application\CalendarBundle\Event\EventManager;

/**
 * EventRequest
 **/
class EventRequest
{
    protected $title;
    protected $from;
    protected $to;
    protected $location;
    protected $refgrp;
    protected $id;

    function __construct()
    {
    }

    //FIXME reflection? hasMethod() ? metametameta
    static public function fromHash($hash)
    {
        $new = new EventRequest();

        if(isset($hash['title'])) $new->setTitle($hash['title']);
        if(isset($hash['from'])) $new->setFrom($hash['from']);
        if(isset($hash['to'])) $new->setTo($hash['to']);
        if(isset($hash['location'])) $new->setLocation($hash['location']);
        if(isset($hash['refgrp'])) $new->setRefgrp($hash['refgrp']);
        if(isset($hash['id'])) $new->setId($hash['id']);

        return $new;
    }

    public function toHash()
    {
        $hash = array(
                      "id" => $this->getId(),
                      "refgrp" => $this->getRefgrp(),
                      "title" => $this->getTitle(),
                      "from" => $this->getFrom(),
                      "to" => $this->getTo(),
                      "location" => $this->getLocation()
                      );

        return $hash;
    }

    public function getRefgrp()
    {
        return $this->refgrp;
    }

    public function setRefgrp($refgrp)
    {
        $this->refgrp = $refgrp;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }
}
