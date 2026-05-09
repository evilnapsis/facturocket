<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="add"){
		$item = new TaxObjectData();
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->add();
		$_SESSION["sweetalert"] = "Registro agregado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir(./?view=taxobjects&opt=all);
	}
	else if($opt=="upd"){
		$item = TaxObjectData::getById($_POST["id"]);
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->update();
		$_SESSION["sweetalert"] = "Registro actualizado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir(./?view=taxobjects&opt=all);
	}
	else if($opt=="del"){
		$item = TaxObjectData::getById($_GET["id"]);
		$item->del();
		$_SESSION["sweetalert"] = "Registro eliminado.";
		$_SESSION["sweetalert_icon"] = "warning";
		Core::redir(./?view=taxobjects&opt=all);
	}
}
?>
