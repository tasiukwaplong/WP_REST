<?php
require 'WP_REST.php';
$WP_REST = new Tasiukwaplong\WP_REST\WP_REST(DB_NAME, TABLE_PREFIX, DB_USERNAME, DB_PASSWORD, DB_HOST);

$CALL_METHOD =  "asJSON";//asArray
$id = (isset($_GET['id'])) ? htmlspecialchars($_GET['id']) : '' ;
$call = (isset($_GET['call'])) ? htmlspecialchars($_GET['call']) : '' ;

switch ($call) {
	case 'getAllPosts':
		die($WP_REST->getAllPosts($CALL_METHOD));
		break;
	case 'getPost':
		die($WP_REST->getPost($id, $CALL_METHOD));
		break;	
	default:
		die(json_encode(["data"=> ["errorExist"=>true, "body"=>"Incorrect API call"]], true));
		break;
}