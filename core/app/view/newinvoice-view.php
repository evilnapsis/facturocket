<?php 
    $related = null;
    if(isset($_GET["related_id"])){
        $related = InvoiceData::getById($_GET["related_id"]);
    }
    $type = isset($_GET["type"]) ? $_GET["type"] : "I";
?>
<section class="container-fluid text-start">
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1"><?php echo $type=="I" ? "Nueva Factura" : "Nueva Nota de Crédito"; ?></h2>
    <p class="text-muted">Genere un nuevo comprobante fiscal (CFDI 4.0).</p>
</div>

<div class="row">
    <!-- Formulario de Configuración -->
    <div class="col-md-4">
        <form method="post" action="./?action=invoices&opt=save">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i> Datos del Cliente</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Cliente</label>
                    <select name="client_id" class="form-control select2" required>
                        <option value="">-- Seleccione Cliente --</option>
                        <?php foreach(ClientData::getAll() as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo ($related && $related->client_id == $c->id) ? "selected" : ""; ?>><?php echo $c->rfc." - " . $c->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Uso de CFDI</label>
                    <select name="cfdi_use_id" class="form-control" required>
                        <?php foreach(CFDIUseData::getAll() as $u): ?>
                        <option value="<?php echo $u->id; ?>"><?php echo $u->code." - ".$u->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <hr class="my-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-credit-card me-2 text-primary"></i> Pago y CFDI</h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">Método de Pago</label>
                    <select name="payment_method_id" class="form-control" required>
                        <?php foreach(PaymentMethodData::getAll() as $pm): ?>
                        <option value="<?php echo $pm->id; ?>" <?php echo ($related && $type=="E" && $pm->code=="PUE") ? "selected" : ""; ?>><?php echo $pm->code." - ".$pm->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Forma de Pago</label>
                    <select name="payment_form_id" class="form-control" required>
                        <?php foreach(PaymentFormData::getAll() as $pf): ?>
                        <option value="<?php echo $pf->id; ?>" <?php echo ($related && $type=="E" && $pf->code=="15") ? "selected" : "selected"; ?>><?php echo $pf->code." - ".$pf->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Moneda</label>
                        <select name="currency" class="form-control">
                            <option value="MXN" <?php echo ($related && $related->currency=="MXN") ? "selected" : ""; ?>>MXN</option>
                            <option value="USD" <?php echo ($related && $related->currency=="USD") ? "selected" : ""; ?>>USD</option>
                        </select>
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-link-45deg me-2 text-primary"></i> CFDI Relacionados <small class="fw-normal text-muted" style="font-size:12px;">(Opcional)</small></h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Relación</label>
                    <select name="relation_type_id" class="form-select">
                        <option value="">-- Sin relación --</option>
                        <?php foreach(RelationTypeData::getAll() as $rt): ?>
                        <option value="<?php echo $rt->id; ?>" <?php echo ($related && $type=="E" && $rt->code=="01") ? "selected" : ""; ?>><?php echo $rt->code." - ".$rt->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">UUID Relacionado</label>
                    <input type="text" name="related_uuid" class="form-control" placeholder="ABC12345-1234-..." pattern="[a-fA-F0-9\-]{36}" value="<?php echo $related ? $related->uuid : ""; ?>">
                    <small class="text-muted">Si son varios, sepárelos por comas.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Buscador y Carrito -->
    <div class="col-md-8">
        <!-- Buscador de Productos -->
        <div class="card mb-4 border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i> Agregar Conceptos</h5>
                    <div class="row g-2">
                        <div class="col-md-8">
                            <select name="product_id" class="form-control select2">
                                <option value="">Buscar producto o servicio...</option>
                                <?php foreach(ProductData::getAll() as $p): ?>
                                <option value="<?php echo $p->id; ?>"><?php echo $p->code." - ".$p->name." ($".number_format($p->price,2).")"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" formaction="./?action=invoices&opt=addtocart" formnovalidate class="btn btn-light fw-bold text-primary"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Tabla de Conceptos -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between">
                <h5 class="fw-bold mb-0">Detalle de Factura</h5>
                <a href="./?action=invoices&opt=clear" class="btn btn-sm btn-outline-danger fw-bold"><i class="bi bi-trash me-1"></i> Vaciar</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cant.</th>
                                <th>Descripción</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal = 0;
                            if(isset($_SESSION["cart"]) && count($_SESSION["cart"])>0):
                                foreach($_SESSION["cart"] as $index => $item):
                                    $p = ProductData::getById($item["product_id"]);
                                    $item_total = $item["price"] * $item["quantity"];
                                    $subtotal += $item_total;
                            ?>
                            <tr>
                                <td><?php echo $item["quantity"]; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $p->name; ?></div>
                                    <small class="text-muted"><?php echo $p->getProductType()->code; ?> - <?php echo $p->getUnit()->code; ?></small>
                                </td>
                                <td>$<?php echo number_format($item["price"],2); ?></td>
                                <td class="fw-bold">$<?php echo number_format($item_total,2); ?></td>
                                <td class="text-end">
                                    <a href="./?action=invoices&opt=remfromcart&index=<?php echo $index; ?>" class="text-danger"><i class="bi bi-x-circle-fill"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted italic">No hay conceptos agregados</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Resumen de Totales -->
                <?php 
                $tax_rate = 0.16;
                $taxes = $subtotal * $tax_rate;
                $total = $subtotal + $taxes;
                ?>
                <div class="row justify-content-end mt-4">
                    <div class="col-md-5">
                        <div class="bg-light rounded p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold">$<?php echo number_format($subtotal,2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">IVA (16%):</span>
                                <span class="fw-bold text-primary">$<?php echo number_format($taxes,2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 fw-bold">TOTAL:</span>
                                <span class="h4 mb-0 fw-bold text-dark">$<?php echo number_format($total,2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 p-4 pt-0 text-end">
                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                <input type="hidden" name="discount" value="0">
                <input type="hidden" name="total" value="<?php echo $total; ?>">
                <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm px-5 <?php echo ($subtotal==0)?"disabled":""; ?>"><i class="bi bi-check2-circle me-2"></i> Generar Factura</button>
            </div>
        </div>
        </form>
    </div>
</div>
</section>
