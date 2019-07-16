<?php
ini_set('memory_limit', -1);
require_once 'testInit.php';
$wf = new \inkpro\wannafind\Wannafind();

$day = new \DateInterval("P4Y");
$now = new \DateTime();


$wf->setFields("Order",array("Id","Status","OrderComment","DateDelivered"));

$start_string = "2018-01-01";
$start_date = new \DateTime($start_string);
$end_date = clone($start_date);
$end_date->add(new \DateInterval("P2Y"))->sub(new \DateInterval("P1D"));
$orders = $wf->getOrders($start_date, $end_date, [8]);
$orders;
$percent = 100/count($orders);
$i = 1;
foreach($orders as $order){
    $now = new \DateTime();
    $order->Status = 3;
    $wf->updateOrderStatus($order);
    $order->OrderComment .= "\n\nOpdateret til Afsendt status ".$now->format("Y-m-d H:i:s")." af Esben (automatisk)";
    $wf->updateOrderComment($order);
    echo "Opdateret ordre ".$order->Id." fra ".$order->DateDelivered." - ".round($i*$percent, 2)."%\n";
    $i++;
}
echo "Færdig!\n";
// $order->Status = 3;
// $wf->updateOrderStatus($order);
// $order->OrderComment .= "\n\nOpdateret til Afsendt status ".$now->format("Y-m-d H:i:s")." af Esben (automatisk)";
// $wf->updateOrderComment($order);
// $order;
// foreach($orders as $order){

// }
// $orders;