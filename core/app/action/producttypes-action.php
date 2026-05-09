<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="add"){
		$item = new ProductTypeData();
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->add();
		$_SESSION["sweetalert"] = "Registro agregado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir(./?view=producttypes&opt=all);
	}
	else if($opt=="upd"){
		$item = ProductTypeData::getById($_POST["id"]);
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->update();
		$_SESSION["sweetalert"] = "Registro actualizado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir(./?view=producttypes&opt=all);
	}
	else if($opt=="del"){
		$item = ProductTypeData::getById($_GET["id"]);
		$item->del();
		$_SESSION["sweetalert"] = "Registro eliminado.";
		$_SESSION["sweetalert_icon"] = "warning";
		Core::redir(./?view=producttypes&opt=all);
	}
}
?>
