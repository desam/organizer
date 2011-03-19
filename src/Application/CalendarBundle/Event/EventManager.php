<?php
namespace Application\CalendarBundle\Event;

use Application\CalendarBundle\Entity\eXist;

/**
 * EventManager
 * Will regroup every query related to event manipulation
 **/

//TODO Refactor/encapsulate $db access..
//superclass eXistManager, with ->query() ?
//container symfony ?
class EventManager
{
    static public function getByGroupAndDate($group, $start, $end)
    {
	$db = new eXist();
	$db->connect() or die($db->getError());

	$query = '
            <results>
            { 
                for $e in document("orga/events.xml")//event[
                    @refgrp = "'. $group .'"
                    and from >= "'. $start .'"
                    and to <= "'. $end .'"]
                return 
                <event>
                    {$e/@*}
                    <title>{replace($e/title, \'"\', \'\\\"\')}</title>
                    {$e/from}
                    {$e/to}
                    <location>{replace($e/location, \'"\', \'\\\"\')}</location>
                </event>
            }
            </results>
        ';
            
	$result = $db->xquery($query) or die($db->getError());
	// $db->disconnect() or die($db->getError());

	return $result['XML'];
    }

    static public function getByID($id)
    {
	$db = new eXist();
	$db->connect() or die($db->getError());

	$query = '
            <results>
            { 
                for $e in document("orga/events.xml")//event[@id = '. $id .']
                return $e
            }
            </results>
        ';
            
	$result = $db->xquery($query) or die($db->getError());
	// $db->disconnect() or die($db->getError());

	return $result['XML'];
    }
    
    static public function insert($event)
    {
	$db = new eXist();
	$db->connect() or die($db->getError());

	$query = '
            update insert '. $event .'
            into document("orga/events.xml")/events
            ';

	$result = $db->xquery($query) or die($db->getError());
	$db->disconnect() or die($db->getError());

	return $result['XML'];
    }

    static public function deleteByID($id)
    {
	$db = new eXist();
	$db->connect() or die($db->getError());

	$query = '
            update delete document("orga/events.xml")//event[@id = "'. $id .'"]
            ';

	$result = $db->xquery($query) or
	    (preg_match('/No data found/', $db->getError()) or
	     die($db->getError()));
	/* $db->disconnect() or die($db->getError()); */

	return $result['XML'];
    }

    static public function updateByID($id, $new)
    {
	$db = new eXist();
	$db->connect() or die($db->getError());

	$query = '
            update replace document("orga/events.xml")//event[@id = "'. $id .'"]
            with '. $new .'
            ';

	$result = $db->xquery($query) or
	    (preg_match('/No data found/', $db->getError()) or
	     die($db->getError()));

	/* $db->disconnect() or die($db->getError()); */

	return $result['XML'];
    }

    static public function toJSON($data)
    {
	//loading XML docs
	$xml = simplexml_load_string($data);
	$xsl = simplexml_load_file(dirname(__FILE__).
				   '/../Resources/views/xsl/calendar.xsl');

	//transforming
	$processor = new \XSLTProcessor();
	$processor->importStyleSheet($xsl);

	return $processor->transformToXML($xml);
    }

    //TODO make it recursive ?
    //FIXME second parameter defines the attributes to look for ?
    static public function hash2xml($hash)
    {
	$children = '';
	$attributes = '';

	//building attributes and children
	foreach ($hash as $k => $v) {
	    if (!isset($v)) continue;

	    if ($k === 'id' or $k === 'refgrp')
		$attributes .= "$k=\"$v\" ";
	    else
		$children .= "\n  <$k>$v</$k>";
	}

	$xml  = "<event $attributes>";
	$xml .= $children . "\n";
	$xml .= '</event>';

	return $xml;
    }

    //TODO make it recursive ?
    static public function xml2hash($xml)
    {
	$event = simplexml_load_string($xml)->event[0];

	$hash = array();
	if (!isset($event)) return $hash;
	
	foreach($event->attributes() as $k => $v) {
	    $hash[$k] = (string)$v;
	}

	foreach($event->children() as $k => $v) {
	    $hash[$k] = (string)$v;
	}

	return $hash;
    }
}
