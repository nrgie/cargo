<?php

class Router{

	private $routes;
	
	function __construct() {
	    $this->routes = array (
		"home" => "indexPage",
		"users" => "ajax",
		"friends" => "ajax",
		"friendsof" => "ajax",
		"suggest" => "ajax",
		"graph" => "ajax"
	    );
	}
	
	public function lookup($query) {
		if(array_key_exists($query, $this->routes)) {
		    return $this->routes[$query];
		}else{
		    return false;
		}
	}
}
