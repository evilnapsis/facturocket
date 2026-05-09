<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="add"){
		$item = new ClientData();
		$item->rfc = $_POST["rfc"];
		$item->name = $_POST["name"];
		$item->email = $_POST["email"];
		$item->address = $_POST["address"];
		$item->zip_code = $_POST["zip_code"];
		$item->tax_regime_id = $_POST["tax_regime_id"];
		$item->cfdi_use_id = $_POST["cfdi_use_id"];
		$item->add();
		$_SESSION["sweetalert"] = "Cliente agregado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=clients&opt=all");
	}
	else if($opt=="upd"){
		$item = ClientData::getById($_POST["id"]);
		$item->rfc = $_POST["rfc"];
		$item->name = $_POST["name"];
		$item->email = $_POST["email"];
		$item->address = $_POST["address"];
		$item->zip_code = $_POST["zip_code"];
		$item->tax_regime_id = $_POST["tax_regime_id"];
		$item->cfdi_use_id = $_POST["cfdi_use_id"];
		$item->update();
		$_SESSION["sweetalert"] = "Cliente actualizado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=clients&opt=all");
	}
	else if($opt=="del"){
		$item = ClientData::getById($_GET["id"]);
		// Check if has invoices before deleting? For now simple delete.
		$item->del(); // I need to add del() to ClientData if it's missing.
		$_SESSION["sweetalert"] = "Cliente eliminado.";
		$_SESSION["sweetalert_icon"] = "warning";
		Core::redir("./?view=clients&opt=all");
	}
}
?>
