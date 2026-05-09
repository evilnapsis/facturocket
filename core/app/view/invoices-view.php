<section class="container-fluid text-start">
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Historial de Facturas</h2>
        <p class="text-muted mb-0">Listado de comprobantes fiscales generados.</p>
    </div>
    <a href="./?view=newinvoice" class="btn btn-primary fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-2"></i> Nueva Factura
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead class="table-light">
                    <tr>
                        <th width="120">Serie/Folio</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th width="100">Estado</th>
                        <th width="120" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(InvoiceData::getAll() as $item): ?>
                    <tr>
                        <td class="fw-bold"><?php echo $item->serie.$item->folio; ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($item->date)); ?></td>
                        <td class="fw-semibold"><?php echo $item->getClient()->name; ?></td>
                        <td class="fw-bold">$<?php echo number_format($item->total,2); ?></td>
                        <td>
                            <?php if($item->status==1): ?>
                                <span class="badge bg-secondary">Borrador</span>
                            <?php elseif($item->status==2): ?>
                                <span class="badge bg-success">Timbrada</span>
                            <?php elseif($item->status==3): ?>
                                <span class="badge bg-danger">Cancelada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="./?view=invoicedetail&id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalle"><i class="bi bi-eye"></i></a>
                            <a href="./?action=invoices&opt=download_xml&id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-secondary" title="Descargar XML"><i class="bi bi-filetype-xml"></i></a>
                            <a href="./?action=invoices&opt=download_pdf&id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-danger" title="Descargar PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
