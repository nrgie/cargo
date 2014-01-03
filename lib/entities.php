<?php

$ENTITY_CACHE = NULL;

abstract class Entity {

	protected $attributes;
	protected $temp_metadata;
	
	protected $write_lock = false;

	private $_id = false;

	protected function initialise_attributes() {

		if (!is_array($this->attributes)) { $this->attributes = array(); }
		if (!is_array($this->temp_metadata)) { $this->temp_metadata = array(); }

		$this->attributes['type'] = false;
		$this->attributes['time_created'] = "";
		$this->attributes['time_updated'] = "";
	}

	public function get($key) {
		if (isset($this->attributes[$key])) {
		        return $this->attributes[$key];
		}
		return false;
	}

	
	public function set($key, $value) {
		$this->attributes[$key] = $value;
		return $this->setMetaData($key, $value);
	}

	public function dump($key, $value) {
		$this->attributes[$key] = $value;
	}
    
	public function getall(){
		return $this->attributes;
	}

	
	public function getMetaData($key) {
	    return $this->attributes[$key];
	}

	function __get($key) {
		return $this->get($key);
	}
	function __set($key, $value) {
		return $this->set($key, $value);
	}
	function __isset($key) {
		return $this->$key !== NULL;
	}
	function __unset($key) {
	    /*  TODO : write me  */
	    return true;
	}

	public function setMetaData($key, $value) {
	    global $db;
	    if(!$db) { throw new Exception('IOException:Database Object not initialised'); }

	    if ($key instanceof MongoId) { throw new Exception('Exception: _id attibute cannot be overwrited'); }

	    // check that entity object is saved into the database ? if not save it now to have unique ID.

	    if($this->_id === false) {
		$_id = $this->save();
	    } else {
		$_id = $this->_id;
	    }
	
	    if($this->write_lock){
		$this->temp_metadata[$key] = $value;
		return true;
	    } else {
	        $match = array("_id" => $_id);
	        $data = array('$set' => array($key => $value));
	        $result = $db->_users->update($match, $data);
	        return $result;
	    }
	}

	public function write_lock() {
	    $this->write_lock = true;
	}

	public function write_unlock() {
	    $this->write_lock = false;
	    $this->flush_temp_metadata();
	    $this->save();
	}

	public function getID() { return $this->_id; }

	public function save() {
		$_id = $this->_id;
		if ($_id !== false && $_id instanceof MongoId) {
		    return update_entity( $this->_id );
		} else {
		    $skel_id = create_entity();
		    $this->_id = $skel_id;

		    if (!$this->_id) {
			throw new Exception('IOException:BaseEntitySaveFailed');
		    }

		    return $this->_id;
		}
	}

	private function flush_temp_metadata() {
	    if (sizeof($this->temp_metadata) > 0) {
		foreach($this->temp_metadata as $name => $value) {
		    $this->$name = $value;
		    unset($this->temp_metadata[$name]);
	        }
	    }
	}


	protected function load($_id) {
	    $row = get_entity_as_row($_id);

	    if ($row) {
		if (!is_array($this->attributes)) {
			$this->attributes = array();
		}

		$objarray = (array) $row;
		foreach($objarray as $key => $value) {
			$this->attributes[$key] = $value;
		}
		return true;
	    }
	    return false;
	}

	private $valid = FALSE;

	function rewind() {
		$this->valid = (FALSE !== reset($this->attributes));
	}

	function current() {
		return current($this->attributes);
	}

	function key() {
		return key($this->attributes);
	}

	function next() {
		$this->valid = (FALSE !== next($this->attributes));
	}

	function valid() {
		return $this->valid;
	}


	function offsetSet($key, $value) {
		if ( array_key_exists($key, $this->attributes) ) {
			$this->attributes[$key] = $value;
		}
	}

	function offsetGet($key) {
		if ( array_key_exists($key, $this->attributes) ) {
			return $this->attributes[$key];
		}
	}

	function offsetUnset($key) {
		if ( array_key_exists($key, $this->attributes) ) {
			$this->attributes[$key] = ""; 
		}
	}

	function offsetExists($offset) {
		return array_key_exists($offset, $this->attributes);
	}
}


function update_entity($_id) {
	global $db;
	if(!$db) { throw new Exception('IOException:Database Object not initialised'); }

	$result = $db->_users->update(array('_id'=>"$_id"), array('$set'=>array('time_updated'=>time())));
	if ($result===false) {
	    return false;
	}
	return true;
}


function create_entity($type="user") {
	global $db;
	if(!$db) { throw new Exception('IOException:Database Object not initialised'); }

	$type = (string) $type;
	$time = time();
	
	$insert = array (
	    'type'=> (string) $type,
	    'time_created'=> (int) $time,
	    'time_updated'=> (int) $time,
	);
	
	$db->_users->insert($insert);
	
	return (object) $insert['_id'];
}


function get_entity_as_row($_id) {
	global $db;
	if (!$_id) {
	    return false;
	}

	$entity = $db->_users->findOne(array("_id" => $_id));
	return (object) $entity;
}


function entity_row_to_bbstar($row) {
	if (!($row instanceof stdClass)) { return $row; }
	if (!isset($row->_id)) { return $row; }

	/* create user object */
	$new_entity = new User($row);
	return $new_entity;
}


function get_entity($_id) {
	return entity_row_to_bbstar(get_entity_as_row($_id));
}



