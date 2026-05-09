<section class="container-fluid text-start">
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Registrar Nuevo Pago</h2>
    <p class="text-muted">Cree un comprobante de pago para facturas con método PPD.</p>
</div>

<div class="row">
    <div class="col-md-10 mx-auto">
        <form method="post" action="./?action=invoices&opt=add_payment_global">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-check me-2"></i> Datos del Cliente y Pago</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Seleccionar Cliente</label>
                        <select name="client_id" id="client_id" class="form-select select2" required onchange="loadPendingInvoices(this.value)">
                            <option value="">-- Seleccione un cliente --</option>
                            <?php foreach(ClientData::getAll() as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo $c->rfc." - ".$c->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fecha de Pago</label>
                        <input type="datetime-local" name="date_at" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Forma de Pago</label>
                        <select name="payment_form_id" class="form-select" required>
                            <?php foreach(PaymentFormData::getAll() as $pf): ?>
                            <option value="<?php echo $pf->id; ?>"><?php echo $pf->code." - ".$pf->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Moneda</label>
                        <select name="currency" class="form-select">
                            <option value="MXN">MXN - Peso Mexicano</option>
                            <option value="USD">USD - Dólar Americano</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Monto Total Recibido</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Observaciones / Notas</label>
                        <textarea name="observations" class="form-control" rows="2" placeholder="Opcional..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="pending_invoices_container" style="display:none;">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-text me-2"></i> Facturas PPD Pendientes</h5>
                    <p class="small text-muted mb-0">Seleccione las facturas a las que se aplicará este pago.</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div id="invoices_list">
                        <!-- Aquí se cargarán las facturas vía AJAX o carga inicial -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Buscando facturas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <a href="./?view=payments" class="btn btn-light fw-bold px-4 me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm">Registrar Pago Completo</button>
        </div>
        </form>
    </div>
</div>
</section>

<script>
function loadPendingInvoices(clientId) {
    if(!clientId) {
        $("#pending_invoices_container").hide();
        return;
    }
    
    $("#pending_invoices_container").show();
    $("#invoices_list").html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Buscando facturas...</p></div>');
    
    // Simulamos la carga o usamos un endpoint
    $.get("./?action=invoices&opt=get_pending_ppd&client_id=" + clientId, function(data) {
        $("#invoices_list").html(data);
    });
}
</script>
