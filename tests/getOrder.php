<?php
require_once 'testInit.php';
$wf = new \inkpro\wannafind\Wannafind("INKSE");

$wf->setFields("Order",array("Id","Delivery"));
$order = $wf->getOrder(25);
$order;