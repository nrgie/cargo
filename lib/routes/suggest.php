<?php

$response = array(
    "id" => $_GET["userid"],
    "data" => get_suggestions($_GET["userid"])
);

