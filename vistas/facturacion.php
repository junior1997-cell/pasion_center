<?php
//Activamos el almacenamiento en el buffer
ob_start();
date_default_timezone_set('America/Lima'); require "../config/funcion_general.php";
session_start(); 
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" style="--primary-rgb: 78, 172, 76;" data-bg-img="bgimg4" data-menu-styles="dark" data-toggled="icon-overlay-close">

  <head>
    <?php $title_page = "Emitir Comprobante";  include("template/head.php"); ?>

    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">

    <style>
      #tabla-facturacion-detalle td {        
        vertical-align: middle !important;
        line-height: 1.462 !important;
        font-size: .6875rem !important;
        font-weight: 50 !important;
      }
      #tabla-ventas_filter label{ width: 100% !important; }
      #tabla-ventas_filter label input{ width: 100% !important; }
    </style>
  </head>

  <body id="body-ventas" idusuario="<?php echo $_SESSION['idusuario'];?>" idpersona="<?php echo $_SESSION['idpersona'];?>" idpersona_trabajador="<?php echo $_SESSION['idpersona_trabajador'];?>" >
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['facturacion']==1) { ?>
      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">

          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_venta(); " style="display: none;" > <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"  > <i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0">Facturación</p>
                  <span class="fs-semibold text-muted">Administra tus comprobantes de pago.</span>
                </div>
              </div>
            </div>
            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Realizar cobro</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Facturación</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->
          <div class="row">     
            
            <!-- TABLA - FACTURA -->
            <div class="col-xl-9" id="div-tabla">
              <div class="card custom-card">
                <div class="card-header justify-content-between">
                  
                    <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_fecha_i" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_i();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                          Fecha Inicio</label>
                        <input type="date" class="form-control" name="filtro_fecha_i" id="filtro_fecha_i" value="<?php echo date("Y-m-d");?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">                        
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_fecha_f" class="form-label">
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_fecha_f();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                          Fecha Fin</label>
                        <input type="date" class="form-control" name="filtro_fecha_f" id="filtro_fecha_f" value="<?php echo date("Y-m-d");?>" onchange="cargando_search(); delay(function(){filtros()}, 50 );">                        
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                      <div class="form-group">
                        <label for="filtro_cliente" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Cliente
                          <span class="charge_filtro_cliente"></span>
                        </label>
                        <select class="form-control" name="filtro_cliente" id="filtro_cliente" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_comprobante" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_comprobante();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Comprobante
                          <span class="charge_filtro_comprobante"></span>
                        </label>
                        <select class="form-control" name="filtro_comprobante" id="filtro_comprobante" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                 
                  <div class="d-flex"> 
                    <div class="dropdown ms-2">
                      <button class="btn btn-icon btn-secondary-light btn-sm btn-wave waves-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu otros-filtros">                        
                        <li><a class="dropdown-item o-f-ac" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('ACEPTADA', '.o-f-ac')" ><i class="ri-check-fill align-middle me-1"></i> Solo aceptados</a></li>
                        <li><a class="dropdown-item o-f-an" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('ANULADO', '.o-f-an')" ><i class="ri-close-fill align-middle me-1"></i> Solo anulados</a></li>
                        <li><a class="dropdown-item o-f-to active" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat('', '.o-f-to')" ><i class="bi bi-border-all align-middle me-1"></i> Todos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="view_mas_detalle();"><i class="bi bi-list-check"></i> Ver mas detalles</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-ventas">
                      <thead>
                        <tr>
                          <th class="text-center"><center>#</center></th>
                          <th class="text-center"><center>OP</center></th>
                          <th class="text-center"><center>ID</center></th>
                          <th>Creación</th>
                          <th>Periodo</th>
                          <th>Cliente</th>
                          <th>Correlativo</th>
                          <th>Total</th> 
                          <th><center>SUNAT</center></th>   
                          <th>Boucher</th>      
                          <th><center>Estado</center></th>               
                        </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                        <tr>
                        <th class="text-center"><center>#</center></th>
                          <th class="text-center"><center>OP</center></th>
                          <th class="text-center"><center>ID</center></th>
                          <th>Creación</th>
                          <th>Periodo</th>
                          <th>Cliente</th>
                          <th>Correlativo</th>
                          <th>Total</th>
                          <th class="text-center" ><center>SUNAT</center></th>
                          <th>Boucher</th>   
                          <th><center>Estado</center></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>                
              </div>
            </div>

            <!-- REPORTE- MINI -->
            <div class="col-xl-3" id="div-mini-reporte"  >

              <div class="" >
                
                <div class="row">

                  <div class="mb-2 col-xxl-12 col-xl-12 col-lg-12 col-md-12 bd-blue-100 border br-5" >
                    <div class=" my-2 d-flex align-items-center justify-content-between">
                      <h6 class="fw-semibold mb-0">Top 5 productos:</h6>
                      <div>
                        <button class="btn btn-sm btn-primary-light btn-wave waves-effect waves-light border-success">Ver mas</button>
                      </div>
                    </div>  
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="filtro-periodo" class="fs-10">Periodo Emitido</label>                     
                      <input type="month" class="form-control form-control-primary form-control-sm" id="filtro-periodo" value="<?php echo date('Y-m'); ?>" onchange=" mini_reporte()" >
                    </div>     
                  </div>                  

                  <div class="col-lg-6" >
                    <div class="form-group">
                      <label for="filtro-trabajador" class="fs-10">Trabajador</label>
                      <select class="form-select form-control-primary text-primary form-select-sm mb-3" aria-label=".form-select-sm example" id="filtro-trabajador" onchange=" mini_reporte()">
                        <option selected="">Buscando datos...</option>                     
                      </select>
                    </div>  
                  </div>

                  <div class="col-lg-12 mt-2">
                    <div class="row">                      

                      <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12" id="html_top_5_productos" >
                      </div> 

                    </div>
                  </div>
                  
                </div>                
              </div>

              <div class="card custom-card" >
                <div class="card-body p-0">                  
                  <div class="p-4">
                    <p class="fs-15 fw-semibold">Mini reporte <span class="text-muted fw-normal">(Últimos 6 meses) :</span></p>
                    <div id="invoice-list-stats"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- FORMULARIO -->
            <div class="col-xl-12" id="div-formulario"  style="display: none;">              
              <div class="card custom-card">
                <div class="card-body">                    
                  
                  <!-- FORM - COMPROBANTE -->                    
                  <form name="form-facturacion" id="form-facturacion" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="row" id="cargando-1-formulario">

                      <!-- IMPUESTO -->
                      <input type="hidden" name="f_idventa" id="f_idventa" />
                      <!-- IMPUESTO -->
                      <input type="hidden" class="form-control" name="f_impuesto" id="f_impuesto" value="0">   
                      <!-- TIPO DOC -->
                      <input type="hidden" class="form-control" name="f_tipo_documento" id="f_tipo_documento" value="0">  
                      <!-- NUMERO DOC -->
                      <input type="hidden" class="form-control" name="f_numero_documento" id="f_numero_documento" value="0">  
                      <!-- ID VENTA PARA: NOTA DE CREDITO -->                   
                      <input type="hidden" class="form-control" name="f_nc_idventa" id="f_nc_idventa" value="0">                     

                      <div class="col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="row gy-3">
                          <!-- ENVIO AUTOMATICO -->
                          <div class="col-md-12 col-lg-4 col-xl-5 col-xxl-5 px-0">
                            <div class="custom-toggle-switch d-flex align-items-center mb-1">
                              <input id="f_crear_y_emitir" name="f_crear_y_emitir" type="checkbox" checked="" value="SI">
                              <label for="f_crear_y_emitir" class="label-warning"></label><span class="ms-3 fs-11">SUNAT</span>
                            </div>
                          </div>
                          <!-- CREAR Y MOSTRAR-->
                          <div class="col-md-12 col-lg-4 col-xl-5 col-xxl-5 px-0">
                            <div class="custom-toggle-switch d-flex align-items-center mb-1">
                              <input id="f_crear_y_mostrar" name="f_crear_y_mostrar" type="checkbox" checked="" value="SI">
                              <label for="f_crear_y_mostrar" class="label-warning"></label><span class="ms-3 fs-11">Crear y mostrar</span>
                            </div>
                          </div>
                          <!--  TIPO COMPROBANTE  -->
                          <div class="col-md-12 col-lg-8 col-xl-8 col-xxl-8"> 
                            <div class="mb-sm-0 mb-2">
                              <p class="fs-14 mb-2 fw-semibold">Tipo de comprobante</p>
                              <div class="mb-0 authentication-btn-group">
                                <input type="hidden" id="f_tipo_comprobante_hidden" value="12">
                                <input type="hidden" name="f_idsunat_c01" id="f_idsunat_c01" value="12">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                  
                                  <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante12" value="12" onchange="modificarSubtotales(); ver_series_comprobante('#f_tipo_comprobante12'); es_valido_cliente();">
                                  <label class="btn btn-sm btn-outline-primary btn-tiket" for="f_tipo_comprobante12"><i class='bx bx-file-blank me-1 align-middle d-inline-block'></i> Ticket</label>
                                  
                                  <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante03" value="03"  onchange="modificarSubtotales(); ver_series_comprobante('#f_tipo_comprobante03'); es_valido_cliente();">
                                  <label class="btn btn-sm btn-outline-primary btn-boleta" for="f_tipo_comprobante03"><i class="ri-article-line me-1 align-middle d-inline-block"></i>Boleta</label>
                                  
                                  <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante01" value="01" onchange="modificarSubtotales(); ver_series_comprobante('#f_tipo_comprobante01'); es_valido_cliente();">
                                  <label class="btn btn-sm btn-outline-primary" for="f_tipo_comprobante01"><i class="ri-article-line me-1 align-middle d-inline-block"></i> Factura</label>
                                  
                                  <input type="radio" class="btn-check" name="f_tipo_comprobante" id="f_tipo_comprobante07" value="07" onchange="modificarSubtotales(); ver_series_comprobante('#f_tipo_comprobante07'); ">
                                  <label class="btn btn-sm btn-outline-primary" for="f_tipo_comprobante07" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Nota de Crédito."><i class="bi bi-file-earmark-x-fill me-1 align-middle d-inline-block"></i> NC</label>

                                </div>
                              </div>
                            </div>                            
                          </div>    
                          
                          <div class="col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                            <div class="form-group">
                              <label for="f_serie_comprobante" class="form-label">Serie <span class="f_charge_serie_comprobante"></span></label>
                              <select class="form-control" name="f_serie_comprobante" id="f_serie_comprobante"></select>
                            </div>
                          </div>

                          <!--  PROVEEDOR  -->
                          <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 div_idpersona_cliente">
                            <div class="form-group">
                              <label for="f_idpersona_cliente" class="form-label">
                                <!-- <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_trabajador(); limpiar_proveedor();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_idpersona_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Cliente
                                <span class="charge_f_idpersona_cliente"></span>
                              </label>
                              <select class="form-control" name="f_idpersona_cliente" id="f_idpersona_cliente" onchange="es_valido_cliente(); usar_anticipo_valid();"></select>
                            </div>
                          </div> 

                          <!--  NC - TIPO DE COMPROBANTE  -->
                          <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 div_nc_tipo_comprobante">
                            <div class="form-group">
                              <label for="f_nc_tipo_comprobante" class="form-label"> Comprobante a anular</label>
                              <select class="form-control" name="f_nc_tipo_comprobante" id="f_nc_tipo_comprobante" onchange="buscar_comprobante_anular();">
                                <option value="01">FACTURA</option>
                                <option value="03">BOLETA DE VENTA</option>
                              </select>
                            </div>
                          </div>
                          
                          <!--  NC - SERIE Y NUMERO COMPROBANTE  -->
                          <div class="col-md-12 col-lg-6 col-xl-6 col-xxl-6 div_nc_serie_y_numero">
                            <div class="form-group">
                              <label for="f_nc_serie_y_numero" class="form-label">
                                <!-- <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_trabajador(); limpiar_proveedor();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_nc_serie_y_numero();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Series y número
                                <span class="charge_f_nc_serie_y_numero"></span>
                              </label>
                              <select class="form-control" name="f_nc_serie_y_numero" id="f_nc_serie_y_numero" onchange="mostrar_para_nota_credito(this);"></select>
                            </div>
                          </div>

                          <!--  NC - SERIE Y NUMERO COMPROBANTE  -->
                          <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 div_nc_motivo_anulacion">
                            <div class="form-group">
                              <label for="f_nc_motivo_anulacion" class="form-label">
                                <!-- <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_trabajador(); limpiar_proveedor();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_nc_motivo_anulacion();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Motivo
                                <span class="charge_f_nc_motivo_anulacion"></span>
                              </label>
                              <select class="form-control" name="f_nc_motivo_anulacion" id="f_nc_motivo_anulacion" ></select>
                            </div>
                          </div>
                          
                          <!-- DESCRIPCION -->
                          <div class="col-md-6 col-lg-12 col-xl-12 col-xxl-12">
                            <div class="form-group">
                              <label for="f_observacion_documento" class="form-label">Observacion</label>
                              <textarea name="f_observacion_documento" id="f_observacion_documento" class="form-control" rows="2" placeholder="ejemp: Cobro de servicio de internet."></textarea>
                            </div>
                          </div> 
                          
                          <!-- FECHA EMISION -->
                          <!-- <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 div_es_cobro">
                            <div class="form-group">
                              <label for="f_es_cobro_inp" class="form-label">Es cobro?</label>
                              <div class="toggle toggle-secondary on es_cobro" onclick="delay(function(){es_cobro_valid()}, 100 );" >  <span></span>   </div>
                              <input type="hidden" class="form-control" name="f_es_cobro_inp" id="f_es_cobro_inp" value="SI" >
                            </div>
                          </div>  

                          <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 datos-de-cobro-mensual">
                            <div class="form-group">
                              <label for="f_periodo_pago" class="form-label">Periodo Pago <span class="text-success fs-10 bx-flashing span_dia_cancelacion"></span></label>
                              <input type="month" class="form-control" name="f_periodo_pago" id="f_periodo_pago" >
                            </div>
                          </div>                                                                   -->

                        </div>
                      </div>

                      <div class="col-md-12 col-lg-8 col-xl-8 col-xxl-8">
                        <div class="row" id="cargando-3-formulario">

                          <div class="col-6 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2 mt-xs-3 div_agregar_producto">
                            <button class="btn btn-info label-btn m-r-10px" type="button" onclick="listar_tabla_producto('PR');"  >
                              <i class="ri-add-circle-line label-btn-icon me-2"></i> Producto
                            </button>
                          </div>
                          <div class="col-6 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-3 mt-xs-3 div_agregar_producto">
                            <button class="btn btn-primary label-btn m-r-10px" type="button"  onclick="listar_tabla_producto('SR');"  >
                            <i class="ri-add-fill label-btn-icon me-2"></i> Servicio <span class="font-size-10px">(Planes)</span>
                            </button>
                          </div>  

                          <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5 col-xxl-5 mt-xs-3 div_agregar_producto">
                            <div class="input-group">                              
                              <button type="button" class="input-group-text buscar_x_code" onclick="listar_producto_x_codigo();"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Buscar por codigo de producto."><i class='bx bx-search-alt'></i></button>
                              <input type="text" name="codigob" id="codigob" class="form-control" onkeyup="mayus(this);" placeholder="Digite el código de producto." >
                            </div>
                          </div>                                              

                          <!-- ------- TABLA PRODUCTOS SELECCIONADOS ------ --> 
                          <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive pt-3">
                            <table id="tabla-productos-seleccionados" class="table table-striped table-bordered table-condensed table-hover">
                              <thead class="bg-color-dark text-white">
                                <th class="font-size-11px py-1" data-toggle="tooltip" data-original-title="Opciones">Op.</th>
                                <th class="font-size-11px py-1">Cod</th> 
                                <th class="font-size-11px py-1">Producto</th>
                                <th class="font-size-11px py-1">Unidad</th>
                                <th class="font-size-11px py-1">Periodo</th>
                                <th class="font-size-11px py-1">Cantidad</th>                                        
                                <th class="font-size-11px py-1" data-toggle="tooltip" data-original-title="Precio Unitario">P/U</th>
                                <th class="font-size-11px py-1">Descuento</th>
                                <th class="font-size-11px py-1">Subtotal</th>
                                <th class="font-size-11px py-1 text-center" ><i class='bx bx-cog fs-4'></i></th>
                              </thead>
                              <tbody ></tbody>
                              <tfoot>
                                <td colspan="6"></td>

                                <th class="text-right">
                                  <h6 class="fs-11 f_tipo_gravada">SUBTOTAL</h6>
                                  <h6 class="fs-11 ">DESCUENTO</h6>
                                  <h6 class="fs-11 val_igv">IGV (18%)</h6>
                                  <h5 class="fs-13 font-weight-bold">TOTAL</h5>
                                </th>
                                <th class="text-right"> 
                                  <h6 class="fs-11 font-weight-bold d-flex justify-content-between f_venta_subtotal"> <span>S/</span>  0.00</h6>
                                  <input type="hidden" name="f_venta_subtotal" id="f_venta_subtotal" />
                                  <input type="hidden" name="f_tipo_gravada" id="f_tipo_gravada" />

                                  <h6 class="fs-11 font-weight-bold d-flex justify-content-between f_venta_descuento"><span>S/</span> 0.00</h6>
                                  <input type="hidden" name="f_venta_descuento" id="f_venta_descuento" />

                                  <h6 class="fs-11 font-weight-bold d-flex justify-content-between f_venta_igv"><span>S/</span> 0.00</h6>
                                  <input type="hidden" name="f_venta_igv" id="f_venta_igv" />
                                  
                                  <h5 class="fs-13 font-weight-bold d-flex justify-content-between f_venta_total"><span>S/</span> 0.00</h5>
                                  <input type="hidden" name="f_venta_total" id="f_venta_total" />
                                  
                                </th>
                                <th></th>
                              </tfoot>
                            </table>
                          </div>

                          <div class="col-12 pt-3 div_pago_rapido">
                            <button type="button" class="btn btn-primary btn-sm pago_rapido" onclick="pago_rapido(this)" >0</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="pago_rapido(this)" >10</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="pago_rapido(this)" >20</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="pago_rapido(this)" >50</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="pago_rapido(this)" >100</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="pago_rapido(this)" >200</button>
                          </div>

                          <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 div_m_pagos">
                            <div class="row">

                              <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3">
                                <div class="form-group">
                                  <label for="f_metodo_pago" class="form-label">
                                    <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_f_metodo_pago();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                    Método de pago
                                    <span class="charge_f_metodo_pago"></span>
                                  </label>
                                  <select class="form-control" name="f_metodo_pago" id="f_metodo_pago" onchange="capturar_pago_venta();">
                                    <option value="EFECTIVO" selected >EFECTIVO</option>
                                    <option value="MIXTO">MIXTO</option>
                                    <option value="CAJA HUANCAYO">CAJA HUANCAYO</option>
                                    <option value="CAJA PIURA">CAJA PIURA</option>
                                    <option value="BANCO DE LA NACIÓN">BANCO DE LA NACIÓN</option>
                                    <option value="COOPACT">COOPACT</option>
                                    <option value="BBVA CONTINENTAL">BBVA CONTINENTAL</option>
                                    <option value="BCP">BCP</option>
                                    <option value="YAPE">YAPE</option>
                                    <option value="PLIN">PLIN</option>
                                    <option value="CULQI">CULQI</option>                                                      
                                    <option value="LUKITA">LUKITA</option>                                                      
                                    <option value="TUNKI">TUNKI</option>                                
                                  </select>                              
                                </div>
                              </div> 
                              
                              <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3">
                                <div class="form-group">
                                  <label for="f_total_recibido" class="form-label">Monto a pagar</label>
                                  <input type="number" name="f_total_recibido" id="f_total_recibido" class="form-control"  onClick="this.select();" onchange="calcular_vuelto();" onkeyup="calcular_vuelto();"  placeholder="Ingrese monto a pagar." >                           
                                </div>
                              </div> 

                              <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3" id="content-mp-monto" style="display: none;">
                                <div class="form-group">
                                  <label for="f_mp_monto" class="form-label">Monto: <span class="span-tipo-pago"></span></label>
                                  <input type="number" name="f_mp_monto" id="f_mp_monto" class="form-control" onClick="this.select();" onchange="calcular_vuelto();" onkeyup="calcular_vuelto();" placeholder="Pagar con" />
                                </div>
                              </div>

                              <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-3 pt-3">
                                <div class="form-group">
                                  <label for="f_total_vuelto" class="form-label">Vuelto <small class="falta_o_completo"></small></label>
                                  <input type="number" name="f_total_vuelto" id="f_total_vuelto" class="form-control-plaintext px-2 f_total_vuelto" readonly placeholder="Ingrese monto a pagar." >                           
                                </div>
                              </div> 

                            </div>
                          </div>                          

                          <!-- USAR SALDO -->
                          <div class="col-md-12 col-lg-3 col-xl-3 col-xxl-3 pt-3 div_usar_anticipo">
                            <div class="form-group">
                              <label for="f_usar_anticipo" class="form-label">Usar anticipos?</label>
                              <div class="toggle toggle-secondary f_usar_anticipo" onclick="delay(function(){usar_anticipo_valid()}, 100 );" >  <span></span>   </div>
                              <input type="hidden" class="form-control" name="f_usar_anticipo" id="f_usar_anticipo" value="NO" >
                            </div>
                          </div>                           

                          <div class="col-md-12 col-lg-9 col-xl-9 col-xxl-9 pt-3 datos-de-saldo" style="display: none !important;">

                            <div class="row">    
                              <div class="col-12 pl-0">
                                <div class="text-primary p-l-10px" style="position: relative; top: 7px;"><label class="bg-white" for=""><b>DATOS DE ANTICIPOS</b></label></div>
                              </div>
                            </div>

                            <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                              <div class="row ">                                                                
                                
                                <!-- SALDO -->
                                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="f_ua_monto_disponible" class="form-label">Saldo Disponible</label>
                                    <input type="number" class="form-control-plaintext" name="f_ua_monto_disponible" id="f_ua_monto_disponible" readonly>
                                  </div>
                                </div> 

                                <!-- Saldo Usar -->
                                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                  <div class="form-group">
                                    <label for="f_ua_monto_usado" class="form-label">Saldo Usar</label>
                                    <input type="number" class="form-control" name="f_ua_monto_usado" id="f_ua_monto_usado" >
                                  </div>
                                </div>       

                              </div>
                            </div>
                          </div>  

                          <div class="col-12" id="content-metodo-pago">
                            <div class="row">
                              <!-- Código de Baucher -->
                              <div class="col-sm-6 col-lg-6 col-xl-6 pt-3" >
                                <div class="form-group">
                                  <label for="f_mp_serie_comprobante">Código de Baucher <span class="span-code-baucher-pago"></span> </label>
                                  <input type="text" name="f_mp_serie_comprobante" id="f_mp_serie_comprobante" class="form-control" onClick="this.select();" placeholder="Codigo de baucher" />
                                </div>
                              </div>  
                              <!-- Baucher -->
                              <div class="col-sm-6 col-lg-6 col-xl-6 pt-3" >
                                <div class="form-group">                              
                                  <input type="file" class="multiple-filepond" name="f_mp_comprobante" id="f_mp_comprobante" data-allow-reorder="true" data-max-file-size="3MB" data-max-files="6" accept="image/*, application/pdf" >                             
                                  <input type="hidden" name="f_mp_comprobante_old" id="f_mp_comprobante_old">
                                </div>
                              </div>
                            </div>
                          </div>                          
                        </div>
                        <!-- ::::::::::: CARGANDO ... :::::::: -->
                        <div class="row" id="cargando-4-fomulario" style="display: none;" >
                          <div class="col-lg-12 mt-5 text-center">                         
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div>
                      </div>

                    </div>  
                    
                    <!-- ::::::::::: CARGANDO ... :::::::: -->
                    <div class="row" id="cargando-2-fomulario" style="display: none;" >
                      <div class="col-lg-12 mt-5 text-center">                         
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>

                    <!-- Chargue -->
                    <div class="p-l-25px col-lg-12" id="barra_progress_venta_div" style="display: none;" >
                      <div  class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 
                        <div id="barra_progress_venta" class="progress-bar" style="width: 0%"> <div class="progress-bar-value">0%</div> </div> 
                      </div>
                    </div>
                    <!-- Submit -->
                    <button type="submit" style="display: none;" id="submit-form-venta">Submit</button>
                  </form>                                  

                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1); limpiar_form_venta();" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button type="button" class="btn btn-success btn-guardar" id="guardar_registro_venta" style="display: none;"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>              
            </div>

            <!-- TABLA - MAS DETALLES FACTURA -->
            <div class="col-xl-12" id="div-tabla-mas-detalles" style="display: none;">
              <div class="card custom-card">
                <div class="card-header justify-content-between">
                  
                    <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_md_fecha_i" class="form-label">
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_md_fecha_i();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                          Fecha Inicio</label>
                        <input type="date" class="form-control" name="filtro_md_fecha_i" id="filtro_md_fecha_i" value="<?php echo date("Y-m-d");?>" onchange="cargando_search(); delay(function(){filtros_md()}, 50 );">                        
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO FECHA :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_md_fecha_f" class="form-label">
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_md_fecha_f();" data-bs-toggle="tooltip" title="Remover filtro"><i class="bi bi-trash3"></i></span>
                          Fecha Fin</label>
                        <input type="date" class="form-control" name="filtro_md_fecha_f" id="filtro_md_fecha_f" value="<?php echo date("Y-m-d");?>" onchange="cargando_search(); delay(function(){filtros_md()}, 50 );">                        
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-4 col-xxl-4">
                      <div class="form-group">
                        <label for="filtro_md_cliente" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_md_cliente();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Cliente
                          <span class="charge_filtro_md_cliente"></span>
                        </label>
                        <select class="form-control" name="filtro_md_cliente" id="filtro_md_cliente" onchange="cargando_search(); delay(function(){filtros_md()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO CLIENTE :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_md_comprobante" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_md_comprobante();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Comprobante
                          <span class="charge_filtro_md_comprobante"></span>
                        </label>
                        <select class="form-control" name="filtro_md_comprobante" id="filtro_md_comprobante" onchange="cargando_search(); delay(function(){filtros_md()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                 
                  <div class="d-flex"> 
                    <div class="dropdown ms-2">
                      <button class="btn btn-icon btn-secondary-light btn-sm btn-wave waves-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu md-otros-filtros">                        
                        <li><a class="dropdown-item md-o-f-ac" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat_md('ACEPTADA', '.md-o-f-ac')" ><i class="ri-check-fill align-middle me-1"></i> Solo aceptados</a></li>
                        <li><a class="dropdown-item md-o-f-an" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat_md('ANULADO', '.md-o-f-an')" ><i class="ri-close-fill align-middle me-1"></i> Solo anulados</a></li>
                        <li><a class="dropdown-item md-o-f-to active" href="javascript:void(0);" onclick="filtrar_solo_estado_sunat_md('', '.md-o-f-to')" ><i class="bi bi-border-all align-middle me-1"></i> Todos</a></li>                        
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered w-100" style="width: 100%;" id="tabla-facturacion-detalle">
                      <thead>
                        <tr>
                          <th class="text-center"><center>ID</center></th>
                          <th class="text-center"><center>Cobro?</center></th>
                          <th class="text-center"><center>Emision</center></th>
                          <th>Periodo</th>
                          <th>Cliente</th>
                          <th>Tipo</th>
                          <th>Num.</th>
                          <th>Comp.</th>
                          <th>Num.</th>
                          <th class="text-nowrap" >Total Cobro</th> 
                          <th>Recibido</th> 
                          <th>Vuelto</th> 
                          <th><center>Método</center></th>        
                          <th><center>Creador</center></th>        
                          <th><center>Estado</center></th>               
                        </tr>
                      </thead>
                      <tbody class="fs-11" ></tbody>
                      <tfoot>
                        <tr>
                          <th class="text-center"><center>ID</center></th>
                          <th class="text-center"><center>Cobro?</center></th>
                          <th class="text-center"><center>Emision</center></th>
                          <th>Periodo</th>
                          <th>Cliente</th>
                          <th>Tipo</th>
                          <th>Num.</th>
                          <th>Comp.</th>
                          <th>Num.</th>
                          <th class="text-nowrap" >Total Cobro</th> 
                          <th>Recibido</th> 
                          <th>Vuelto</th> 
                          <th><center>Método</center></th>   
                          <th><center>Creador</center></th>     
                          <th><center>Estado</center></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>                
              </div>
            </div>

          </div>
          <!-- End::row-1 -->

          <!-- MODAL - IMPRIMIR -->
          <div class="modal fade modal-effect" id="modal-imprimir-comprobante" tabindex="-1" aria-labelledby="modal-imprimir-comprobante-label" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-imprimir-comprobante-label">COMPROBANTE</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >                  
                  <div id="html-imprimir-comprobante" class="text-center" > </div>
                </div>                
              </div>
            </div>
          </div> 
          <!-- End::Modal-Ver-Comprobante venta -->

          <!-- MODAL - VER ESTADO -->
          <div class="modal fade modal-effect" id="modal-ver-estado" tabindex="-1" aria-labelledby="modal-ver-estado-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-ver-estado-label">VER ESTADO</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >                  
                  <div id="html-ver-estado" class="text-left" >
                    
                  </div>
                </div>                
              </div>
            </div>
          </div>          

          <!-- MODAL - VER FOTO -->
          <div class="modal fade modal-effect" id="modal-ver-imgenes" tabindex="-1" aria-labelledby="modal-ver-imgenes" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title title-ver-imgenes" id="modal-ver-imgenesLabel1">Imagen</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body html_modal_ver_imgenes">
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal - Ver foto proveedor -->

          <!-- MODAL - SELECIONAR PRODUCTO -->
          <div class="modal fade modal-effect" id="modal-producto" tabindex="-1" aria-labelledby="title-modal-producto-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="title-modal-producto-label">Seleccionar Producto</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive">
                  <table id="tabla-productos" class="table table-bordered w-100">
                    <thead>
                      <th>Op.</th>
                      <th>Code</th>
                      <th>Nombre Producto</th>                              
                      <th>P/U.</th>
                      <th>Descripción</th>
                    </thead>
                    <tbody></tbody>
                  </table>
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-Producto -->

          <!-- MODAL - DETALLE venta -->
          <div class="modal fade modal-effect" id="modal-detalle-venta" tabindex="-1" aria-labelledby="modal-detalle-ventaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-detalle-ventaLabel1">Detalle - venta</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                  <ul class="nav nav-tabs" id="custom-tab" role="tablist">
                    <!-- DATOS VENTA -->
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="rol-venta" data-bs-toggle="tab" data-bs-target="#rol-venta-pane" type="button" role="tab" aria-selected="true">venta</button>
                    </li>
                    <!-- DATOS TOURS -->
                    <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rol-detalle" data-bs-toggle="tab" data-bs-target="#rol-detalle-pane" type="button" role="tab" aria-selected="true">PRODUCTOS</button>
                    </li>
                    
                  </ul>
                  <div class="tab-content" id="custom-tabContent">                                
                    <!-- /.tab-panel --> 
                  </div>                   
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger py-1" data-bs-dismiss="modal" ><i class="las la-times"></i> Close</button>                  
                </div>
              </div>
            </div>
          </div> 
          <!-- End::Modal-Detalle-venta -->          
          
          <!-- MODAL - AGREGAR PRODUCTO - charge p1 -->
          <div class="modal fade modal-effect" id="modal-agregar-producto" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-productoLabel">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-productoLabel1">Registrar Producto</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="form-agregar-producto" id="form-agregar-producto" method="POST" class="row needs-validation" novalidate >
                    <div class="row gy-2" id="cargando-P1-formulario">
                      <!-- ----------------------- ID ------------- -->
                      <input type="hidden" id="idproducto" name="idproducto">

                      <!-- ----------------- Categoria --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="form-group">
                          <label for="categoria" class="form-label">Categoría</label>
                          <select class="form-control" name="categoria" id="categoria">
                            <!-- lista de categorias -->
                          </select>
                        </div>
                      </div>

                      <!-- ----------------- Unidad Medida --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="form-group">
                          <label for="u_medida" class="form-label">U. Medida</label>
                          <select class="form-control" name="u_medida" id="u_medida">
                            <!-- lista de u medidas -->
                          </select>
                        </div>
                      </div>

                      <!-- ----------------- Marca --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="form-group">
                          <label for="marca" class="form-label">Marca</label>
                          <select class="form-control" name="marca" id="marca">
                            <!-- lista de marcas -->
                          </select>
                        </div>
                      </div>
                      <!-- --------- NOMBRE ------ -->
                      <div class="col-md-4 col-lg-4 col-xl-6 col-xxl-6 mt-3">
                        <div class="form-group">
                          <label for="nombre" class="form-label">Nombre(*)</label>
                          <textarea class="form-control" name="nombre" id="nombre" rows="1"></textarea>
                        </div>
                      </div>

                      <!-- --------- DESCRIPCION ------ -->
                      <div class="col-md-4 col-lg-4 col-xl-6 col-xxl-6 mt-3">
                        <div class="form-group">
                          <label for="descripcion" class="form-label">Descrición(*)</label>
                          <textarea class="form-control" name="descripcion" id="descripcion" rows="1"></textarea>
                        </div>
                      </div>

                      <!-- ----------------- STOCK --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="stock" class="form-label">Stock(*)</label>
                          <input type="number" class="form-control" name="stock" id="stock" />
                        </div>
                      </div>

                      <!-- ----------------- STOCK MININO --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="stock_min" class="form-label">Stock Minimo(*)</label>
                          <input type="number" class="form-control" name="stock_min" id="stock_min" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO VENTA --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="precio_v" class="form-label">Precio Venta(*)</label>
                          <input type="number" class="form-control" name="precio_v" id="precio_v" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO venta --------------- -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                        <div class="form-group">
                          <label for="precio_c" class="form-label">Precio venta(*)</label>
                          <input type="number" class="form-control" name="precio_c" id="precio_c" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO X MAYOR --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                        <div class="form-group">
                          <label for="precio_x_mayor" class="form-label">Precio por Mayor</label>
                          <input type="text" class="form-control" name="precio_x_mayor" id="precio_x_mayor" placeholder="precioB" />
                        </div>
                      </div>

                      <!-- ----------------- PRECIO DISTRIBUIDOR --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                        <div class="form-group">
                          <label for="precio_dist" class="form-label">Precio Distribuidor</label>
                          <input type="text" class="form-control" name="precio_dist" id="precio_dist" placeholder="precioC"/>
                        </div>
                      </div>

                      <!-- ----------------- PRECIO ESPECIAL --------------- -->
                      <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                        <div class="form-group">
                          <label for="precio_esp" class="form-label">Precio Especial</label>
                          <input type="text" class="form-control" name="precio_esp" id="precio_esp" placeholder="precioD"/>
                        </div>
                      </div>

                      <!-- Imgen -->
                      <div class="col-md-6 col-lg-6 mt-4">
                        <span class="" > <b>Imagen Prducto</b> </span>
                        <div class="mb-4 mt-2 d-sm-flex align-items-center">
                          <div class="mb-0 me-5">
                            <span class="avatar avatar-xxl avatar-rounded">
                              <img src="../assets/modulo/productos/no-producto.png" alt="" id="imagenmuestraProducto" onerror="this.src='../assets/modulo/productos/no-producto.png';">
                              <a href="javascript:void(0);" class="badge rounded-pill bg-primary avatar-badge cursor-pointer">
                                <input type="file" class="position-absolute w-100 h-100 op-0" name="imagenProducto" id="imagenProducto" accept="image/*">
                                <input type="hidden" name="imagenactualProducto" id="imagenactualProducto">
                                <i class="fe fe-camera  "></i>
                              </a>
                            </span>
                          </div>
                          <div class="btn-group">
                            <a class="btn btn-primary" onclick="cambiarImagenProducto()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                            <a class="btn btn-light" onclick="removerImagenProducto()"><i class="bi bi-trash fs-6"></i> Remover</a>
                          </div>
                        </div>
                      </div> 

                    </div>
                    <div class="row" id="cargando-P2-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-producto">Submit</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_producto();"><i class="las la-times fs-lg"></i> Close</button>
                  <button type="button" class="btn btn-primary" id="guardar_registro_producto"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-Agregar-Producto -->

         

        </div>
      </div>
      <!-- End::app-content -->
      <?php } else { $title_submodulo ='venta'; $descripcion ='Lista de ventas del sistema!'; $title_modulo = 'ventas'; include("403_error.php"); }?>   

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>   

    <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Filepond JS -->
    <script src="../assets/libs/filepond/filepond.min.js"></script>
    <script src="../assets/libs/filepond/locale/es-es.js"></script>
    <script src="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js"></script>
    <script src="https://unpkg.com/medium-zoom/dist/medium-zoom.min.js"></script>

    <!-- Dropzone JS -->
    <script src="../assets/libs/dropzone/dropzone-min.js"></script>

    <!-- HTML Imagen -->
    <!-- <script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script> -->
    
    <script src="scripts/facturacion.js?version_jdl=1.31"></script>
    <script src="scripts/js_facturacion.js?version_jdl=1.31"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        console.log('Pagina termino de cargar');
      });
    </script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>