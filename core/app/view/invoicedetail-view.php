<?php
if(isset($_GET["id"])):
$invoice = InvoiceData::getById($_GET["id"]);
$client = $invoice->getClient();
$items = InvoiceItemData::getAllByInvoiceId($invoice->id);
?>
<section class="container-fluid text-start">
<div class="mb-4">
    <a href="./?view=invoices&opt=all" class="btn btn-sm btn-light text-muted fw-bold mb-3"><i class="bi bi-arrow-left me-1"></i> Regresar</a>
    <h2 class="fw-bold text-dark mb-1">Detalle de Factura: <?php echo $invoice->serie.$invoice->folio; ?></h2>
    <p class="text-muted">Información detallada del comprobante fiscal.</p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-primary">Receptor</h5>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="mb-3">
                    <div class="small text-muted uppercase fw-bold">RFC</div>
                    <div class="fw-bold fs-5"><?php echo $client->rfc; ?></div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted uppercase fw-bold">Razón Social</div>
                    <div class="fw-semibold text-dark"><?php echo $client->name; ?></div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted uppercase fw-bold">Dirección</div>
                    <div class="text-dark small"><?php echo $client->address; ?>, CP: <?php echo $client->zip_code; ?></div>
                </div>
                <div class="mb-0">
                    <div class="small text-muted uppercase fw-bold">Régimen Fiscal</div>
                    <div class="text-dark small"><?php echo $client->getTaxRegime()->code." - ".$client->getTaxRegime()->name; ?></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm bg-light">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-primary">Información CFDI</h5>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="small text-muted fw-bold">Uso CFDI</div>
                        <div class="small fw-bold"><?php echo $invoice->getCFDIUse()->code; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted fw-bold">Tipo Comp.</div>
                        <div class="small fw-bold"><?php echo $invoice->type; ?></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="small text-muted fw-bold">Método Pago</div>
                        <div class="small fw-bold"><?php echo $invoice->getPaymentMethod()->code; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted fw-bold">Forma Pago</div>
                        <div class="small fw-bold"><?php echo $invoice->getPaymentForm()->code; ?></div>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="small text-muted fw-bold">UUID / Folio Fiscal</div>
                    <div class="small fw-bold text-break text-primary"><?php echo $invoice->uuid ? $invoice->uuid : 'PENDIENTE DE TIMBRAR'; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-primary">Conceptos Facturados</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cant.</th>
                                <th>SAT Code</th>
                                <th>Descripción</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $it): $p = ProductData::getById($it->product_id); ?>
                            <tr>
                                <td><?php echo $it->quantity; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo $p->getProductType()->code; ?></span></td>
                                <td>
                                    <div class="fw-bold small"><?php echo $it->description; ?></div>
                                    <div class="text-muted" style="font-size:10px;"><?php echo $p->getUnit()->code." - ".$p->getUnit()->name; ?></div>
                                </td>
                                <td class="text-end">$<?php echo number_format($it->price,2); ?></td>
                                <td class="text-end fw-bold">$<?php echo number_format($it->total,2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mt-4">
                    <div class="col-md-5">
                        <div class="p-4 rounded border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold">$<?php echo number_format($invoice->subtotal,2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted text-primary">Impuestos (IVA):</span>
                                <span class="fw-bold text-primary">$<?php echo number_format($invoice->total - $invoice->subtotal,2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 fw-bold">TOTAL:</span>
                                <span class="h4 mb-0 fw-bold text-dark">$<?php echo number_format($invoice->total,2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 p-4 pt-0 text-end">
                <?php if($invoice->xml_path == "" || !file_exists($invoice->xml_path)): ?>
                    <a href="./?action=invoices&opt=generate_xml&id=<?php echo $invoice->id; ?>" class="btn btn-outline-secondary fw-bold me-2"><i class="bi bi-filetype-xml me-1"></i> Generar XML</a>
                <?php else: ?>
                    <a href="./?action=invoices&opt=download_xml&id=<?php echo $invoice->id; ?>" class="btn btn-success fw-bold me-2 text-white"><i class="bi bi-download me-1"></i> Descargar XML</a>
                    <a href="./?action=invoices&opt=generate_xml&id=<?php echo $invoice->id; ?>" class="btn btn-sm btn-link text-muted me-2" title="Regenerar"><i class="bi bi-arrow-clockwise"></i></a>
                    
                    <?php if($invoice->status != 3): ?>
                        <a href="./?action=invoices&opt=stamp_xml&id=<?php echo $invoice->id; ?>" class="btn btn-primary fw-bold me-2"><i class="bi bi-patch-check me-1"></i> Timbrar (PAC)</a>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="./?action=invoices&opt=download_pdf&id=<?php echo $invoice->id; ?>" class="btn btn-danger fw-bold me-2"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
                
                <?php if($invoice->type == "I" && $invoice->status == 3): ?>
                    <a href="./?view=newinvoice&related_id=<?php echo $invoice->id; ?>&type=E" class="btn btn-warning fw-bold text-dark"><i class="bi bi-arrow-return-left me-1"></i> Nota de Crédito</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($invoice->payment_method_id == 2): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-cash-stack me-2"></i> Pagos y Parcialidades (PPD)</h5>
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalPayment">
                    <i class="bi bi-plus-lg me-1"></i> Registrar Pago
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Fecha Pago</th>
                                <th>Forma de Pago</th>
                                <th>S. Anterior</th>
                                <th>Monto Pagado</th>
                                <th>S. Insoluto</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $payments = PaymentData::getAllByInvoiceId($invoice->id); 
                            if(count($payments)>0):
                                foreach($payments as $p):
                            ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $p->num_parcialidad; ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($p->date_at)); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo $p->getPaymentForm()->name; ?></span></td>
                                <td>$<?php echo number_format($p->saldo_anterior, 2); ?></td>
                                <td class="text-success fw-bold">$<?php echo number_format($p->amount, 2); ?></td>
                                <td class="text-danger">$<?php echo number_format($p->saldo_insoluto, 2); ?></td>
                                <td>
                                    <?php if($p->is_stamped): ?>
                                        <span class="badge bg-success">Timbrado</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hay pagos registrados para esta factura.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPayment" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white border-bottom-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Registrar Abono</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="./?action=invoices&opt=add_payment">
        <input type="hidden" name="invoice_id" value="<?php echo $invoice->id; ?>">
        <div class="modal-body p-4">
            <?php 
                $last_payment = count($payments) > 0 ? end($payments) : null;
                $saldo_actual = $last_payment ? $last_payment->saldo_insoluto : $invoice->total;
                $proxima_parcialidad = count($payments) + 1;
            ?>
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="alert alert-info py-2 small mb-3">
                        Saldo Pendiente: <strong>$<?php echo number_format($saldo_actual, 2); ?></strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Monto a Pagar</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="amount" class="form-control" max="<?php echo $saldo_actual; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">No. Parcialidad</label>
                    <input type="number" name="num_parcialidad" class="form-control" value="<?php echo $proxima_parcialidad; ?>" readonly>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold small">Forma de Pago</label>
                    <select name="payment_form_id" class="form-select" required>
                        <?php foreach(PaymentFormData::getAll() as $pf): ?>
                        <option value="<?php echo $pf->id; ?>"><?php echo $pf->code." - ".$pf->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold small">Fecha de Pago</label>
                    <input type="datetime-local" name="date_at" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                </div>
            </div>
        </div>
        <div class="modal-footer border-top-0 p-4 pt-0">
            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">Guardar Pago</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

</section>
<?php endif; ?>
