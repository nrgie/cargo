<?php

class database {

	private $mongo_url = "mongodb://localhost";
	private $connection;
	private $database = "cargomedia";
	private $users_collection = "users";
	public $_cache;
	public $_users;

        public function __construct() {

		try {
			$this->connection = new MongoClient();
		} catch (Exception $e) {
			echo "Database connection failed";
			exit();
		}
		$database = $this->database;
		$users_coll = $this->users_collection;
		$this->_users = $this->connection->$database->$users_coll;

        }
}

