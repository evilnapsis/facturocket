<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="container-fluid text-start">
<div class="d-flex align-items-center justify-content-between mb-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">Usuarios del Sistema</h2>
        <p class="text-muted">Gestione el acceso de los empleados y mecánicos al taller.</p>
    </div>
</div>

<div class="mb-4">
    <a href="./?view=users&opt=new" class="btn btn-primary fw-bold px-3 shadow-sm"><i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario</a>
</div>

<div class="row">
	<div class="col-md-12">
<div class="card mb-4 border-0 shadow-sm">
  <div class="card-body p-4">
		<?php
		$users = UserData::getAll();
		if(count($users)>0){
			?>
			<table class="table table-bordered table-hover datatable align-middle">
			<thead class="table-light">
			<th>Nombre completo</th>
			<th>Email</th>
      <th>Tipo de Acceso</th>
      <th>Estado</th>
			<th style="width:100px;">Acciones</th>
			</thead>
			<?php
			foreach($users as $user):
				?>
				<tr>
				<td><span class="fw-bold"><?php echo $user->name." ".$user->lastname; ?></span></td>
				<td><?php echo $user->email; ?></td>
        <td>
          <?php 
          if($user->type == 1){
            echo "<span class='badge bg-primary shadow-sm'><i class='bi bi-shield-lock me-1'></i> Administrador</span>";
          }else if($user->type == 2){
            echo "<span class='badge bg-info shadow-sm'><i class='bi bi-person-badge me-1'></i> Mecánico</span>";
          }else{
            echo "<span class='badge bg-secondary shadow-sm'><i class='bi bi-person me-1'></i> Cliente</span>";
          }
          ?>
        </td>
        <td class="text-center">
          <?php if($user->is_active==1): ?>
            <span class="badge bg-success shadow-sm"><i class="bi bi-check-circle me-1"></i> Activo</span>
          <?php else: ?>
            <span class="badge bg-danger shadow-sm"><i class="bi bi-x-circle me-1"></i> Inactivo</span>
          <?php endif; ?>
        </td>
				<td class="text-center">
				<a href="index.php?view=users&opt=edit&id=<?php echo $user->id;?>" class="btn btn-warning btn-sm text-white" title="Editar"><i class="bi bi-pencil-square"></i></a>
				<a id="deluser-<?php echo $user->id;?>" class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash"></i></a>
<script type="text/javascript">
    $("#deluser-<?php echo $user->id?>").click(function(){
Swal.fire({
  title: "¿Estas seguro?",
  text: "Deseas eliminar este usuario y revocar todos sus accesos.",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#d33',
  cancelButtonColor: '#6c757d',
  confirmButtonText: "Sí, Eliminar",
  cancelButtonText: "Cancelar"
}).then((result) => {
  if (result.isConfirmed) {
    window.location = "./?action=users&opt=del&id=<?=$user->id;?>";
  }
});
    });
</script>
				</td>
				</tr>
				<?php
			endforeach; ?>
</table>
<?php		}else{
			?>
      <div class="alert alert-info mb-0">
        <i class="bi bi-info-circle me-2"></i> No hay usuarios registrados.
      </div>
			<?php
		}
		?>
	</div>
</div>
	</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="container-fluid text-start">
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Nuevo Usuario</h2>
    <p class="text-muted">Designe un nuevo integrante para su taller y asigne sus permisos correspondientes.</p>
</div>

