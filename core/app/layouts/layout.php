<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Facturocket - Sistema de Facturación SAT México">
    <meta name="author" content="Facturocket">
    <title>Facturocket - Facturación SAT México</title>
    <!-- Vendors styles-->
    <link rel="stylesheet" href="vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="css/vendors/simplebar.css">
    <!-- Main styles for this application-->
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/datatables/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/select2/select2.min.css">
    <script type="text/javascript" src="vendors/sweetalert/sweetalert2.all.min.js"></script>
  </head>
  <body>
    <?php if(isset($_SESSION["user_id"])):
      $curr_user = UserData::getById($_SESSION["user_id"]);
    ?>
    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
      <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
          <span class="sidebar-brand-full" style="font-size:22px; font-weight: bold;"><i class="bi bi-rocket-takeoff me-2"></i>FACTU<span class="text-primary">ROCKET</span></span>
          <span class="sidebar-brand-narrow">FR</span>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
      </div>
      <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
          <a class="nav-link" href="./">
            <i class="nav-icon bi bi-speedometer2"></i> Inicio
          </a>
        </li>

        <li class="nav-title">FACTURACIÓN</li>
        <li class="nav-item">
          <a class="nav-link" href="./?view=newinvoice">
            <i class="nav-icon bi bi-plus-circle"></i> Nueva Factura
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./?view=invoices&opt=all">
            <i class="nav-icon bi bi-file-earmark-text"></i> Historial Facturas
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./?view=clients&opt=all">
            <i class="nav-icon bi bi-people"></i> Clientes
          </a>
        </li>
       <!-- <li class="nav-item">
          <a class="nav-link" href="./?view=newpayment">
            <i class="nav-icon bi bi-cash-stack"></i> Registrar Pago
          </a>
        </li>
    -->
        <li class="nav-title">CATÁLOGOS</li>
        <li class="nav-item">
          <a class="nav-link" href="./?view=products&opt=all">
            <i class="nav-icon bi bi-box-seam"></i> Productos y Serv.
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./?view=categories&opt=all">
            <i class="nav-icon bi bi-tags"></i> Categorías
          </a>
        </li>

        <li class="nav-group">
          <a class="nav-link nav-group-toggle" href="#">
            <i class="nav-icon bi bi-gear"></i> Catálogos SAT
          </a>
          <ul class="nav-group-items compact">
            <li class="nav-item"><a class="nav-link" href="./?view=taxregimes&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Regímenes Fiscales</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=cfdiuses&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Usos de CFDI</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=paymentmethods&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Métodos de Pago</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=paymentforms&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Formas de Pago</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=units&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Unidades de Medida</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=producttypes&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Claves SAT (Prod/Serv)</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=taxobjects&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Objetos de Impuesto</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=taxes&opt=all"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Impuestos</a></li>
          </ul>
        </li>

        <li class="nav-title">SISTEMA</li>
        <li class="nav-item">
          <a class="nav-link" href="./?view=settings">
            <i class="nav-icon bi bi-building"></i> Datos del Emisor
          </a>
        </li>
        <?php if($curr_user->type==1):?>
        <li class="nav-item">
          <a class="nav-link" href="./?view=users&opt=all">
            <i class="nav-icon bi bi-person-gear"></i> Usuarios
          </a>
        </li>
        <?php endif; ?>
      </ul>
      <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
      </div>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
      <header class="header header-sticky p-0 mb-4 shadow-sm">
        <div class="container-fluid border-bottom px-4">
          <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
            <i class="bi bi-list fs-3"></i>
          </button>
          
          <ul class="header-nav ms-auto">
          </ul>
          <ul class="header-nav">
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item dropdown"><a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold">
                  <?php echo substr($curr_user->name ?? '',0,1).substr($curr_user->lastname ?? '',0,1); ?>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end pt-0 shadow border-0">
                <div class="dropdown-header bg-light text-body-secondary fw-semibold rounded-top mb-2">Mi Cuenta</div>
                <div class="px-3 py-2">
                  <div class="fw-bold"><?php echo $curr_user->name." ".$curr_user->lastname; ?></div>
                  <div class="small text-muted">Administrador</div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="./?action=processlogout">
                  <i class="bi bi-box-arrow-right me-2 text-danger"></i> Cerrar sesión
                </a>
              </div>
            </li>
          </ul>
        </div>
      </header>
      <div class="body flex-grow-1">
        <div class="container-fluid px-4">
          <?php View::load("index"); ?>
        </div>
      </div>
      <footer class="footer px-4 border-top-0 bg-transparent text-muted small">
        <div>Facturocket © 2026.</div>
        <div class="ms-auto">v1.0 SAT 4.0</div>
      </footer>
    </div>
    <?php else:?>
    <div class="bg-light min-vh-100 d-flex flex-row align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-5">
            <div class="card shadow-lg border-0">
              <div class="card-body p-5">
                <div class="text-center mb-4">
                  <div class="display-1 text-primary mb-2"><i class="bi bi-rocket-takeoff-fill"></i></div>
                  <h1 class="h3 fw-bold">Facturocket</h1>
                  <p class="text-muted">Sistema de Facturación SAT México</p>
                </div>
                <form method="post" action="./?action=access&opt=login">
                  <div class="mb-3">
                    <label class="form-label fw-bold">Usuario</label>
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
                      <input class="form-control border-start-0" name="email" required type="text" placeholder="Tu correo electrónico">
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="form-label fw-bold">Contraseña</label>
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                      <input class="form-control border-start-0" name="password" required type="password" placeholder="Tu contraseña">
                    </div>
                  </div>
                  <div class="d-grid mb-3">
                    <button class="btn btn-primary btn-lg shadow-sm fw-bold" type="submit">Acceder</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <!-- CoreUI and necessary plugins-->
    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>
    <script src="vendors/datatables/datatables.min.js"></script>
    <script src="vendors/select2/select2.full.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(".datatable").DataTable({
          "responsive": true,
          "language": {
            "url": "./vendors/datatables/esmx.json"
          }
        });

        // Initialize Select2 globally
        if ($.fn.select2) {
          $('.select2').each(function() {
            $(this).select2({
              width: '100%',
              dropdownParent: $(this).parent()
            });
          });
        }

        // SweetAlert from Session
        <?php if(isset($_SESSION["sweetalert"])): 
          $icon = isset($_SESSION["sweetalert_icon"]) ? $_SESSION["sweetalert_icon"] : 'info';
          $title = 'Notificación';
          if($icon == 'success') $title = '¡Éxito!';
          if($icon == 'error') $title = 'Error';
          if($icon == 'warning') $title = 'Atención';
        ?>
          console.log("Facturocket Debug:", <?php echo json_encode($_SESSION["sweetalert"]); ?>);
          Swal.fire({
            title: '<?php echo $title; ?>',
            text: '<?php echo $_SESSION["sweetalert"]; ?>',
            icon: '<?php echo $icon; ?>',
            confirmButtonText: 'Aceptar',
            timer: 4000,
            timerProgressBar: true,
            confirmButtonColor: '#5856d6'
          });
          <?php 
            unset($_SESSION["sweetalert"]); 
            unset($_SESSION["sweetalert_icon"]); 
          ?>
        <?php endif; ?>
      });
    </script>
  </body>
</html>