<?php

class User extends Entity {

	protected function initialise_attributes() {
		parent::initialise_attributes();
	}

	function __construct($_id = NULL) {
		$this->initialise_attributes();

		if (!empty($_id)) {

		    if ($_id instanceof stdClass) {
			if (!$this->load($_id->_id)) {
			    throw new Exception('IOException:FailedToLoadID');
			}
		    } else if ($_id instanceof User) {
			    foreach ($_id->attributes as $key => $value) {
				$this->attributes[$key] = $value;
			    }
		    } else if ($_id instanceof Entity) {
			throw new Exception('InvalidParameterException:NonUser Object');
		    } else if (is_string($_id)) {
			$mongoid = new MongoId($_id);
			if (!$this->load($mongoid)) {
			    throw new Exception('IOException:FailedToLoadID');
			}
		    } else if ($_id instanceof MongoId) {
			if (!$this->load($_id)) {
			    throw new Exception('IOException:FailedToLoadID');
			}
		    } else {
			throw new Exception('InvalidParameterException:UnrecognisedValue');
		    }
		    
		}
	}
	
	protected function load($_id) {
		if (!parent::load($_id)) { return false; }
		return true;
	}

	public function save() {
		if (!parent::save()) { return false; }
		return create_user_entity($this->_id);
	}

	public function delete() {
		return parent::delete();
	}

	public function toArray() {
	        return get_object_vars($this);
	}

	function addFriend($friend_id) {}

	function removeFriend($friend_id) {}

	function isFriend() {}

	function isFriendsWith($user_id) {}

	function isFriendOf($user_id) {}

	function getFriends($limit = 10, $offset = 0) {}

	function getFriendsOf($limit = 10, $offset = 0) {}

}

function get_user_entity_as_row($_id) {
	return get_entity($_id);
}


function create_user_entity($_id) {
	global $db;

	$time = time();
	$row = get_entity_as_row($_id);

	if ($row) {
	    $db->_users->update(array('_id' => $_id), array('$set' => array('last_action'=>$time)));
	    $entity = get_entity($_id);
	    return $entity;
	}

	return false;
}


function get_users() {
	global $db;
	$list = array();

	if($db)
	    $users = $db->_users->find();
	
	if($users)
	    foreach($users as $u) {
		$list[] = $u;
	    }

	return $list;
}

function get_suggestions($id) {
	global $db;

	$list = array();
	$user = $db->_users->findOne(array("id"=> (int) $id));

	if($user) {
	    $search = array(
		"id" => array( '$ne' => $user['id'] ),
		"friends" => array( '$in' => $user['friends'], '$nin' => array($user['id']) )
	    );
	    $cursor = $db->_users->find($search);

	    foreach($cursor as $u) {

		$i=0;

		foreach($user['friends'] as $k => $v) {
		    if(in_array($v, $u['friends'])) { $i++; }
		    if($i == 2) {
			if(!array_key_exists($list[$u['id']])) {
			    $list[$u['id']] = $u;
			    break;
			}
		    }
		}
	    }
	} else {
	    echo "user not found";
	}
	return $list;
}

function user_add_friend($user_id, $friend_id) {}

function user_remove_friend($user_id, $friend_id) {}

function user_is_friend($user_id, $friend_id) {}

function get_user_friends($user_id, $limit = 10, $offset = 0) {}

function get_user_friends_of($user_id, $limit = 10, $offset = 0) {}
