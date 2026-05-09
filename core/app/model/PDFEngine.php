<?php
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PDFEngine {

    public static function generate($invoice_id) {
        $invoice = InvoiceData::getById($invoice_id);
        $client = ClientData::getById($invoice->client_id);
        $items = InvoiceItemData::getAllByInvoiceId($invoice_id);
        $emisor = SettingData::getAll();
        
        $logo = SettingData::getByShort("logo")->val;
        $logo_path = "storage/branding/" . $logo;
        $logo_base64 = "";
        if (file_exists($logo_path) && !is_dir($logo_path)) {
            $logo_data = file_get_contents($logo_path);
            $logo_base64 = 'data:image/' . pathinfo($logo_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logo_data);
        }

        // Obtener datos del XML si existe
        $xml_content = "";
        $uuid = $invoice->uuid;
        if (file_exists($invoice->xml_path)) {
            $xml_content = file_get_contents($invoice->xml_path);
        }

        // Generar QR para el SAT (solo si está timbrada)
        $qr_base64 = "";
        if ($uuid != "") {
            $re = SettingData::getByShort("rfc")->val;
            $rr = $client->rfc;
            $tt = number_format($invoice->total, 6, '.', '');
            $id = $uuid;
            $qr_url = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=$id&re=$re&rr=$rr&tt=$tt&fe=TEST";
            
            $qrOptions = new QROptions(['outputType' => QRCode::OUTPUT_MARKUP_SVG]);
            $qr_base64 = (new QRCode($qrOptions))->render($qr_url);
        }

        // HTML Template
        ob_start();
        ?>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
                .header { width: 100%; margin-bottom: 20px; }
                .logo { width: 150px; }
                .company-info { text-align: right; }
                .section-title { background: #f4f4f4; padding: 5px; font-weight: bold; margin-top: 10px; border-bottom: 2px solid #ddd; }
                .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .table th { background: #5856d6; color: white; padding: 6px; text-align: left; }
                .table td { padding: 6px; border-bottom: 1px solid #eee; }
                .totals { margin-top: 20px; width: 300px; margin-left: auto; }
                .totals td { padding: 4px; }
                .footer-box { margin-top: 30px; border: 1px solid #eee; padding: 10px; }
                .qr-section { width: 100px; float: left; }
                .stamps-section { margin-left: 110px; font-size: 8px; word-break: break-all; }
                .page-number:after { content: counter(page); }
            </style>
        </head>
        <body>
            <table class="header">
                <tr>
                    <td width="50%">
                        <?php if($logo_base64): ?><img src="<?php echo $logo_base64; ?>" class="logo"><?php endif; ?>
                        <div style="margin-top:10px;">
                            <strong style="font-size:14px;"><?php echo SettingData::getByShort("company_name")->val; ?></strong><br>
                            RFC: <?php echo SettingData::getByShort("rfc")->val; ?><br>
                            <?php echo SettingData::getByShort("address")->val; ?><br>
                            CP: <?php echo SettingData::getByShort("zip_code")->val; ?>
                        </div>
                    </td>
                    <td class="company-info" width="50%">
                        <div style="font-size:18px; color:#5856d6; font-weight:bold;">FACTURA</div>
                        <div style="font-size:12px; margin-top:5px;">
                            Serie y Folio: <strong><?php echo $invoice->serie.$invoice->folio; ?></strong><br>
                            Fecha Emisión: <?php echo $invoice->created_at; ?><br>
                            Tipo: <?php echo $invoice->type == "I" ? "Ingreso" : "Egreso"; ?>
                        </div>
                        <?php if($uuid): ?>
                        <div style="margin-top:10px; font-size:9px;">
                            Folio Fiscal (UUID):<br>
                            <span style="font-weight:bold;"><?php echo $uuid; ?></span>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <div class="section-title">RECEPTOR</div>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td width="60%">
                        <strong><?php echo $client->name; ?></strong><br>
                        RFC: <?php echo $client->rfc; ?><br>
                        Uso CFDI: <?php echo $client->getCFDIUse()->code . " - " . $client->getCFDIUse()->name; ?>
                    </td>
                    <td width="40%">
                        CP: <?php echo $client->zip_code; ?><br>
                        Régimen: <?php echo $client->getTaxRegime()->code . " - " . $client->getTaxRegime()->name; ?>
                    </td>
                </tr>
            </table>

            <table class="table">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Clave SAT</th>
                        <th>Descripción</th>
                        <th>Valor Unit.</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $it): 
                        $p = ProductData::getById($it->product_id);
                    ?>
                    <tr>
                        <td width="10%"><?php echo $it->quantity; ?></td>
                        <td width="15%"><?php echo $p->getProductType()->code; ?></td>
                        <td width="45%"><?php echo $it->description; ?></td>
                        <td width="15%">$<?php echo number_format($it->price, 2); ?></td>
                        <td width="15%">$<?php echo number_format($it->total, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <table class="totals">
                <tr>
                    <td width="60%">Subtotal:</td>
                    <td width="40%" align="right">$<?php echo number_format($invoice->subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td>IVA (16%):</td>
                    <td align="right">$<?php echo number_format($invoice->total - $invoice->subtotal, 2); ?></td>
                </tr>
                <tr style="background:#5856d6; color:white; font-weight:bold;">
                    <td>TOTAL:</td>
                    <td align="right">$<?php echo number_format($invoice->total, 2); ?></td>
                </tr>
            </table>

            <div style="margin-top:20px;">
                <p>Método de Pago: <?php echo $invoice->getPaymentMethod()->code . " - " . $invoice->getPaymentMethod()->name; ?></p>
                <p>Forma de Pago: <?php echo $invoice->getPaymentForm()->code . " - " . $invoice->getPaymentForm()->name; ?></p>
                <p>Moneda: <?php echo $invoice->currency; ?></p>
            </div>

            <?php if($uuid != ""): ?>
            <div class="footer-box">
                <div class="qr-section">
                    <img src="<?php echo $qr_base64; ?>" style="width:100px;">
                </div>
                <div class="stamps-section">
                    <strong>Sello Digital del Emisor:</strong><br>
                    <span style="font-size:7px;">Ejemplo_de_sello_digital_provisional_para_la_representacion_impresa_cfdi_4_0</span><br><br>
                    <strong>Sello Digital del SAT:</strong><br>
                    <span style="font-size:7px;">Ejemplo_de_sello_sat_provisional_para_la_representacion_impresa_cfdi_4_0</span><br><br>
                    <strong>Cadena Original del Complemento de Certificación Digital del SAT:</strong><br>
                    <span style="font-size:7px;">||1.1|<?php echo $uuid; ?>|2026-05-09T00:00:00|SAT970701NN3|Ejemplo_de_cadena_original...||</span>
                </div>
                <div style="clear:both;"></div>
            </div>
            <?php endif; ?>

            <div style="text-align:center; margin-top:30px; font-size:8px; color:#999;">
                ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI 4.0
            </div>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        // Setup Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save file
        $pdf_dir = "storage/invoices/pdf/";
        if(!file_exists($pdf_dir)) mkdir($pdf_dir, 0777, true);
        
        $filename = $invoice->serie . $invoice->folio . ".pdf";
        $filepath = $pdf_dir . $filename;
        file_put_contents($filepath, $dompdf->output());

        // Update invoice record
        $invoice->pdf_path = $filepath;
        $invoice->update_pdf();

        return $filepath;
    }
}
?>
