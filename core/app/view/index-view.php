<?php 
if(!isset($_SESSION["user_id"])){ Core::redir("./");}
$user = UserData::getById($_SESSION["user_id"]);
$invoices = InvoiceData::getAll();
$clients = ClientData::getAll();
$products = ProductData::getAll();
$total_billed = 0;
foreach($invoices as $i) { $total_billed += $i->total; }
?>
<section class="container-fluid text-start">
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Panel de Control</h2>
    <p class="text-muted">Resumen general de su sistema de facturación.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-primary bg-opacity-25 p-2 rounded-3 me-3">
                    <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo count($invoices); ?></h4>
                    <small class="text-white-50 text-uppercase fw-bold" style="font-size: 10px;">Facturas</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-success bg-opacity-25 p-2 rounded-3 me-3">
                    <i class="bi bi-people text-success fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo count($clients); ?></h4>
                    <small class="text-white-50 text-uppercase fw-bold" style="font-size: 10px;">Clientes</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-warning bg-opacity-25 p-2 rounded-3 me-3">
                    <i class="bi bi-box-seam text-warning fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo count($products); ?></h4>
                    <small class="text-white-50 text-uppercase fw-bold" style="font-size: 10px;">Conceptos</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-danger bg-opacity-25 p-2 rounded-3 me-3">
                    <i class="bi bi-currency-dollar text-danger fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">$<?php echo number_format($total_billed,0); ?></h4>
                    <small class="text-white-50 text-uppercase fw-bold" style="font-size: 10px;">Total</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Últimas Facturas</h5>
                <a href="./?view=invoices&opt=all" class="btn btn-sm btn-light">Ver todas</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent = array_slice(array_reverse($invoices), 0, 8);
                            foreach($recent as $i): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $i->serie.$i->folio; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($i->date)); ?></td>
                                <td class="small"><?php echo $i->getClient()->name; ?></td>
                                <td class="fw-bold">$<?php echo number_format($i->total,2); ?></td>
                                <td>
                                    <?php if($i->status==1): ?>
                                        <span class="badge bg-secondary">Borrador</span>
                                    <?php elseif($i->status==2): ?>
                                        <span class="badge bg-success">Timbrada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i> Acciones Rápidas</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <a href="./?view=newinvoice" class="btn btn-primary fw-bold py-2"><i class="bi bi-plus-lg me-2"></i> Nueva Factura</a>
                    <a href="./?view=clients&opt=all" class="btn btn-outline-dark fw-bold py-2"><i class="bi bi-person-plus me-2"></i> Registrar Cliente</a>
                    <a href="./?view=products&opt=all" class="btn btn-outline-dark fw-bold py-2"><i class="bi bi-box-seam me-2"></i> Nuevo Concepto</a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Estado del SAT</h5>
                <div class="d-flex align-items-center mb-3">
                    <div class="spinner-grow spinner-grow-sm text-light me-2" role="status"></div>
                    <span class="small fw-bold">Conexión con PAC: Activa</span>
                </div>
                <div class="small opacity-75 mb-0">Certificados (CSD) vigentes hasta 2027.</div>
            </div>
        </div>
    </div>
</div>
</section>
