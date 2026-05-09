<?php
use CfdiUtils\CfdiCreator40;
use CfdiUtils\Nodes\Node;
use CfdiUtils\Certificado\Certificado;
use PhpCfdi\Credentials\Credential;

class CFDIEngine {

    public static function createXML($invoice_id) {
        $invoice = InvoiceData::getById($invoice_id);
        $client = $invoice->getClient();
        $items = InvoiceItemData::getAllByInvoiceId($invoice_id);
        $user = UserData::getById($invoice->user_id);
        
        // Cargar datos del emisor desde settings
        $emisor_rfc = SettingData::getByShort("rfc") ? SettingData::getByShort("rfc")->val : "EKU9003173C9"; // Test RFC
        $emisor_name = SettingData::getByShort("commercial_name") ? SettingData::getByShort("commercial_name")->val : "ESCUELA KEMPER URGATE";
        $emisor_regime = SettingData::getByShort("tax_regime_id") ? SettingData::getByShort("tax_regime_id")->val : "601";
        $emisor_cp = SettingData::getByShort("zip_code") ? SettingData::getByShort("zip_code")->val : "01000";

        $creator = new CfdiCreator40([
            'Serie' => $invoice->serie,
            'Folio' => $invoice->folio,
            'Fecha' => date('Y-m-d\TH:i:s'),
            'FormaPago' => $invoice->getPaymentForm()->code,
            'NoCertificado' => '00001000000504465930', // Test Cert
            'SubTotal' => number_format($invoice->subtotal, 2, '.', ''),
            'Moneda' => $invoice->currency,
            'Total' => number_format($invoice->total, 2, '.', ''),
            'TipoDeComprobante' => $invoice->type,
            'Exportacion' => '01',
            'MetodoPago' => $invoice->getPaymentMethod()->code,
            'LugarExpedicion' => $emisor_cp,
        ]);

        $comprobante = $creator->comprobante();

        $comprobante->addEmisor([
            'Rfc' => $emisor_rfc,
            'Nombre' => $emisor_name,
            'RegimenFiscal' => $emisor_regime,
        ]);

        $comprobante->addReceptor([
            'Rfc' => $client->rfc,
            'Nombre' => $client->name,
            'DomicilioFiscalReceptor' => $client->zip_code,
            'RegimenFiscalReceptor' => $client->getTaxRegime()->code,
            'UsoCFDI' => $client->getCfdiUse()->code,
        ]);

        foreach ($items as $it) {
            $p = $it->getProduct();
            $concepto = $comprobante->addConcepto([
                'ClaveProdServ' => $p->getProductType()->code,
                'NoIdentificacion' => $p->code,
                'Cantidad' => number_format($it->quantity, 6, '.', ''),
                'ClaveUnidad' => $p->getUnit()->code,
                'Unidad' => $p->getUnit()->name,
                'Descripcion' => $it->description,
                'ValorUnitario' => number_format($it->price, 6, '.', ''),
                'Importe' => number_format($it->total, 6, '.', ''),
                'ObjetoImp' => $it->getTaxObject()->code,
            ]);

            if ($it->tax_object_id == 2) { // Sí objeto de impuesto
                $tax = $p->getTax();
                $concepto->addTraslado([
                    'Base' => number_format($it->total, 6, '.', ''),
                    'Impuesto' => $tax->code,
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => '0.160000',
                    'Importe' => number_format($it->total * 0.16, 6, '.', ''),
                ]);
            }
        }

        // Sellado Digital dinámico desde settings
        $certName = SettingData::getByShort("csd_cert") ? SettingData::getByShort("csd_cert")->val : "";
        $keyName = SettingData::getByShort("csd_key") ? SettingData::getByShort("csd_key")->val : "";
        $password = SettingData::getByShort("csd_pass") ? SettingData::getByShort("csd_pass")->val : "";

        $certPath = "storage/branding/" . $certName;
        $keyPath = "storage/branding/" . $keyName;

        if($certName != "" && $keyName != "" && file_exists($certPath) && file_exists($keyPath)){
            try {
                $credential = Credential::openFiles($certPath, $keyPath, $password);
                $creator->putCertificado(new Certificado($certPath));
                $creator->addSello($credential->privateKey()->pem(), $password);
            } catch (Exception $e) {
                // Si hay error en los archivos, los ignoramos para que al menos genere el XML sin sello
            }
        }

        return $creator;
    }
}
?>
