<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="add"){
		$item = new ProductData();
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->description = $_POST["description"];
		$item->price = $_POST["price"];
		$item->category_id = $_POST["category_id"];
		$item->product_type_id = $_POST["product_type_id"];
		$item->unit_id = $_POST["unit_id"];
		$item->tax_id = $_POST["tax_id"];
		$item->add();
		$_SESSION["sweetalert"] = "Concepto agregado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=products&opt=all");
	}
	else if($opt=="upd"){
		$item = ProductData::getById($_POST["id"]);
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->description = $_POST["description"];
		$item->price = $_POST["price"];
		$item->category_id = $_POST["category_id"];
		$item->product_type_id = $_POST["product_type_id"];
		$item->unit_id = $_POST["unit_id"];
		$item->tax_id = $_POST["tax_id"];
		$item->update();
		$_SESSION["sweetalert"] = "Concepto actualizado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=products&opt=all");
	}
	else if($opt=="del"){
		$item = ProductData::getById($_GET["id"]);
		$item->del();
		$_SESSION["sweetalert"] = "Concepto eliminado.";
		$_SESSION["sweetalert_icon"] = "warning";
		Core::redir("./?view=products&opt=all");
	}
}
?>
