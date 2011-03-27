<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('hello', new Route('/helloo/{id}', array(
    '_controller' => 'HelloBundle:Hello:signup',
)));

return $collection;