<div class="row">
	<div class="col-md-12">
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
<form class="form-horizontal" method="post" id="addproduct" action="index.php?action=users&opt=add" role="form">
  <div class="row g-3">
    <div class="col-md-6">
      <label for="name" class="form-label fw-bold">Nombre*</label>
      <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre">
    </div>
    <div class="col-md-6">
      <label for="lastname" class="form-label fw-bold fw-bold">Apellido*</label>
      <input type="text" name="lastname" required class="form-control" id="lastname" placeholder="Apellido">
    </div>
    <div class="col-md-6">
      <label for="email" class="form-label fw-bold">Email*</label>
      <input type="email" name="email" required class="form-control" id="email" placeholder="Correo electrónico">
    </div>
    <div class="col-md-6">
      <label for="password" class="form-label fw-bold">Contraseña*</label>
      <input type="password" name="password" required class="form-control" id="password" placeholder="Contraseña">
    </div>
    <div class="col-md-12">
      <label for="type" class="form-label fw-bold">Tipo de Acceso*</label>
      <select name="type" class="form-select select2" required>
        <option value="">-- SELECCIONE --</option>
        <option value="1">Administrador</option>
        <option value="2">Mecánico</option>
        <option value="3">Cliente</option>
      </select>
    </div>
  </div>
  <p class="text-muted small">* Campos obligatorios</p>
  <div class="mt-4">
    <button type="submit" class="btn btn-info text-white fw-bold"><i class="bi bi-check-lg me-1"></i> Agregar Usuario</button>
    <a href="./?view=users&opt=all" class="btn btn-secondary me-2"><i class="bi bi-arrow-left-short me-1"></i> Cancelar</a>
  </div>
</form>
</div>
</div>
</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<section class="container-fluid text-start">
<?php $user = UserData::getById($_GET["id"]);?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Editar Usuario</h2>
    <p class="text-muted">Actualice el perfil y permisos de acceso del usuario seleccionado.</p>
</div>

<div class="row">
	<div class="col-md-12">
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
		<form class="form-horizontal" method="post" id="addproduct" action="index.php?action=users&opt=upd" role="form">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="name" class="form-label fw-bold">Nombre*</label>
          <input type="text" name="name" value="<?php echo $user->name;?>" required class="form-control" id="name" placeholder="Nombre">
        </div>
        <div class="col-md-6">
          <label for="lastname" class="form-label fw-bold">Apellido*</label>
          <input type="text" name="lastname" value="<?php echo $user->lastname;?>" required class="form-control" id="lastname" placeholder="Apellido">
        </div>
        <div class="col-md-6">
          <label for="email" class="form-label fw-bold">Email*</label>
          <input type="email" name="email" value="<?php echo $user->email;?>" required class="form-control" id="email" placeholder="Email">
        </div>
        <div class="col-md-6">
          <label for="password" class="form-label fw-bold">Contraseña</label>
          <input type="password" name="password" class="form-control" id="password" placeholder="Nueva contraseña (dejar vacío para no cambiar)">
          <small class="text-muted">Si no desea cambiarla, deje este campo vacío.</small>
        </div>
        <div class="col-md-6">
          <label for="type" class="form-label fw-bold">Tipo de Acceso*</label>
          <select name="type" class="form-select select2" required>
            <option value="1" <?php if($user->type==1){ echo "selected"; } ?>>Administrador</option>
            <option value="2" <?php if($user->type==2){ echo "selected"; } ?>>Mecánico</option>
            <option value="3" <?php if($user->type==3){ echo "selected"; } ?>>Cliente</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="is_active" class="form-label fw-bold">Estado*</label>
          <select name="is_active" class="form-select select2" required>
            <option value="1" <?php if($user->is_active==1){ echo "selected"; } ?>>Activo</option>
            <option value="0" <?php if($user->is_active==0){ echo "selected"; } ?>>Inactivo</option>
          </select>
        </div>
      </div>
      <p class="text-muted small">* Campos obligatorios</p>
      <div class="mt-4">
        <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
        <button type="submit" class="btn btn-success text-white fw-bold"><i class="bi bi-check-lg me-1"></i> Actualizar Usuario</button>
        <a href="./?view=users&opt=all" class="btn btn-secondary me-2"><i class="bi bi-arrow-left-short me-1"></i> Cancelar</a>
      </div>
    </form>
	</div>
</div>
</div>
</div>
</section>
<?php endif; ?>