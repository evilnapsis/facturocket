<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="add"){
		$item = new PaymentFormData();
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->add();
		$_SESSION["sweetalert"] = "Registro agregado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir(./?view=paymentforms&opt=all);
	}
	else if($opt=="upd"){
		$item = PaymentFormData::getById($_POST["id"]);
		$item->code = $_POST["code"];
		$item->name = $_POST["name"];
		$item->update();
		$_SESSION["sweetalert"] = "Registro actualizado correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir(./?view=paymentforms&opt=all);
	}
	else if($opt=="del"){
		$item = PaymentFormData::getById($_GET["id"]);
		$item->del();
		$_SESSION["sweetalert"] = "Registro eliminado.";
		$_SESSION["sweetalert_icon"] = "warning";
		Core::redir(./?view=paymentforms&opt=all);
	}
}
?>
