<?php


$data = file_get_contents(dirname(dirname(__FILE__)) . '/data/data.json');
$data = json_decode($data);

$refs = array();

foreach($data as $d) {

    $user = new User();
    $user->write_lock();
    $user->id = $d->id;
    $user->firstname = $d->firstName;
    $user->surname = $d->surname;
    $user->age = $d->age;
    $user->gender = $d->gender;
    $user->friends = $d->friends;
    $user->write_unlock();

    $refs[$d->id] = $user->getID();


}


/* create document relationship referencies with the help of MongoId. */


$cursor = $db->_users->find();

foreach($cursor as $i) {

    $friends = $i['friends'];

    $map = array();

    if(!empty($friends))
    foreach($friends as $fid) {
        $friendReference = new MongoId($refs[$fid]);
        $map[] = $friendReference;
    }

    $db->_users->update(array('_id'=>$i['_id']), array('$set'=>array('relationships'=>$map)));

}


