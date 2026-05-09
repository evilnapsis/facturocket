<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"])){
	$opt = $_GET["opt"];

	if($opt=="addtocart"){
        if(!isset($_POST["product_id"]) || $_POST["product_id"] == ""){
            $_SESSION["sweetalert"] = "Por favor selecciona un producto.";
            $_SESSION["sweetalert_icon"] = "warning";
            Core::redir("./?view=newinvoice");
            exit;
        }
		$product = ProductData::getById($_POST["product_id"]);
		$quantity = $_POST["quantity"];
		
		$cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : [];
		
		$found = false;
		foreach($cart as &$c){
			if($c["product_id"] == $product->id){
				$c["quantity"] += $quantity;
				$found = true;
				break;
			}
		}

		if(!$found){
			$cart[] = [
				"product_id" => $product->id,
				"quantity" => $quantity,
				"price" => $product->price,
				"tax_object_id" => 2 // Default: Sí objeto de impuesto (IVA 16% assumed for simplicity or can be customized)
			];
		}

		$_SESSION["cart"] = $cart;
		Core::redir("./?view=newinvoice");
	}
	else if($opt=="remfromcart"){
		$cart = $_SESSION["cart"];
		$newcart = [];
		foreach($cart as $index => $c){
			if($index != $_GET["index"]){
				$newcart[] = $c;
			}
		}
		$_SESSION["cart"] = $newcart;
		Core::redir("./?view=newinvoice");
	}
	else if($opt=="clear"){
		unset($_SESSION["cart"]);
		Core::redir("./?view=newinvoice");
	}
	else if($opt=="save"){
		if(!isset($_SESSION["cart"]) || count($_SESSION["cart"])==0){
			$_SESSION["sweetalert"] = "El carrito está vacío.";
			$_SESSION["sweetalert_icon"] = "error";
			Core::redir("./?view=newinvoice");
			exit;
		}

		$invoice = new InvoiceData();
		$invoice->client_id = $_POST["client_id"];
		$invoice->cfdi_use_id = $_POST["cfdi_use_id"];
		$invoice->payment_form_id = $_POST["payment_form_id"];
		$invoice->payment_method_id = $_POST["payment_method_id"];
		$invoice->currency = $_POST["currency"];
		$invoice->subtotal = $_POST["subtotal"];
		$invoice->discount = $_POST["discount"];
		$invoice->total = $_POST["total"];
		$invoice->type = $_POST["type"];
		$invoice->user_id = $_SESSION["user_id"];
		
        // Obtener siguiente folio y serie dinámicamente
        $seq = FolioSequenceData::getNext($invoice->type);
        $invoice->serie = $seq["serie"];
		$invoice->folio = $seq["folio"];

		$invoice->uuid = "";
		$invoice->xml_path = "";
		$invoice->pdf_path = "";
		
		$res = $invoice->add();
		
		if($res[0]==false){
			$_SESSION["sweetalert"] = "Error al guardar la cabecera de la factura.";
			$_SESSION["sweetalert_icon"] = "error";
			Core::redir("./?view=newinvoice");
			exit;
		}

		$invoice_id = $res[1];

        // Guardar CFDI Relacionados
        if(isset($_POST["relation_type_id"]) && $_POST["relation_type_id"] != "" && $_POST["related_uuid"] != ""){
            $uuids = explode(",", $_POST["related_uuid"]);
            foreach($uuids as $u){
                $rel = new InvoiceRelationData();
                $rel->invoice_id = $invoice_id;
                $rel->relation_type_id = $_POST["relation_type_id"];
                $rel->related_uuid = trim($u);
                $rel->add();
            }
        }

		foreach($_SESSION["cart"] as $c){
			$p = ProductData::getById($c["product_id"]);
			$item = new InvoiceItemData();
			$item->invoice_id = $invoice_id;
			$item->product_id = $c["product_id"];
			$item->description = $p->name;
			$item->quantity = $c["quantity"];
			$item->price = $c["price"];
			$item->total = $c["price"] * $c["quantity"];
			$item->tax_object_id = $c["tax_object_id"];
			$res_item = $item->add();
			
			if($res_item[0]==false){
				$_SESSION["sweetalert"] = "Error al guardar el concepto: ".$p->name;
				$_SESSION["sweetalert_icon"] = "error";
				Core::redir("./?view=newinvoice");
				exit;
			}

			$item_id = $res_item[1];

			// Impuesto dinámico basado en el producto
			$tax = new InvoiceTaxData();
			$tax->invoice_item_id = $item_id;
			$tax->tax_id = $p->tax_id; 
			$tax->base = $item->total;
			$tax->rate = 0.16; // Default rate, could be specialized later if needed
			$tax->amount = $item->total * $tax->rate;
			$tax->add();
		}

		unset($_SESSION["cart"]);
		$_SESSION["sweetalert"] = "Factura creada correctamente.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=invoices&opt=all");
	}
	else if($opt=="generate_xml"){
		$invoice = InvoiceData::getById($_GET["id"]);
		
		// Usar el motor de CFDI para generar el XML real
		$creator = CFDIEngine::createXML($invoice->id);
		
		$filename = "CFDI_".$invoice->serie.$invoice->folio."_".time().".xml";
		$path = "storage/invoices/xml/".$filename;
		
		if(!file_exists("storage/invoices/xml/")){
			mkdir("storage/invoices/xml/", 0777, true);
		}

		// Por ahora guardamos el XML sin sellar (o sellado si tenemos CSD)
		$creator->saveXml($path);

		$invoice->xml_path = $path;
		// UUID simulado hasta que conectemos con el PAC
		$invoice->uuid = strtoupper(bin2hex(random_bytes(4))."-".bin2hex(random_bytes(2))."-".bin2hex(random_bytes(2))."-".bin2hex(random_bytes(2))."-".bin2hex(random_bytes(6)));
		$invoice->status = 2; // Signed
		
		$sql = "update invoice set xml_path=\"$invoice->xml_path\", uuid=\"$invoice->uuid\", status=$invoice->status where id=$invoice->id";
		Executor::doit($sql);

		$_SESSION["sweetalert"] = "XML generado con estructura CFDI 4.0.";
		$_SESSION["sweetalert_icon"] = "success";
		Core::redir("./?view=invoicedetail&id=".$invoice->id);
	}
	else if($opt=="download_xml"){
		$invoice = InvoiceData::getById($_GET["id"]);
		if($invoice->xml_path != "" && file_exists($invoice->xml_path)){
			header('Content-Type: application/xml');
			header('Content-Disposition: attachment; filename="'.basename($invoice->xml_path).'"');
			readfile($invoice->xml_path);
			exit;
		}else{
			$_SESSION["sweetalert"] = "El archivo XML no existe. Genérelo primero.";
			$_SESSION["sweetalert_icon"] = "warning";
			Core::redir("./?view=invoices&opt=all");
		}
	}
    else if($opt=="stamp_xml"){
        $invoice = InvoiceData::getById($_GET["id"]);
        if ($invoice->xml_path != "" && file_exists($invoice->xml_path)) {
            $xml_content = file_get_contents($invoice->xml_path);
            $res = FacturamaEngine::stamp($xml_content);

            if ($res["success"]) {
                // Guardamos el XML timbrado sobre el anterior o uno nuevo
                $stamped_path = "storage/invoices/xml/CFDI_" . $invoice->serie . $invoice->folio . "_" . time() . "_stamped.xml";
                file_put_contents($stamped_path, $res["xml"]);

                // Actualizamos la base de datos
                $invoice->xml_path = $stamped_path;
                $invoice->uuid = $res["uuid"];
                $invoice->status = 3; // Marcamos como Timbrada Oficialmente (Status 3)
                
                $sql = "update invoice set xml_path=\"$invoice->xml_path\", uuid=\"$invoice->uuid\", status=$invoice->status where id=$invoice->id";
                Executor::doit($sql);

                $_SESSION["sweetalert"] = "Factura Timbrada con éxito. UUID: " . $res["uuid"];
                $_SESSION["sweetalert_icon"] = "success";
            } else {
                $_SESSION["sweetalert"] = "Error al timbrar: " . $res["message"];
                $_SESSION["sweetalert_icon"] = "error";
            }
        }
        Core::redir("./?view=invoicedetail&id=" . $invoice->id);
    }
    else if($opt=="add_payment"){
        $invoice = InvoiceData::getById($_POST["invoice_id"]);
        $payments = PaymentData::getAllByInvoiceId($invoice->id);
        
        $last_payment = count($payments) > 0 ? end($payments) : null;
        $saldo_anterior = $last_payment ? $last_payment->saldo_insoluto : $invoice->total;
        
        $p = new PaymentData();
        $p->invoice_id = $_POST["invoice_id"];
        $p->payment_form_id = $_POST["payment_form_id"];
        $p->amount = $_POST["amount"];
        $p->saldo_anterior = $saldo_anterior;
        $p->saldo_insoluto = $saldo_anterior - $_POST["amount"];
        $p->num_parcialidad = count($payments) + 1;
        $p->date_at = $_POST["date_at"];
        $p->add();

        $_SESSION["sweetalert"] = "Pago registrado correctamente.";
        $_SESSION["sweetalert_icon"] = "success";
        Core::redir("./?view=invoicedetail&id=" . $invoice->id);
    }
    else if($opt=="get_pending_ppd"){
        $client_id = $_GET["client_id"];
        $sql = "select * from invoice where client_id=$client_id and payment_method_id=2 and status!=3"; // Solo PPD no timbradas totalmente (o con saldo)
        $query = Executor::doit($sql);
        $invoices = Model::many($query[0], new InvoiceData());
        
        if(count($invoices)>0):
        ?>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Total</th>
                    <th>Saldo Actual</th>
                    <th>Monto a Aplicar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $i): 
                    $payments = PaymentData::getAllByInvoiceId($i->id);
                    $last_payment = count($payments) > 0 ? end($payments) : null;
                    $saldo_actual = $last_payment ? $last_payment->saldo_insoluto : $i->total;
                ?>
                <tr>
                    <td><?php echo $i->serie.$i->folio; ?> (<?php echo $i->created_at; ?>)</td>
                    <td>$<?php echo number_format($i->total, 2); ?></td>
                    <td class="text-danger fw-bold">$<?php echo number_format($saldo_actual, 2); ?></td>
                    <td style="width: 200px;">
                        <input type="number" step="0.01" name="amount_<?php echo $i->id; ?>" class="form-control form-control-sm" placeholder="0.00" max="<?php echo $saldo_actual; ?>">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-warning mb-0">El cliente no tiene facturas PPD pendientes.</div>
        <?php endif;
        exit;
    }
    else if($opt=="add_payment_global"){
        $count = 0;
        foreach($_POST as $key => $val){
            if(strpos($key, "amount_") !== false && $val > 0){
                $invoice_id = str_replace("amount_", "", $key);
                $invoice = InvoiceData::getById($invoice_id);
                $payments = PaymentData::getAllByInvoiceId($invoice->id);
                
                $last_payment = count($payments) > 0 ? end($payments) : null;
                $saldo_anterior = $last_payment ? $last_payment->saldo_insoluto : $invoice->total;
                
                $p = new PaymentData();
                $p->invoice_id = $invoice_id;
                $p->payment_form_id = $_POST["payment_form_id"];
                $p->amount = $val;
                $p->saldo_anterior = $saldo_anterior;
                $p->saldo_insoluto = $saldo_anterior - $val;
                $p->num_parcialidad = count($payments) + 1;
                $p->date_at = $_POST["date_at"];
                $p->observations = $_POST["observations"];
                $p->add();
                $count++;
            }
        }

        if($count > 0){
            $_SESSION["sweetalert"] = "Se registraron $count abono(s) correctamente.";
            $_SESSION["sweetalert_icon"] = "success";
        }else{
            $_SESSION["sweetalert"] = "No se aplicó ningún monto a las facturas.";
            $_SESSION["sweetalert_icon"] = "warning";
        }
        Core::redir("./?view=newpayment");
    }
    else if($opt=="download_pdf"){
        $invoice = InvoiceData::getById($_GET["id"]);
        
        // Generar si no existe
        if($invoice->pdf_path == "" || !file_exists($invoice->pdf_path)){
            $path = PDFEngine::generate($invoice->id);
            $invoice->pdf_path = $path;
            $invoice->update_pdf();
        }

        if(ob_get_length()) ob_clean(); // Limpiar buffer
        
        $filename = $invoice->serie . $invoice->folio . ".pdf";

        header("Content-Description: File Transfer");
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($invoice->pdf_path));
        
        readfile($invoice->pdf_path);
        exit;
    }
}
?>
