<?php

namespace Application\CalendarBundle\Event;

/**
 * EventRequest
 **/
class EventRequest
{
    protected $title;
    protected $from;
    protected $to;
    protected $location;

    function __construct()
    {
    }

    static public function fromXML($xml)
    {
        // code...
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

    public function create()
    {
        echo 'LOL';
    }
}
