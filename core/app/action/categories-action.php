<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="add"){
		$item = new CategoryData();
		$item->name = $_POST["name"];
		$item->add();
		$_SESSION["sweetalert"] = "Categoría agregada correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=categories&opt=all");
	}
	else if($opt=="upd"){
		$item = CategoryData::getById($_POST["id"]);
		$item->name = $_POST["name"];
		$item->update();
		$_SESSION["sweetalert"] = "Categoría actualizada correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=categories&opt=all");
	}
	else if($opt=="del"){
		$item = CategoryData::getById($_GET["id"]);
		$item->del();
		$_SESSION["sweetalert"] = "Categoría eliminada.";
		$_SESSION["sweetalert_icon"] = "warning";
		Core::redir("./?view=categories&opt=all");
	}
}
?>
