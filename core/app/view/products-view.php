<section class="container-fluid text-start">
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Productos y Servicios</h2>
        <p class="text-muted mb-0">Catálogo de conceptos para facturación.</p>
    </div>
    <button type="button" class="btn btn-primary fw-bold shadow-sm" data-coreui-toggle="modal" data-coreui-target="#newModal">
        <i class="bi bi-plus-lg me-2"></i> Nuevo Concepto
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>SAT / Unidad</th>
                        <th>Impuesto</th>
                        <th width="120" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(ProductData::getAll() as $item): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo $item->code; ?></span></td>
                        <td class="fw-semibold"><?php echo $item->name; ?></td>
                        <td>$<?php echo number_format($item->price,2); ?></td>
                        <td><?php echo $item->getCategory()->name; ?></td>
                        <td>
                            <small class="d-block text-muted"><?php echo $item->getProductType()->code; ?></small>
                            <small class="d-block text-muted"><?php echo $item->getUnit()->code; ?></small>
                        </td>
                        <td><span class="badge bg-info text-white"><?php echo $item->getTax()->name; ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" data-coreui-toggle="modal" data-coreui-target="#editModal<?php echo $item->id; ?>"><i class="bi bi-pencil"></i></button>
                            <a href="./?action=products&opt=del&id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?php echo $item->id; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold">Editar Concepto</h5>
                                    <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                </div>
                                <form method="post" action="./?action=products&opt=upd">
                                <div class="modal-body p-4">
                                    <input type="hidden" name="id" value="<?php echo $item->id; ?>">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Código Interno</label>
                                            <input type="text" name="code" class="form-control" value="<?php echo $item->code; ?>" required>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label fw-bold">Nombre del Producto/Servicio</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo $item->name; ?>" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Descripción</label>
                                            <textarea name="description" class="form-control" rows="2"><?php echo $item->description; ?></textarea>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Precio Unitario</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $item->price; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label fw-bold">Categoría</label>
                                            <select name="category_id" class="form-control" required>
                                                <?php foreach(CategoryData::getAll() as $cat): ?>
                                                <option value="<?php echo $cat->id; ?>" <?php echo ($item->category_id==$cat->id)?"selected":""; ?>><?php echo $cat->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Clave SAT (Prod/Serv)</label>
                                            <select name="product_type_id" class="form-control select2" required>
                                                <?php foreach(ProductTypeData::getAll() as $pt): ?>
                                                <option value="<?php echo $pt->id; ?>" <?php echo ($item->product_type_id==$pt->id)?"selected":""; ?>><?php echo $pt->code." - ".$pt->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Unidad de Medida</label>
                                            <select name="unit_id" class="form-control select2" required>
                                                <?php foreach(UnitData::getAll() as $u): ?>
                                                <option value="<?php echo $u->id; ?>" <?php echo ($item->unit_id==$u->id)?"selected":""; ?>><?php echo $u->code." - ".$u->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Impuesto Aplicable</label>
                                            <select name="tax_id" class="form-control" required>
                                                <?php foreach(TaxData::getAll() as $t): ?>
                                                <option value="<?php echo $t->id; ?>" <?php echo ($item->tax_id==$t->id)?"selected":""; ?>><?php echo $t->code." - ".$t->name; ?></option>
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
                <h5 class="modal-title fw-bold">Nuevo Concepto</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
            </div>
            <form method="post" action="./?action=products&opt=add">
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Código Interno</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Nombre del Producto/Servicio</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach(CategoryData::getAll() as $cat): ?>
                            <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Clave SAT (Prod/Serv)</label>
                        <select name="product_type_id" class="form-control select2" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach(ProductTypeData::getAll() as $pt): ?>
                            <option value="<?php echo $pt->id; ?>"><?php echo $pt->code." - ".$pt->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Unidad de Medida</label>
                        <select name="unit_id" class="form-control select2" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach(UnitData::getAll() as $u): ?>
                            <option value="<?php echo $u->id; ?>"><?php echo $u->code." - ".$u->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Impuesto Aplicable</label>
                        <select name="tax_id" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach(TaxData::getAll() as $t): ?>
                            <option value="<?php echo $t->id; ?>"><?php echo $t->code." - ".$t->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-coreui-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold">Crear Concepto</button>
            </div>
            </form>
        </div>
    </div>
</div>
</section>
