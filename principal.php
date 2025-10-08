<?php 
require_once('cnx_db.php');
if($_POST['traerhora']==1){
  echo date('Y-m-d H:i');
  exit();
}
require_once('globales.php'); 

require_once('validarloging.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $NOMBRE; ?></title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <link href="css/ui.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <link href="vendor/datatables/jquery.dataTables.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <link href="vendor/bootstrap/bootstrap-toggle-master/css/bootstrap-toggle.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <link href="css/sweetalert.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <link href="css/estilos.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <link href="css/bootstrap-select.css?d=<?php echo date('H:i:s');?>" rel="stylesheet">
  <style>
    #contenedorprincipal {
      height: calc(100% - 60px);
      overflow: auto;
    }
    input[type=number]::-webkit-outer-spin-button,

    input[type=number]::-webkit-inner-spin-button {

      -webkit-appearance: none;

      margin: 0;

    }

   

    input[type=number] {

      -moz-appearance:textfield;
      text-align: right;
    }
  </style>
  <!--Start of Tawk.to Script-->
  <script type="text/javascript">
  var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
  (function(){
  var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
  s1.async=true;
  s1.src='https://embed.tawk.to/646fbfe6ad80445890ef246b/1h1a8b6vm';
  s1.charset='UTF-8';
  s1.setAttribute('crossorigin','*');
  s0.parentNode.insertBefore(s1,s0);
  })();
  </script>
  <!--End of Tawk.to Script-->
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-light_2 sidebar accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="principal.php">
        <div class="sidebar-brand-text mx-3">Verificaci&oacute;n VC854</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Heading -->
      <div class="sidebar-heading" id="premenu">
        Menu
      </div>
      <?php include('menu.php'); ?>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <div id="nombrepagina">
              Inicio
            </div>

          <!--<div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" id="busqueda_menu" placeholder="Buscar Menu" aria-label="Buscar" aria-describedby="button-addon1">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="button-addon1" disabled><i class="fas fa-search fa-sm"></i></button>
            </div>
          </div>-->
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no_arrow">
              <a class="nav-link" href="#" id="Clock" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-dark small" id="idreloj"><?php echo date('Y-m-d H:i');?></span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="companyDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-danger small" id="nombreplazatop"></span>
                <i class="fas fa-building fa-lg fa-fw mr-2 text-gray-400"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="companyDropdown">
                <?php
                  $plaza = array();
                  $asignar = false;

                  if($_SESSION['CveUsuario']==1) {
                    $res = mysql_query("SELECT a.cve,a.numero, a.nombre FROM plazas a WHERE a.estatus!='I' ORDER BY a.lista, a.numero, a.nombre");
                  }
                  elseif($_SESSION['TipoUsuario']==1){
                    $res = mysql_query("SELECT a.cve,a.numero,a.nombre FROM plazas a WHERE a.estatus!='I' ORDER BY a.lista, a.numero, a.nombre");
                  }
                  else{
                    $res = mysql_query("SELECT a.cve,a.numero,a.nombre FROM plazas a INNER JOIN usuario_accesos b ON a.cve=b.plaza AND b.usuario='".$_SESSION['CveUsuario']."' AND b.acceso>0 WHERE a.estatus!='I' GROUP BY a.cve ORDER BY a.lista, a.numero, a.nombre");
                  }
                  
                  if(mysql_num_rows($res)==1){
                    $asignar = true;
                  }
                  while($row = mysql_fetch_assoc($res)){
                    if($asignar){
                      $plaza = $row;
                    }
                ?>
                  <span class="dropdown-item text-danger" onClick="cambiarPlaza(<?php echo $row['cve'];?>, $(this));" style="width:500px">
                    <?php echo $row['numero'].' '.utf8_encode($row['nombre']); ?>
                  </span>
                <?php
                  }
                ?>
              </div>
            </li>

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            
            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['NomUsuario'];?></span>
                <i class="fas fa-user fa-lg fa-fw mr-2 text-gray-400"></i>
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" onClick="menu('0', '52', 'cambiar_password.php', 'Cambiar Contraseña', '<?php echo $_SESSION['reg_sistema'];?>');">
                  <i class="fas fa-exclamation fa-sm fa-fw mr-2 text-gray-400"></i>
                  Cambiar Contraseña
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Salir
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->
        <div id="modalbusquedas" style="z-index: 1000000;" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Busqueda</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
              </div>
              <div class="modal-body" id="bodybusquedas">
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                 </div>
            </div>
          </div>
        </div>
        
        <!-- Begin Page Content -->
        <form id="formaprincipal" name="formaprincipal" method="POST" enctype="multipart/form-data">
          <div style="display:none;">
            <input type="hidden" name="cmd" value="">
            <input type="hidden" name="cvemenu" value="" id="cvemenu">
            <input type="hidden" name="reg" value="" id="reg">
            <input type="hidden" name="cveplaza" id="cveplaza" value="<?php echo $plaza['cve']; ?>">
            <input type="hidden" name="cveusuario" id="cveusuario" value="<?php echo $_SESSION['CveUsuario'];?>">
          </div>

          <div class="container-fluid" id="contenedorprincipal">

          
          </div>
          <input type="text" style="display:none;" id="nocargar" name="nocargar" value="">
        <!-- /.container-fluid -->
        </form>
      </div>
      <!-- End of Main Content -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">¿Seguro que desea salir?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Seleccione "Cerrar Sesión" si esta listo para cerrar la sesión.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-primary" href="logout.php?cveregistro=<?php echo $_SESSION['reg_sistema'];?>">Cerrar Sesión</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.js"></script>
  <script src="js/jquery-ui.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.js?d=<?php echo date('H:i:s');?>"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
  <script src="vendor/bootstrap/bootstrap-toggle-master/js/bootstrap-toggle.js?d=<?php echo date('H:i:s');?>"></script>
  <script src="js/funciones.js?d=<?php echo date('H:i:s');?>"></script>
  <script src="js/sweetalert.js?d=<?php echo date('H:i:s');?>"></script>
  <script src="js/bootstrap-select.js?d=<?php echo date('H:i:s');?>"></script>
  <script>
    $("#modalbusquedas").modal({
      backdrop: false,
      keyboard: false,
      show: false
    });

     <?php
      if($plaza['cve']>0){
    ?>
    $('#nombreplazatop').html('<?php echo utf8_encode($plaza['nombre']);?>');
    cambiarPlaza(<?php echo $plaza['cve'];?>,$('.navbar').find('.navbar-nav').find('.nav-item').find('.dropdown-menu').find('span'));
    <?php
      }
      else{
    ?>
    $('#nombreplazatop').html('Seleccione plaza');
    <?php
      }
    ?>

    function mueveReloj(){
      $.ajax({
        url: 'principal.php',
        type: "POST",
        data: {
          traerhora:1
        },
        success: function(data) {
          $('#idreloj').html(data);
        }
      });

      setTimeout("mueveReloj()",10000)
    }

    mueveReloj();

   
  </script>
</body>

</html>
