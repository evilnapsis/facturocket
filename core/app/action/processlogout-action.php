<?php
/**
* @author evilnapsis
**/
if(isset($_SESSION["user_id"])){
	unset($_SESSION["user_id"]);
}

session_destroy();
Core::redir("./");
?>
