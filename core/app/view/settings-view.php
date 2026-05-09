<?php
$settings = SettingData::getAll();
// Helper function to get setting value safely
function s_val($short) {
    $s = SettingData::getByShort($short);
    return $s ? $s->val : "";
}
?>
<section class="container-fluid text-start">
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Configuración del Emisor</h2>
    <p class="text-muted">Administre los datos fiscales, certificados y folios de su empresa.</p>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <ul class="nav nav-pills" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="general-tab" data-coreui-toggle="tab" data-coreui-target="#general" type="button" role="tab"><i class="bi bi-building me-2"></i> General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="fiscal-tab" data-coreui-toggle="tab" data-coreui-target="#fiscal" type="button" role="tab"><i class="bi bi-shield-check me-2"></i> Fiscal / CSD</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="api-tab" data-coreui-toggle="tab" data-coreui-target="#api" type="button" role="tab"><i class="bi bi-cloud-arrow-up me-2"></i> PAC / API</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="folios-tab" data-coreui-toggle="tab" data-coreui-target="#folios" type="button" role="tab"><i class="bi bi-hash me-2"></i> Folios</button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <form id="settings-form" enctype="multipart/form-data">
                <div class="tab-content" id="settingsTabsContent">
                    
                    <!-- Tab General -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre o Razón Social</label>
                                <input type="text" name="company_name" class="form-control" value="<?php echo s_val('company_name'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre Comercial</label>
                                <input type="text" name="commercial_name" class="form-control" value="<?php echo s_val('commercial_name'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Logotipo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <?php if($logo = s_val('logo')): ?>
                                    <div class="mt-2"><img src="storage/branding/<?php echo $logo; ?>" style="height: 60px;" class="img-thumbnail"></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="<?php echo s_val('email'); ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Dirección Fiscal</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo s_val('address'); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Fiscal -->
                    <div class="tab-pane fade" id="fiscal" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">RFC</label>
                                <input type="text" name="rfc" class="form-control" value="<?php echo s_val('rfc'); ?>" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Régimen Fiscal</label>
                                <select name="tax_regime_id" class="form-select">
                                    <?php 
                                    $current_tr = s_val('tax_regime_id');
                                    foreach(TaxRegimeData::getAll() as $tr): ?>
                                    <option value="<?php echo $tr->id; ?>" <?php echo ($current_tr == $tr->id) ? "selected" : ""; ?>><?php echo $tr->code." - ".$tr->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Certificado (.cer)</label>
                                <input type="file" name="csd_cert" class="form-control">
                                <?php if($c=s_val('csd_cert')): ?>
                                    <div class="mt-1 text-success small"><i class="bi bi-check-circle-fill"></i> Cargado: <?php echo $c; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Llave Privada (.key)</label>
                                <input type="file" name="csd_key" class="form-control">
                                <?php if($k=s_val('csd_key')): ?>
                                    <div class="mt-1 text-success small"><i class="bi bi-check-circle-fill"></i> Cargado: <?php echo $k; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Contraseña CSD</label>
                                <input type="password" name="csd_pass" class="form-control" value="<?php echo s_val('csd_pass'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Tab API -->
                    <div class="tab-pane fade" id="api" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Usuario Facturama (Sandbox)</label>
                                <input type="text" name="facturama_user" class="form-control" value="<?php echo s_val('facturama_user'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Contraseña Facturama (Sandbox)</label>
                                <input type="password" name="facturama_pass" class="form-control" value="<?php echo s_val('facturama_pass'); ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Serial PAC / API Key (Producción)</label>
                                <input type="text" name="pac_serial" class="form-control" value="<?php echo s_val('pac_serial'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Tab Folios -->
                    <div class="tab-pane fade" id="folios" role="tabpanel">
                        <div class="row g-3">
                            <?php foreach(FolioSequenceData::getAll() as $fs): ?>
                            <div class="col-md-6 mb-3 p-3 border rounded bg-light">
                                <label class="form-label fw-bold small text-primary mb-2">
                                    <?php 
                                        if($fs->type_code=="I") echo "FACTURAS (Ingreso)";
                                        elseif($fs->type_code=="E") echo "NOTAS DE CRÉDITO";
                                        elseif($fs->type_code=="P") echo "PAGOS (Complemento)";
                                        else echo "NOTAS DE VENTA";
                                    ?>
                                </label>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="small text-muted">Serie</label>
                                        <input type="text" name="serie_<?php echo $fs->type_code; ?>" class="form-control" value="<?php echo $fs->serie; ?>">
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted">Siguiente Folio</label>
                                        <input type="number" name="folio_<?php echo $fs->type_code; ?>" class="form-control" value="<?php echo $fs->next_folio; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div id="ajax-status" class="small fw-bold"></div>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4"><i class="bi bi-save me-2"></i> Guardar Cambios</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</section>

<script>
$(document).ready(function() {
    $("#settings-form").on("submit", function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var $status = $("#ajax-status");
        var $btn = $(this).find("button[type='submit']");

        $status.html('<i class="bi bi-hourglass-split me-2 text-primary"></i> Guardando...').fadeIn();
        $btn.prop("disabled", true);

        $.ajax({
            url: "./?action=settings&opt=upd_ajax",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                try {
                    var res = JSON.parse(response);
                    if(res.status == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $status.html('<i class="bi bi-check-circle-fill me-2 text-success"></i> Cambios guardados.').fadeOut(3000);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                        $status.html('<i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i> Error al guardar.');
                    }
                } catch(e) {
                    console.error("Error parsing JSON:", response);
                    Swal.fire('Error', 'Respuesta inesperada del servidor.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
            },
            complete: function() {
                $btn.prop("disabled", false);
            }
        });
    });
});
</script>