<section class="container-fluid text-start">
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Clientes</h2>
        <p class="text-muted mb-0">Gestión de receptores para facturación.</p>
    </div>
    <button type="button" class="btn btn-primary fw-bold shadow-sm" data-coreui-toggle="modal" data-coreui-target="#newModal">
        <i class="bi bi-plus-lg me-2"></i> Nuevo Cliente
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead class="table-light">
                    <tr>
                        <th>RFC</th>
                        <th>Nombre / Razón Social</th>
                        <th>Email</th>
                        <th>Régimen</th>
                        <th width="120" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(ClientData::getAll() as $item): ?>
                    <tr>
                        <td><span class="badge bg-primary"><?php echo $item->rfc; ?></span></td>
                        <td class="fw-semibold"><?php echo $item->name; ?></td>
                        <td><?php echo $item->email; ?></td>
                        <td><small><?php echo $item->getTaxRegime()->code; ?></small></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" data-coreui-toggle="modal" data-coreui-target="#editModal<?php echo $item->id; ?>"><i class="bi bi-pencil"></i></button>
                            <a href="./?action=clients&opt=del&id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?php echo $item->id; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold">Editar Cliente</h5>
                                    <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                </div>
                                <form method="post" action="./?action=clients&opt=upd">
                                <div class="modal-body p-4">
                                    <input type="hidden" name="id" value="<?php echo $item->id; ?>">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">RFC</label>
                                            <input type="text" name="rfc" class="form-control" value="<?php echo $item->rfc; ?>" required>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label fw-bold">Nombre / Razón Social</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo $item->name; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Email</label>
                                            <input type="email" name="email" class="form-control" value="<?php echo $item->email; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Código Postal</label>
                                            <input type="text" name="zip_code" class="form-control" value="<?php echo $item->zip_code; ?>" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Dirección Fiscal</label>
                                            <input type="text" name="address" class="form-control" value="<?php echo $item->address; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Régimen Fiscal</label>
                                            <select name="tax_regime_id" class="form-control" required>
                                                <?php foreach(TaxRegimeData::getAll() as $tr): ?>
                                                <option value="<?php echo $tr->id; ?>" <?php echo ($item->tax_regime_id==$tr->id)?"selected":""; ?>><?php echo $tr->code." - ".$tr->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Uso de CFDI (Default)</label>
                                            <select name="cfdi_use_id" class="form-control" required>
                                                <?php foreach(CfdiUseData::getAll() as $cu): ?>
                                                <option value="<?php echo $cu->id; ?>" <?php echo ($item->cfdi_use_id==$cu->id)?"selected":""; ?>><?php echo $cu->code." - ".$cu->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pb-4 px-4">
                                    <button type="button" class="btn btn-light fw-bold" data-coreui-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="newModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
            </div>
            <form method="post" action="./?action=clients&opt=add">
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">RFC</label>
                        <input type="text" name="rfc" class="form-control" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Nombre / Razón Social</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Código Postal</label>
                        <input type="text" name="zip_code" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Dirección Fiscal</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Régimen Fiscal</label>
                        <select name="tax_regime_id" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach(TaxRegimeData::getAll() as $tr): ?>
                            <option value="<?php echo $tr->id; ?>"><?php echo $tr->code." - ".$tr->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Uso de CFDI (Default)</label>
                        <select name="cfdi_use_id" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach(CfdiUseData::getAll() as $cu): ?>
                            <option value="<?php echo $cu->id; ?>"><?php echo $cu->code." - ".$cu->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-coreui-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold">Crear Cliente</button>
            </div>
            </form>
        </div>
    </div>
</div>
</section>
