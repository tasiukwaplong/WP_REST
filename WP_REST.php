<?php
/**
 * Creator: Tasiu kwaplong
 * Email: tasiukwaplong@gmail.com
 */

namespace Tasiukwaplong\WP_REST;

use Exception;
use Mysqli;

class WP_REST{
	public $resp = ["data"=>[]];
	public $tblPrefix;
	public $DB;


	function __construct($dbName, $tblPrefix, $dbUser, $dbPsw, $dbHost){
    	$this->tblPrefix = $tblPrefix;
   
    	$this->connectSQLDB($dbHost, $dbUser, $dbPsw, $dbName);	
    }

    function connectSQLDB($dbHost, $dbUser, $dbPsw, $dbName){
    	$this->DB = new mysqli($dbHost, $dbUser, $dbPsw, $dbName);

		if ($this->DB->error) {
			$this->setError("SQl connection not possible. #".$this->DB->errno, "asArray");
		}
    }

    function setError($msg, $callMethod){
        $this->resp = ["data"=> ["errorExist"=>true, "error"=>$msg]];
        return $this->msgBody($callMethod);
    }

    function setMsg($msg, $callMethod = "asArray"){
        $this->resp = ["data"=> ["errorExist"=>false, "body"=>$msg]];
        return $this->msgBody($callMethod);
    }

    function msgBody($callMethod){
    	//default is asArray, set JSOn as asJSON
    	return ($callMethod == "asArray") ? $this->resp : json_encode($this->resp, true);
    }

    function getAllPosts($callMethod = "asArray"){
    	$tbl = $this->tblPrefix."_posts";
    	$queryGetPost = $this->DB->query("SELECT ID, post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'post' ORDER BY ID DESC");
    	$allPosts = [];
    	
    	for ($i=0; $i < $queryGetPost->num_rows ; $i++) { 
    		//get all posts in a single array
    		$post = $queryGetPost->fetch_assoc();
    		$post["imageLink"] = $this->getFeaturedImage($post['ID']);
    		$allPosts[$i] = $post;
    	}
    	
    	$this->DB->close();
    	// $this->resp =  $allPosts;
    	return $this->setMsg($allPosts, $callMethod);
    }

    function getPost($id, $callMethod = "asArray"){
    	//get a particular post
    	if (!isset($id) || empty($id) ) {
    		# no post id supplied or empty post id
    		return $this->setError("There was a problem loading post content. Please confirm if it still exists", $callMethod);
    	}else{
    		$id = htmlspecialchars($id);
    	}
    	$tbl = $this->tblPrefix."_posts";

    	$queryGetPost = $this->DB->query("SELECT ID, post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'post' AND ID = $id");
    	// $this->DB->close();    	
    	$post = $queryGetPost->fetch_assoc();
        $post["imageLink"] = $this->getFeaturedImage($post['ID']);
        $this->DB->close();     

        return ($queryGetPost->num_rows == 1) ? $this->setMsg($post, $callMethod) : $this->setError("Sorry, this post does not exist", $callMethod);
    }



    function getAllPages($callMethod = "asArray"){
    	$tbl = $this->tblPrefix."_posts";
    	$queryGetPages = $this->DB->query("SELECT ID, post_name AS post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'page' ORDER BY ID DESC");
    	$allages = [];
    	
    	for ($i=0; $i < $queryGetPages->num_rows ; $i++) { 
    		//get all posts in a single array
    		$page = $queryGetPages->fetch_assoc();
    		$allages[$i] = $page;
    	}
    	
    	$this->DB->close();
    	// $this->resp =  $allages;
    	return $this->setMsg($allages, $callMethod);
    }

    function getPage($id_or_name, $callMethod = "asArray"){
    	//get a particular post
    	if (!isset($id_or_name) || empty($id_or_name) ) {
    		# no post id supplied or empty post id
    		return $this->setError("There was a problem loading post content. Please confirm if it still exists", $callMethod);
    	}else{
    		$id_or_name = htmlspecialchars($id_or_name);
    	}
    	$tbl = $this->tblPrefix."_posts";

    	$queryGetPost = $this->DB->query("SELECT ID, post_name AS post_title, post_content FROM $tbl WHERE post_status = 'publish' AND post_type = 'page' AND (ID = '$id_or_name' OR post_name = '$id_or_name')");
    	$this->DB->close();    	
    	return ($queryGetPost->num_rows == 1) ? $this->setMsg($queryGetPost->fetch_assoc(), $callMethod) : $this->setError("Sorry, this page does not exist", $callMethod);
    }



   function getFeaturedImage($id){
    	//get a particular post
    	if (!isset($id) || empty($id) ) {
    		# no post id supplied or empty post id
    		return false;
    	}else{
    		$id = htmlspecialchars($id);
    	}

    	$postmeta = $this->tblPrefix."_postmeta";

    	$queryGetPost = $this->DB->query("SELECT meta_value AS imageLink FROM $postmeta WHERE meta_key = '_wp_attached_file' AND  post_id in (SELECT meta_value FROM $postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $id)");
    	return $queryGetPost->fetch_assoc()['imageLink'];//wp-content/uploads
    }

}