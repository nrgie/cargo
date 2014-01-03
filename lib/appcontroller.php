<?php

require("database.php");

$db = new database();

require("router.php");
require("entities.php");
require("users.php");

class App {

	private $router;
	public $response;
	
	function __construct() {

	    $this->router = new Router();

	    $page = isset($_GET['page']) ? $_GET['page'] : 'home';

	    if (isset($_GET['query']))
		if (strlen($_GET['query']) > 0) {
		    $queryParams = explode("/", $_GET['query']);
		} else {
		    $queryParams = false;
		}

	    $endpoint = $this->router->lookup($page);
	
	    if ($endpoint === false) {
		header("HTTP/1.0 404 Not Found");
		exit;
	    } else if($endpoint == 'ajax') {
		header("Content-type: application/json; charset=utf-8");
		include(dirname(__FILE__) . "/routes/".$page.".php");
		if(isset($response))
		    return $this->json_response($response);
	    } else {
		if($endpoint)
		    $this->loadPage($page);
	    }
	}
	
	public function jsonp_response($data) {
	    echo $_GET['callback'] .'('. json_encode($data) . ')';
	    exit;
	}
	
	public function json_response($data) {
	    echo json_encode($data);
	    exit;
	}
	
	public function response($data) {
	    if (isset($_GET['callback'])) {
		return $this->jsonp_response($data);
	    } else {
		return $this->json_response($data);
	    }
	}

	private function loadView($view, $data = null) {
	    if(is_array($data)) { extract($data); }
	    require(dirname(dirname(__FILE__)) . "/lib/views/" . $view . ".php");
	}

	private function loadPage($view, $data = null, $flash = false) {
	    $this->loadView("header", $data);
	    $this->loadView($view, $data);
	    $this->loadView("footer");
	}

	public function indexPage($params) {
	    $this->loadPage("home");
	}
}
