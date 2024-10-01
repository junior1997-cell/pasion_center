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
  <html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close">

  <head>

    <?php $title_page = "Clientes";
    include("template/head.php"); ?>

    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">

    <style>
      #tabla-cliente_filter label{ width: 100% !important; }
      #tabla-cliente_filter label input{ width: 100% !important; }

      #tabla_all_pagos_filter label{ width: 100% !important; }
      #tabla_all_pagos_filter label input{ width: 100% !important; }
    </style>

  </head>

  <body id="body-usuario">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if($_SESSION['cliente']==1) { ?>

      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">
            
          <!-- Start::page-header -->
          <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
              <div class="d-md-flex d-block align-items-center ">
                <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="wiev_tabla_formulario(2); limpiar_cliente();"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar</button>
                <button class="btn-modal-effect btn btn-info label-btn btn-pagos-all m-r-10px" onclick="cargar_fltros_pagos_all_cliente();"><i class="ti ti-currency-dollar label-btn-icon me-2"></i>Pagos</button>
                <button class="btn-modal-effect btn btn-teal label-btn btn-pagos-all m-r-10px" onclick="toastr_info('En desarrolo', 'Estamos por terminar esta opcion, sea paciente porfavor.');"><i class="bi bi-file-earmark-post label-btn-icon me-2"></i> Recibos</button>
                <button type="button" class="btn btn-danger btn-cancelar btn-regresar m-r-10px" onclick="wiev_tabla_formulario(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar-cobro m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0 title-body-pagina">Lista de clientes!</p>
                  <span class="fs-semibold text-muted detalle-body-pagina">Adminstra de manera eficiente tus clientes.</span>
                </div>
              </div>
            </div>

            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Zonas</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- End::page-header -->

          <!-- Start::row-1 -->          
          <div class="row">

            <!-- ::::::::::::::::::: VER TABLA PRINCIPAL ::::::::::::::::::: -->
            <div class="col-xxl-12 col-xl-12 " id="div-tabla-principal">          
              <div class="card custom-card">
                <div class="card-header row" style="gap: 0px !important;">
                      
                  <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_trabajador" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_trabajador');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Trabajador
                        <span class="charge_filtro_trabajador"></span>
                      </label>
                      <select class="form-control" name="filtro_trabajador" id="filtro_trabajador" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                  <!-- ::::::::::::::::::::: FILTRO DIA DE PAGO :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_dia_pago" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_dia');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Día de Afiliación
                        <span class="charge_filtro_dia_pago"></span>
                      </label>
                      <select class="form-control" name="filtro_dia_pago" id="filtro_dia_pago" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                  <!-- ::::::::::::::::::::: FILTRO PLAN :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_plan" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_plan');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Plan
                        <span class="charge_filtro_plan"></span>
                      </label> 
                      <select class="form-control" name="filtro_plan" id="filtro_plan" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > </select>
                    </div>
                  </div>
                  <!-- ::::::::::::::::::::: FILTRO ZONA ANTENA :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_zona_antena" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_zona_antena');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Zona Antena
                        <span class="charge_filtro_zona_antena"></span>
                      </label>
                      <select class="form-control" name="filtro_zona_antena" id="filtro_zona_antena" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                    
                </div>
                <div class="card-body">                      
                      
                  <nav class="nav bg-light border-2 p-2 nav-style-6 nav-pills mb-3 nav-justified d-sm-flex d-block" role="tablist">
                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#nav-deudores" aria-selected="false" onclick="filtrar_grupo('tabla_deudores');" >Deudores <span class="cant-span-deudor badge bg-danger-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link " data-bs-toggle="tab" role="tab" href="#nav-sin-deuda" aria-selected="false" onclick="filtrar_grupo('tabla_no_deuda');">Sin Deuda <span class="cant-span-no-deuda badge bg-info-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link" data-bs-toggle="tab" role="tab" href="#nav-sin-servicio" aria-selected="false" onclick="filtrar_grupo('tabla_no_servicio');">Sin Servicio <span class="cant-span-no-servicio badge bg-info-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link" data-bs-toggle="tab" role="tab" href="#nav-sin-pago" aria-selected="false" onclick="filtrar_grupo('tabla_no_pago');">Sin Pagos <span class="cant-span-no-pago badge bg-info-transparent border border-1 ms-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                    <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#nav-todos" aria-selected="true" onclick="filtrar_grupo('tabla_todos');">Todos <span class="cant-span-total badge bg-info-transparent ms-1 border border-1"><div class="spinner-border spinner-border-sm" role="status"></div></span></a>
                  </nav>
                  <div class="tab-content">
                    <div class="tab-pane text-muted " id="nav-deudores" role="tabpanel">
                      <div class="row">
                        <div class="col-4">                        
                          <div class="card border-0">
                            <div class="alert alert-danger border border-danger mb-0 p-2">
                              <div class="d-flex align-items-start">
                                <div class="me-2">
                                  <svg class="flex-shrink-0 svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                                    <g>
                                      <rect fill="none" height="24" width="24"></rect>
                                    </g>
                                    <g>
                                      <g>
                                        <g>
                                          <path d="M15.73,3H8.27L3,8.27v7.46L8.27,21h7.46L21,15.73V8.27L15.73,3z M19,14.9L14.9,19H9.1L5,14.9V9.1L9.1,5h5.8L19,9.1V14.9z"></path>
                                          <rect height="6" width="2" x="11" y="7"></rect>
                                          <rect height="2" width="2" x="11" y="15"></rect>
                                        </g>
                                      </g>
                                    </g>
                                  </svg>
                                </div>
                                <div class="text-danger w-100">
                                  <div class="fw-semibold d-flex justify-content-between">Estamos en Desarrollo!!<button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>
                                  <div class="fs-12 op-8 mb-1">Esta sección del módulo aún está en desarrollo. Algunas funcionalidades pueden no estar disponibles o funcionar de manera inesperada. Agradecemos tu comprensión y paciencia.</div>
                                  <div class="fs-12 d-inline-flex">
                                    <a href="javascript:void(0);" class="text-info fw-semibold me-2 d-inline-block">cancel</a>
                                    <a href="javascript:void(0);" class="text-danger fw-semibold">open</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane  text-muted" id="nav-sin-deuda" role="tabpanel">
                      <div class="row">
                        <div class="col-4">                        
                          <div class="card border-0">
                            <div class="alert alert-danger border border-danger mb-0 p-2">
                              <div class="d-flex align-items-start">
                                <div class="me-2">
                                  <svg class="flex-shrink-0 svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                                    <g>
                                      <rect fill="none" height="24" width="24"></rect>
                                    </g>
                                    <g>
                                      <g>
                                        <g>
                                          <path d="M15.73,3H8.27L3,8.27v7.46L8.27,21h7.46L21,15.73V8.27L15.73,3z M19,14.9L14.9,19H9.1L5,14.9V9.1L9.1,5h5.8L19,9.1V14.9z"></path>
                                          <rect height="6" width="2" x="11" y="7"></rect>
                                          <rect height="2" width="2" x="11" y="15"></rect>
                                        </g>
                                      </g>
                                    </g>
                                  </svg>
                                </div>
                                <div class="text-danger w-100">
                                  <div class="fw-semibold d-flex justify-content-between">Estamos en Desarrollo!!<button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>
                                  <div class="fs-12 op-8 mb-1">Esta sección del módulo aún está en desarrollo. Algunas funcionalidades pueden no estar disponibles o funcionar de manera inesperada. Agradecemos tu comprensión y paciencia.</div>
                                  <div class="fs-12 d-inline-flex">
                                    <a href="javascript:void(0);" class="text-info fw-semibold me-2 d-inline-block">cancel</a>
                                    <a href="javascript:void(0);" class="text-danger fw-semibold">open</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane text-muted" id="nav-sin-servicio" role="tabpanel">
                      <div class="row">
                        <div class="col-4">                        
                          <div class="card border-0">
                            <div class="alert alert-danger border border-danger mb-0 p-2">
                              <div class="d-flex align-items-start">
                                <div class="me-2">
                                  <svg class="flex-shrink-0 svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                                    <g>
                                      <rect fill="none" height="24" width="24"></rect>
                                    </g>
                                    <g>
                                      <g>
                                        <g>
                                          <path d="M15.73,3H8.27L3,8.27v7.46L8.27,21h7.46L21,15.73V8.27L15.73,3z M19,14.9L14.9,19H9.1L5,14.9V9.1L9.1,5h5.8L19,9.1V14.9z"></path>
                                          <rect height="6" width="2" x="11" y="7"></rect>
                                          <rect height="2" width="2" x="11" y="15"></rect>
                                        </g>
                                      </g>
                                    </g>
                                  </svg>
                                </div>
                                <div class="text-danger w-100">
                                  <div class="fw-semibold d-flex justify-content-between">Estamos en Desarrollo!!<button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>
                                  <div class="fs-12 op-8 mb-1">Esta sección del módulo aún está en desarrollo. Algunas funcionalidades pueden no estar disponibles o funcionar de manera inesperada. Agradecemos tu comprensión y paciencia.</div>
                                  <div class="fs-12 d-inline-flex">
                                    <a href="javascript:void(0);" class="text-info fw-semibold me-2 d-inline-block">cancel</a>
                                    <a href="javascript:void(0);" class="text-danger fw-semibold">open</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane text-muted" id="nav-sin-pago" role="tabpanel">
                      <div class="row">
                        <div class="col-4">                        
                          <div class="card border-0">
                            <div class="alert alert-danger border border-danger mb-0 p-2">
                              <div class="d-flex align-items-start">
                                <div class="me-2">
                                  <svg class="flex-shrink-0 svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                                    <g>
                                      <rect fill="none" height="24" width="24"></rect>
                                    </g>
                                    <g>
                                      <g>
                                        <g>
                                          <path d="M15.73,3H8.27L3,8.27v7.46L8.27,21h7.46L21,15.73V8.27L15.73,3z M19,14.9L14.9,19H9.1L5,14.9V9.1L9.1,5h5.8L19,9.1V14.9z"></path>
                                          <rect height="6" width="2" x="11" y="7"></rect>
                                          <rect height="2" width="2" x="11" y="15"></rect>
                                        </g>
                                      </g>
                                    </g>
                                  </svg>
                                </div>
                                <div class="text-danger w-100">
                                  <div class="fw-semibold d-flex justify-content-between">Estamos en Desarrollo!!<button type="button" class="btn-close p-0" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>
                                  <div class="fs-12 op-8 mb-1">Esta sección del módulo aún está en desarrollo. Algunas funcionalidades pueden no estar disponibles o funcionar de manera inesperada. Agradecemos tu comprensión y paciencia.</div>
                                  <div class="fs-12 d-inline-flex">
                                    <a href="javascript:void(0);" class="text-info fw-semibold me-2 d-inline-block">cancel</a>
                                    <a href="javascript:void(0);" class="text-danger fw-semibold">open</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane show active text-muted" id="nav-todos" role="tabpanel">
                      <div  class="table-responsive">
                        <table id="tabla-cliente" class="table table-bordered w-100" style="width: 100%;">
                          <thead class="buscando_tabla">
                            <tr id="id_buscando_tabla"> 
                              <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                            </tr>
                            <tr>
                              <th class="text-center">#</th>
                              <th class="text-center">Acciones</th>
                              <th>Cliente</th>
                              <th>Lugar/Direccion</th>
                              <th>Falta</th>
                              <th>Cancelación</th>
                              <th>Zona/Plan</th>
                              <th>IP</th>
                              <th>Trabajador</th>                                
                              <th class="text-center">Observación.</th>

                              <th class="text-center">Nombres</th>
                              <th class="text-center">Tipo Documento</th>
                              <th class="text-center">Número Documento</th>
                              <th class="text-center">Centro Poblado</th>
                              <th class="text-center">Dirección</th>
                              <th class="text-center">Plan</th>
                              <th class="text-center">Costo Plan</th>
                              <th class="text-center">Nombre Zona</th>
                              <th class="text-center">Siguiente Pago</th>
                              <th class="text-center">Ip Antena</th>

                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-center">#</th>
                              <th class="text-center">Acciones</th>
                              <th>Cliente</th>
                              <th>Direccion</th>
                              <th>Falta</th>
                              <th>Cancelación</th>
                              <th>Zona/Plan</th>
                              <th>IP</th>
                              <th>Trabajador</th>
                              <th class="text-center">Observación.</th>

                              <th class="text-center">Nombres</th>
                              <th class="text-center">Tipo Documento</th>
                              <th class="text-center">Número Documento</th>
                              <th class="text-center">Centro Poblado</th>
                              <th class="text-center">Dirección</th>
                              <th class="text-center">Plan</th>
                              <th class="text-center">Costo Plan</th>
                              <th class="text-center">Nombre Zona</th>
                              <th class="text-center">Siguiente Pago</th>
                              <th class="text-center">Ip Antena</th>

                            </tr>
                          </tfoot>
                        </table>
                      </div>    
                    </div>
                  </div>                    
                 
                                 
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="wiev_tabla_formulario(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                </div>
              </div>
              
            </div>

            <!-- ::::::::::::::::::: VER COBROS INDIVIDAL ::::::::::::::::::: -->
            <div class="col-xxl-12 col-xl-12 " id="div-ver-cobro-x-cliente" style="display: none;">          
              <div class="card custom-card">                
                <div class="card-body"> 
                  
                  <div class="table-responsive" id="div_tabla_x_cliente">
                    <div class="row" >
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>                       
                  </div>
                                
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="wiev_tabla_formulario(1);" ><i class="las la-times fs-lg"></i> Cancelar</button>                  
                </div>
              </div>
              
            </div>

            <!-- ::::::::::::::::::: VER TODOS LOS COBROS ::::::::::::::::::: -->
            <div class="col-xxl-12 col-xl-12 " id="div-ver-cobro-all-cliente" style="display: none;">          
              <div class="card custom-card">                
                <div class="card-body"> 
                 
                  <div class="row mb-3" >
                      
                    <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                      <div class="form-group">
                        <label for="filtro_p_all_trabajador" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_p_all_trabajador');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Trabajador
                          <span class="charge_filtro_p_all_trabajador"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_trabajador" id="filtro_p_all_trabajador" onchange="cargando_search_pago_all(); delay(function(){filtros_pago_all()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO DIA DE PAGO :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_p_all_dia_pago" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_dia_pago');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Día Afiliación
                          <span class="charge_filtro_p_all_dia_pago"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_dia_pago" id="filtro_p_all_dia_pago" onchange="cargando_search_pago_all(); delay(function(){filtros_pago_all()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO AÑO :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_p_all_anio_pago" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_anio_pago');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Año de Pago
                          <span class="charge_filtro_p_all_anio_pago"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_anio_pago" id="filtro_p_all_anio_pago" onchange="cargando_search_pago_all(); delay(function(){filtros_pago_all()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO PLAN :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_p_all_plan" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_p_all_plan');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Plan
                          <span class="charge_filtro_p_all_plan"></span>
                        </label> 
                        <select class="form-control" name="filtro_p_all_plan" id="filtro_p_all_plan" onchange="cargando_search_pago_all(); delay(function(){filtros_pago_all()}, 50 );" > </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO ZONA ANTENA :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                      <div class="form-group">
                        <label for="filtro_p_all_zona_antena" class="form-label">                         
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_p_all_zona_antena');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Zona Antena
                          <span class="charge_filtro_p_all_zona_antena"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_zona_antena" id="filtro_p_all_zona_antena" onchange="cargando_search_pago_all(); delay(function(){filtros_pago_all()}, 50 );" > <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    
                  </div>
                  <div class="table-responsive" >
                    <table id="tabla_all_pagos" class="table table-bordered w-100" style="width: 100%;">
                      <thead>
                        <tr>
                          <th class="font-size-11px text-nowrap">N°</th> 
                          <th class="font-size-11px text-nowrap">APELLIDOS Y NOMBRES</th> 
                          <th class="font-size-11px text-nowrap" >CANCELACIÓN</th> 
                          <th class="font-size-11px text-nowrap" >IMPORTE</th> 
                          <th class="font-size-11px text-nowrap" >AÑO</th> 
                          <th class="font-size-11px text-nowrap" >ENE</th> 
                          <th class="font-size-11px text-nowrap" >FEB</th> 
                          <th class="font-size-11px text-nowrap" >MAR</th> 
                          <th class="font-size-11px text-nowrap" >ABR</th>
                          <th class="font-size-11px text-nowrap" >MAY</th> 
                          <th class="font-size-11px text-nowrap" >JUN</th> 
                          <th class="font-size-11px text-nowrap" >JUL</th> 
                          <th class="font-size-11px text-nowrap" >AGO</th> 
                          <th class="font-size-11px text-nowrap" >SEP</th> 
                          <th class="font-size-11px text-nowrap" >OCT</th> 
                          <th class="font-size-11px text-nowrap" >NOV</th> 
                          <th class="font-size-11px text-nowrap" >DIC</th> 
                          <th class="font-size-11px text-nowrap" >OBSERVACIONES</th>  
                          <th class="font-size-11px text-nowrap" >ID</th>
                          <th class="font-size-11px text-nowrap" >PERIODO</th>          
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                    <!-- <div class="row" >
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>                       -->
                  </div>
                  
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="wiev_tabla_formulario(1);" ><i class="las la-times fs-lg"></i> Cancelar</button>                  
                </div>
              </div>
              
            </div>

            <!-- ::::::::::::::::::: FORMULARIO ::::::::::::::::::: -->
            <div class="col-xxl-12 col-xl-12 " id="div-form-cliente" style="display: none;">          
              <div class="card custom-card">                
                <div class="card-body">                  
                 
                  <form name="form-agregar-cliente" id="form-agregar-cliente" method="POST">

                    <div class="row" id="cargando-1-formulario">

                      <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">

                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS PERSONALES</b></label></div>
                          </div>
                        </div>

                        <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">

                          <div class="row ">

                            <input type="hidden" id="idpersona" name="idpersona">
                            <input type="hidden" id="idtipo_persona" name="idtipo_persona" value="3">
                            <input type="hidden" id="idbancos" name="idbancos" value="1">
                            <input type="hidden" id="idcargo_trabajador" name="idcargo_trabajador" value="1">
                            <!-- ----------- -->

                            <input type="hidden" id="idpersona_cliente" name="idpersona_cliente">

                            <!-- TIPO PERSONA -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="nombre_razonsocial">Tipo Persona: <sup class="text-danger">*</sup></label>
                                <select name="tipo_persona_sunat" id="tipo_persona_sunat" class="form-control" placeholder="Tipo Persona">
                                  <option value="NATURAL">NATURAL</option>
                                  <option value="JURÍDICA">JURÍDICA</option>
                                </select>
                              </div>
                            </div>

                            <!-- Tipo Doc -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="tipo_documento">Tipo Doc. <sup class="text-danger">*</sup></label>
                                <select name="tipo_documento" id="tipo_documento" class="form-control" placeholder="Tipo de documento" ></select>
                              </div>
                            </div>

                            <!-- N° de documento -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="numero_documento">N° de documento <sup class="text-danger">*</sup></label>
                                <div class="input-group ">
                                  <input type="text" class="form-control" name="numero_documento" id="numero_documento" placeholder="" aria-describedby="icon-view-password">
                                  <button class="btn btn-primary" type="button" onclick="buscar_sunat_reniec('#form-agregar-cliente', '_t', '#tipo_documento', '#numero_documento', '#nombre_razonsocial', '#apellidos_nombrecomercial', '#direccion', '#distrito' );">
                                    <i class='bx bx-search-alt' id="search_t"></i>
                                    <div class="spinner-border spinner-border-sm" role="status" id="charge_t" style="display: none;"></div>
                                  </button>
                                </div>
                              </div>
                            </div>

                            <!-- Nombre -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mt-2" >
                              <div class="form-group">
                                <label class="form-label nombre_razon" for="nombre_razonsocial">Nombre <sup class="text-danger">*</sup></label>
                                <input type="text" name="nombre_razonsocial" class="form-control inpur_edit" id="nombre_razonsocial" />
                              </div>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mt-2" >
                              <div class="form-group">
                                <label class="form-label apellidos_nombrecomer" for="apellidos_nombrecomercial">Apellidos <sup class="text-danger">*</sup></label>
                                <input type="text" name="apellidos_nombrecomercial" class="form-control inpur_edit" id="apellidos_nombrecomercial" />
                              </div>
                            </div>
                            <!-- Fecha cumpleaño -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-5 col-xl-5 col-xxl-5 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="fecha_nacimiento">Fecha nacimiento </label>
                                <input type="date" name="fecha_nacimiento" class="form-control inpur_edit" id="fecha_nacimiento" placeholder="Fecha de Nacimiento" onclick="calcular_edad('#fecha_nacimiento', '#edad', '.edad');" onchange="calcular_edad('#fecha_nacimiento', '#edad', '.edad');" />
                                <input type="hidden" name="edad" id="edad" />
                              </div>
                            </div>
                            <!-- Edad -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-2 col-xl-2 col-xxl-2 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="Edad">Edad </label>
                                <p class="edad" style="border: 1px solid #ced4da; border-radius: 4px; padding: 5px;">0 años.</p>

                              </div>
                            </div>
                            <!-- Celular  -->
                            <div class="col-12 col-sm-6 col-md-12 col-lg-5 col-xl-5 col-xxl-5 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="celular">Celular </label>
                                <input type="number" name="celular" class="form-control inpur_edit" id="celular" />
                              </div>
                            </div>

                            <!-- Correo -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 mt-2" >
                              <div class="form-group">
                                <label class="form-label" for="Correo">Correo </label>
                                <input type="email" name="correo" id="correo" class="form-control" placeholder="Correo"></input>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>
                      <!-- --------------DIRECCION -->
                      <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">

                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>UBICACIÓN</b></label></div>
                          </div>
                        </div>

                        <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">

                          <div class="row ">

                            <!-- Dirección -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="direccion">Dirección: <sup class="text-danger">*</sup></label>
                                <input type="text" name="direccion" class="form-control inpur_edit" id="direccion" placeholder="ejemp: Jr las flores - Al costado del colegio" />
                              </div>
                            </div>

                            <!-- Distrito -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="distrito" class="form-label">Distrito: </label></label>
                                <select name="distrito" id="distrito" class="form-control" placeholder="Seleccionar" onchange="llenar_dep_prov_ubig(this);">
                                </select>
                              </div>
                            </div>
                            <!-- Departamento -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="departamento" class="form-label">Departamento: <span class="chargue-pro"></span></label>
                                <input type="text" class="form-control" name="departamento" id="departamento" readonly>
                              </div>
                            </div>
                            <!-- Provincia -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="provincia" class="form-label">Provincia: <span class="chargue-dep"></span></label>
                                <input type="text" class="form-control" name="provincia" id="provincia" readonly>
                              </div>
                            </div>
                            <!-- Ubigeo -->
                            <div class="col-12 col-md-12 col-lg-6 col-xl-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label for="ubigeo" class="form-label">Ubigeo: <span class="chargue-ubi"></span></label>
                                <input type="text" class="form-control" name="ubigeo" id="ubigeo" readonly>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>

                      <div class="col-12 col-md-12">

                        <div class="row">
                          <!-- Grupo -->
                          <div class="col-12 pl-0">
                            <div class="text-primary p-l-10px" style="position: relative; top: 10px;"><label class="bg-white" for=""><b>DATOS TÉCNICOS </b>
                          </label></div>
                          </div>
                        </div>

                        <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                          <div class="row">


                            <!-- Select trabajdor -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="idpersona_trabajador">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('trab');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Trabajador <sup class="text-danger">*</sup><span class="charge_idtrabaj"></span></label>
                                <select name="idpersona_trabajador" id="idpersona_trabajador" class="form-control" placeholder="Selec. Trabajador"></select>
                              </div>
                            </div>

                            <!-- Select Zona antena -->
                            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="idzona_antena">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('zona');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span> 
                                Zona Antena <sup class="text-danger">*</sup> <span class="charge_idzona"></span></label>
                                <select name="idzona_antena" id="idzona_antena" class="form-control" placeholder="Selec. Zona Antena"></select>
                              </div>
                            </div>
                            <!-- Select Zona antena -->
                            <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="idselec_centroProbl">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('centroPbl');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Centro Poblado <sup class="text-danger">*</sup><span class="charge_idctroPbl"></span></label>
                                <select name="idselec_centroProbl" id="idselec_centroProbl" class="form-control" placeholder="Selecionar"></select>
                              </div>
                            </div>

                            <!-- Select Plan -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="idplan">
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('plan');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Plan <sup class="text-danger">*</sup><span class="charge_idplan"></span></label>
                                <select name="idplan" id="idplan" class="form-control" placeholder="Selec. Plan"></select>
                              </div>
                            </div>

                            <!-- Ip Personal -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="ip_personal">Ip Personal </label>
                                <input type="text" name="ip_personal" class="form-control inpur_edit" id="ip_personal" placeholder="ejemp: 192.168.1.12" />
                              </div>
                            </div>

                            <!-- fecha afiliacion -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="fecha_afiliacion">Fecha Afiliación <sup class="text-danger">*</sup></label>
                                <input type="date" name="fecha_afiliacion" class="form-control inpur_edit" id="fecha_afiliacion" />
                              </div>
                            </div>
                            <!-- fecha CANCELACION -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="fecha_cancelacion">Fecha Cancelación <sup class="text-danger">*</sup></label>
                                <input type="date" name="fecha_cancelacion" class="form-control inpur_edit" id="fecha_cancelacion" />
                              </div>
                            </div>
                            <!-- USUARIO MICROTICK -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="usuario_microtick">Usuario Microtic <sup class="text-danger">*</sup></label>
                                <input type="text" name="usuario_microtick"  id="usuario_microtick" class="form-control inpur_edit" />
                              </div>
                            </div>
                            <!--NOTA -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-9 col-xxl-9" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="nota">Nota </label>
                                <textarea class="form-control inpur_edit" name="nota" id="nota" cols="30" rows="2" placeholder="ejemp: Se removio el servicio por deuda" ></textarea>
                              </div>
                            </div>
                            <!-- Descuento -->
                            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 col-xxl-2" style="margin-bottom: 20px; display: none;">
                              <div class="form-group">
                                <label class="form-label" for="estado_descuento"><sup class="text-white">*</sup></label>
                                <div class="custom-toggle-switch d-flex align-items-center mb-4">
                                  <input id="toggleswitchSuccess" name="toggleswitch001" type="checkbox" onchange="funtion_switch();">
                                  <label for="toggleswitchSuccess" class="label-success"></label><span class="ms-3">Descuento</span>
                                </div>
                                <input type="hidden" id="estado_descuento" name="estado_descuento" value="0">
                              </div>
                            </div>

                            <!-- fecha afiliacion -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-2 col-xl-2 col-xxl-2" style="margin-bottom: 20px; display: none;">
                              <div class="form-group">
                                <label class="form-label" for="descuento">Monto descuento <sup class="text-danger">*</sup></label>
                                <input type="number" name="descuento" class="form-control inpur_edit" id="descuento" readonly />
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>

                      <!-- Imgen -->
                      <div class="col-md-4 col-lg-4 mt-4">
                        <span class=""> <b>Imagen de Perfil</b> </span>
                        <div class="mb-4 mt-2 d-sm-flex align-items-center">
                          <div class="mb-0 me-5">
                            <span class="avatar avatar-xxl avatar-rounded">
                              <img src="../assets/images/faces/9.jpg" alt="" id="imagenmuestra" onerror="this.src='../assets/modulo/persona/perfil/no-perfil.jpg';">
                              <a href="javascript:void(0);" class="badge rounded-pill bg-primary avatar-badge cursor-pointer">
                                <input type="file" class="position-absolute w-100 h-100 op-0" name="imagen" id="imagen" accept="image/*">
                                <input type="hidden" name="imagenactual" id="imagenactual">
                                <i class="fe fe-camera  "></i>
                              </a>
                            </span>
                          </div>
                          <div class="btn-group">
                            <a class="btn btn-primary" onclick="cambiarImagen()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                            <a class="btn btn-light" onclick="removerImagen()"><i class="bi bi-trash fs-6"></i> Remover</a>
                          </div>
                        </div>
                      </div>

                      <!-- Chargue -->
                      <div class="p-l-25px col-lg-12" id="barra_progress_usuario_div" style="display: none;">
                        <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                          <div id="barra_progress_usuario" class="progress-bar" style="width: 0%">
                            <div class="progress-bar-value">0%</div>
                          </div>
                        </div>
                      </div>

                    </div>

                    <div class="row" id="cargando-2-formulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" style="display: none;" id="submit-form-cliente">Submit</button>
                  </form>
                               
                </div>
                <div class="card-footer border-top-0">
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="wiev_tabla_formulario(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                </div>
              </div>              
            </div>

            <!-- ::::::::::::::::::: REALIZAR PAGO ::::::::::::::::::: -->
            <div class="col-xl-12" id="div-realizar-pago"  style="display: none;">      
              <div class="col-xl-12">
                <div class="card custom-card mb-2">
                  <div class="card-header justify-content-between border-bottom-0 py-2">
                    <div class="card-title cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapse-cobros-cliente">Mostrar pagos realizados</div>
                    <a href="javascript:void(0);" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-cobros-cliente" aria-expanded="false" aria-controls="collapse-cobros-cliente">
                      <i class="ri-arrow-down-s-line fs-18 collapse-open"></i>
                      <i class="ri-arrow-up-s-line collapse-close fs-18"></i>
                    </a>
                  </div>
                  <div class="collapse border-top" id="collapse-cobros-cliente">
                    <div class="card-body pb-0 pt-2">
                      <div class="table-responsive" id="div_tabla_x_cliente_pagar">
                        <div class="row" >
                          <div class="col-lg-12 text-center">
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div>                       
                      </div>
                    </div>
                    <div class="card-footer py-1 text-center">
                      <button class="btn btn-sm btn-primary py-0" data-bs-toggle="collapse" data-bs-target="#collapse-cobros-cliente"><i class="bi bi-dash-square-dotted"></i> Minimizar</button>
                      <button class="btn btn-sm btn-info py-0 reload-reload-pago-individual" onclick=""><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
                    </div>
                  </div>
                </div>
              </div>        
              <div class="card custom-card">
                <div class="card-body">                    
                  
                  <!-- FORM - COMPROBANTE -->                    
                  <form name="form-facturacion" id="form-facturacion" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="row" id="cargando-3-formulario">

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
                      
                      <!-- ID CLIENTE -->
                      <input type="hidden" class="form-control" name="f_idpersona_cliente" id="f_idpersona_cliente" value="">                          
                      <input type="hidden" class="form-control" name="f_tipo_documento" id="f_tipo_documento" value="">
                      <input type="hidden" class="form-control" name="f_numero_documento" id="f_numero_documento" value="">
                      <input type="hidden" class="form-control" name="f_direccion" id="f_direccion" value="">
                      <input type="hidden" class="form-control" name="f_dia_cancelacion" id="f_dia_cancelacion" value="">
                      
                      <!-- NOTA DE CREDITO -->
                      <input type="hidden" name="f_nc_tipo_comprobante" id="f_nc_tipo_comprobante">
                      <input type="hidden" name="f_nc_serie_y_numero" id="f_nc_serie_y_numero">                          
                      <input type="hidden" name="f_nc_motivo_anulacion" id="f_nc_motivo_anulacion">  

                      <div class="col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="row gy-3">
                          <!-- ENVIO AUTOMATICO -->
                          <div class="col-md-12 col-lg-6 col-xl-5 col-xxl-4 px-0">
                            <div class="custom-toggle-switch d-flex align-items-center mb-1">
                              <input id="f_crear_y_emitir" name="f_crear_y_emitir" type="checkbox" checked="" value="SI">
                              <label for="f_crear_y_emitir" class="label-warning"></label><span class="ms-3 fs-11">SUNAT</span>
                            </div>
                          </div>
                          <!-- CREAR Y MOSTRAR-->
                          <div class="col-md-12 col-lg-6 col-xl-5 col-xxl-4 px-0">
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
                                  
                                </div>
                              </div>
                            </div>                            
                          </div>    
                          
                          <div class="col-md-12 col-lg-4 col-xl-4 col-xxl-4 ">
                            <div class="form-group">
                              <label for="f_serie_comprobante" class="form-label">Serie <span class="f_charge_serie_comprobante"></span></label>
                              <select class="form-control" name="f_serie_comprobante" id="f_serie_comprobante"></select>
                            </div>
                          </div>                                                 
                          
                          <!-- DESCRIPCION -->
                          <div class="col-md-6 col-lg-12 col-xl-12 col-xxl-12">
                            <div class="form-group">
                              <label for="f_observacion_documento" class="form-label">Observacion</label>
                              <textarea name="f_observacion_documento" id="f_observacion_documento" class="form-control" rows="2" placeholder="ejemp: Cobro de servicio de internet."></textarea>
                            </div>
                          </div>                                                                                        

                        </div>
                      </div>

                      <div class="col-md-12 col-lg-8 col-xl-8 col-xxl-8">
                        <div class="row" id="cargando-5-formulario">

                          <div class="col-6 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2 mt-xs-3 div_agregar_producto">
                            <button class="btn btn-sm btn-info label-btn p-l-40px m-r-10px" type="button" onclick="listar_tabla_producto('PR');"  >
                              <i class="ri-add-circle-line label-btn-icon me-2"></i> Producto
                            </button>
                          </div>
                          <div class="col-6 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2 mt-xs-3 div_agregar_producto">
                            <button class="btn btn-sm btn-primary label-btn p-l-40px m-r-10px" type="button"  onclick="listar_tabla_producto('SR');"  >
                            <i class="ri-add-fill label-btn-icon me-2"></i> 
                              Servicio
                            </button>
                          </div>  

                          <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5 col-xxl-5 mt-xs-3 div_agregar_producto">
                            <div class="input-group">                              
                              <button type="button" class="input-group-text buscar_x_code" onclick="listar_producto_x_codigo();"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Buscar por codigo de producto."><i class='bx bx-search-alt'></i></button>
                              <input type="text" name="codigob" id="codigob" class="form-control form-control-sm" onkeyup="mayus(this);" placeholder="Digite el código de producto." >
                            </div>
                          </div>                                              

                          <!-- ------- TABLA PRODUCTOS SELECCIONADOS ------ --> 
                          <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive pt-3">
                            <table id="tabla-productos-seleccionados" class="table table-striped table-bordered table-condensed table-hover">
                              <thead class="bg-color-dark text-white">
                                <th class="fs-11 py-1" data-toggle="tooltip" data-original-title="Opciones">Op.</th>
                                <th class="fs-11 py-1">Cod</th> 
                                <th class="fs-11 py-1">Producto</th>
                                <th class="fs-11 py-1">Unidad</th>
                                <th class="font-size-11px py-1">Periodo</th>
                                <th class="fs-11 py-1">Cantidad</th>                                        
                                <th class="fs-11 py-1" data-toggle="tooltip" data-original-title="Precio Unitario">P/U</th>
                                <th class="fs-11 py-1">Descuento</th>
                                <th class="fs-11 py-1">Subtotal</th>
                                <th class="fs-11 py-1 text-center" ><i class='bx bx-cog fs-4'></i></th>
                              </thead>
                              <tbody ></tbody>
                              <tfoot>
                                <td colspan="6"></td>

                                <th class="text-right">
                                  <h6 class="fs-11 f_tipo_gravada">SUBTOTAL</h6>
                                  <h6 class="fs-11 ">DESCUENTO</h6>
                                  <h6 class="fs-11 f_val_igv">IGV (18%)</h6>
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
                                  <label for="f_metodo_pago" class="form-label">Método de pago</label>
                                  <select class="form-control" name="f_metodo_pago" id="f_metodo_pago" onchange="capturar_pago_venta();">
                                    <option selected >CARGANDO...</option>                                                            
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
                          <div class="col-md-12 col-lg-3 col-xl-3 col-xxl-3 pt-3 div_f_usar_anticipo">
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
                        <div class="row" id="cargando-6-formulario" style="display: none;" >
                          <div class="col-lg-12 mt-5 text-center">                         
                            <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div>
                            <h4 class="bx-flashing">Cargando...</h4>
                          </div>
                        </div>
                      </div>

                    </div>  
                    
                    <!-- ::::::::::: CARGANDO ... :::::::: -->
                    <div class="row" id="cargando-4-formulario" style="display: none;" >
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
                  <button type="button" class="btn btn-danger btn-cancelar" onclick="wiev_tabla_formulario(1); limpiar_form_venta();" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                  <button type="button" class="btn btn-success btn-guardar-cobro" id="guardar_registro_venta" style="display: none;"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                </div>
              </div>              
            </div>
          </div>          
          <!-- End::row-1 --> 
          
          <!-- Start::Modal-pago-cliente-x-mes -->
          <div class="modal fade" id="pago-cliente-mes" tabindex="-1" aria-labelledby="pago-cliente-mesLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="pago-cliente-mesLabel1">Pagos por Mes</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="table-responsive" id="div_tabla_pagos_Cx_mes">
                    <div class="row" >
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>                      
                  </div>
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-pago-cliente-x-mes -->

          <!-- Start::modal-imprimir_ticket -->
          <div class="modal fade" id="modal-imprimir-comprobante" tabindex="-1" aria-labelledby="modal-imprimir-comprobante-Label" aria-hidden="true">
            <div class="modal-dialog modal-md" >
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modal-imprimir-comprobante-Label"> <button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('modalAntcticket')"><i class="ri-printer-fill"></i></button> Ticket Pago Cliente</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="html-imprimir-comprobante" class="text-center" > </div>                   
                </div>
                
              </div>
            </div>
          </div>
          <!-- End::modal-imprimir_ticket -->

          <!-- MODAL - VER FOTO -->
          <div class="modal fade modal-effect" id="modal-ver-imgenes" tabindex="-1" aria-labelledby="modal-ver-imgenes" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title fs-13 title-ver-imgenes" id="modal-ver-imgenesLabel1">Imagen</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body html_modal_ver_imgenes">
                  
                </div>
                <div class="modal-footer py-2">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" ><i class="las la-times fs-lg"></i> Close</button>                  
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

        </div>
      </div>
      <!-- End::app-content -->

      <?php } else { $title_submodulo ='Clientes'; $descripcion ='Lista de Clientes del sistema!'; $title_modulo = 'Ventas'; include("403_error.php"); }?>   


      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

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

    <!-- Dropzone JS -->
    <script src="../assets/libs/dropzone/dropzone-min.js"></script>

    <script src="scripts/persona_cliente.js?version_jdl=1.31"></script>
    <script src="scripts/js_facturacion_cliente.js?version_jdl=1.31"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>


  </body>

  </html>
<?php
}
ob_end_flush();
?>