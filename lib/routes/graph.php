<?php

global $db;
$users = $db->_users->find();

$response = new stdClass();
$response->nodes = array();
$response->edges = array();

$temp = array();

foreach ($users as $u) {

    $temp[$u['id']] = $u;
    $nodekey = $u['firstname'] . " " . $u['surname'];
    $node = array("borders"=>count($u['friends']), "length"=>100);
    $response->nodes[$nodekey] = $node;

}

foreach ($users as $u) {
    $key = $u['firstname'] . " " . $u['surname'];
    $friends = array();

    foreach($u['friends'] as $k=>$v) {
	$name = $temp[$v]['firstname'] . " " . $temp[$v]['surname'];
	$friends[$name] = array("border"=>100);
    }

    $response->edges[$key] = $friends;

}

return $response;
