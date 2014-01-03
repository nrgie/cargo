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

/*
    nodes =>
	"firstname surname": {"friends_count": 2, "length": 100},
    edges =>

	"firstname surname": {
	    "firstname surname": {"border": 100}, 
	    "India": {"border": 100}
	},

{
    "nodes": {


"East Timor": {"borders": 2, "length": 228.0}, 
"Afghanistan": {"borders": 7, "length": 5529.0}, 
"Nepal": {"borders": 2, "length": 2926.0}, 
"Bangladesh": {"borders": 2, "length": 4246.0}, 
"Macau": {"borders": 1, "length": 0.34000000000000002}, 
"Brunei": {"borders": 2, "length": 381.0}, 
"Indonesia": {"borders": 4, "length": 2830.0}, 
"India": {"borders": 9, "length": 14103.0}, 
"Cambodia": {"borders": 3, "length": 2572.0}, 
"North Korea": {"borders": 3, "length": 1673.0}, 
"Malaysia": {"borders": 4, "length": 3147.0}, 
"People's Republic of China": {"borders": 19, "length": 22147.0}, 
"Burma": {"borders": 5, "length": 5876.0}, 
"Mongolia": {"borders": 2, "length": 8220.0}, 
"Vietnam": {"borders": 3, "length": 4639.0}, 
"Laos": {"borders": 5, "length": 5083.0}, 
"Thailand": {"borders": 4, "length": 4863.0}, 
"South Korea": {"borders": 1, "length": 238.0}, 
"Hong Kong": {"borders": 1, "length": 30.0}}, 

"edges": {
    "East Timor": {"Indonesia": {"border": 228}}, 
    "Afghanistan": {
	"People's Republic of China": {"border": 76}, 
	"India": {"border": 106}}, 
    "Nepal": {
	"People's Republic of China": {"border": 236}, 
	"India": {"border": 690}
    }, 
    "Bangladesh": {
	"Burma": {"border": 193}, 
	"India": {"border": 53}}, 
    "Macau": {"People's Republic of China": {"border": 34}}, 
    "Brunei": {"Malaysia": {"border": 381}}, 
    "Indonesia": {
	"East Timor": {"border": 228}, 
	"Malaysia": {"border": 782}}, 
    "India": {
	"Burma": {"border": 463}, 
	"Afghanistan": {"border": 106}, 
	"People's Republic of China": {"border": 380}, 
	"Nepal": {"border": 690}, 
	"Bangladesh": {"border": 53}}, 
    "Cambodia": {
	"Thailand": {"border": 803}, 
	"Vietnam": {"border": 228}, 
	"Laos": {"border": 541}}, 
    "North Korea": {
	"South Korea": {"border": 238}, 
	"People's Republic of China": {"border": 416}}, 
    "Malaysia": {
	"Brunei": {"border": 381}, 
	"Indonesia": {"border": 782}, 
	"Thailand": {"border": 506}}, "People's Republic of China": {"Burma": {"border": 185}, "Afghanistan": {"border": 76}, "Macau": {"border": 34}, "Nepal": {"border": 236}, "India": {"border": 380}, "Mongolia": {"border": 677}, "North Korea": {"border": 416}, "Vietnam": {"border": 281}, "Laos": {"border": 423}, "Hong Kong": {"border": 30}}, "Burma": {"Thailand": {"border": 800}, "People's Republic of China": {"border": 185}, "India": {"border": 463}, "Bangladesh": {"border": 193}, "Laos": {"border": 235}}, "Mongolia": {"People's Republic of China": {"border": 677}}, "Vietnam": {"People's Republic of China": {"border": 281}, "Cambodia": {"border": 228}, "Laos": {"border": 130}}, "Laos": {"Burma": {"border": 235}, "Thailand": {"border": 754}, "People's Republic of China": {"border": 423}, "Vietnam": {"border": 130}, "Cambodia": {"border": 541}}, "Thailand": {"Burma": {"border": 800}, "Malaysia": {"border": 506}, "Cambodia": {"border": 803}, "Laos": {"border": 754}}, "South Korea": {"North Korea": {"border": 238}}, "Hong Kong": {"People's Republic of China": {"border": 30}}}}






*/