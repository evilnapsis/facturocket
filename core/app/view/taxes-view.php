<section class="container-fluid text-start">
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Impuestos</h2>
        <p class="text-muted mb-0">Administración del catálogo de Impuestos del SAT.</p>
    </div>
    <button type="button" class="btn btn-primary fw-bold shadow-sm" data-coreui-toggle="modal" data-coreui-target="#newModal">
        <i class="bi bi-plus-lg me-2"></i> Nuevo Registro
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead class="table-light">
                    <tr>
                        <th width="100">Código</th>
                        <th>Nombre</th>
                        <th width="120" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(TaxData::getAll() as $item): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo $item->code; ?></span></td>
                        <td class="fw-semibold"><?php echo $item->name; ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" data-coreui-toggle="modal" data-coreui-target="#editModal<?php echo $item->id; ?>"><i class="bi bi-pencil"></i></button>
                            <a href="./?action=taxes&opt=del&id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?php echo $item->id; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold">Editar Registro</h5>
                                    <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                </div>
                                <form method="post" action="./?action=taxes&opt=upd">
                                <div class="modal-body p-4">
                                    <input type="hidden" name="id" value="<?php echo $item->id; ?>">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Código SAT</label>
                                        <input type="text" name="code" class="form-control" value="<?php echo $item->code; ?>" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Nombre</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $item->name; ?>" required>
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
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Nuevo Registro</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
            </div>
            <form method="post" action="./?action=taxes&opt=add">
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Código SAT</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-coreui-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold">Crear Registro</button>
            </div>
            </form>
        </div>
    </div>
</div>
</section>
