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
  <html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" style="--primary-rgb: 78, 172, 76;" data-bg-img="bgimg4" data-menu-styles="dark" data-toggled="icon-overlay-close">

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
                <button type="button" class="btn btn-danger btn-cancelar btn-regresar m-r-10px" onclick="wiev_tabla_formulario(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                <div>
                  <p class="fw-semibold fs-18 mb-0 title-body-pagina">Lista de clientes!</p>
                  <span class="fs-semibold text-muted detalle-body-pagina">Adminstra de manera eficiente tus clientes.</span>
                </div>
              </div>
            </div>

            <div class="btn-list mt-md-0 mt-2">
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="javascript:void(0);">Realizar Ventas</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Clientes</li>
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
                                        
                  <!-- ::::::::::::::::::::: FILTRO MES A DE AFILIACION :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_mes_afiliacion" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_dia');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Mes de Afiliación
                        <span class="charge_filtro_mes_afiliacion"></span>
                      </label>
                      <select class="form-control" name="filtro_mes_afiliacion" id="filtro_mes_afiliacion" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > <!-- lista de categorias --> </select>
                    </div>
                  </div>
                  <!-- ::::::::::::::::::::: FILTRO DISTRITO :::::::::::::::::::::: -->
                  <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                    <div class="form-group">
                      <label for="filtro_distrito" class="form-label">                         
                        <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_distrito');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                        Distrito
                        <span class="charge_filtro_distrito"></span>
                      </label> 
                      <select class="form-control" name="filtro_distrito" id="filtro_distrito" onchange="cargando_search(); delay(function(){filtros()}, 50 );" > </select>
                    </div>
                  </div>                  
                    
                </div>
                <div class="card-body">                      
                      
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
                          <th>Direccion</th>                          
                          <th>CP</th>
                          <th>Distrito</th>
                          <th>Provincia</th>
                          <th>Departamento</th>                                
                          <th class="text-center">Observación.</th>

                          <th class="text-center">Nombres</th>
                          <th class="text-center">Tipo Documento</th>
                          <th class="text-center">Número Documento</th>

                        </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-center">Acciones</th>
                          <th>Cliente</th>
                          <th>Direccion</th>                          
                          <th>CP</th>
                          <th>Distrito</th>
                          <th>Provincia</th>
                          <th>Departamento</th>                                
                          <th class="text-center">Observación.</th>

                          <th class="text-center">Nombres</th>
                          <th class="text-center">Tipo Documento</th>
                          <th class="text-center">Número Documento</th>

                        </tr>
                      </tfoot>
                    </table>
                  </div>                 
                                 
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
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="direccion">Dirección: <sup class="text-danger">*</sup></label>
                                <textarea name="direccion" class="form-control inpur_edit" id="direccion" placeholder="ejemp: Jr las flores - Al costado del colegio" rows="2" ></textarea>
                              </div>
                            </div>

                            <!-- Select Zona antena -->
                            <div class="col-12 col-sm-3 col-md-3 col-lg-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="idcentropoblado">
                                <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_centro_poblado(); limpiar_centro_poblado();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('centroPbl');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                Centro Poblado <sup class="text-danger">*</sup><span class="charge_idctroPbl"></span></label>
                                <select name="idcentropoblado" id="idcentropoblado" class="form-control" placeholder="Selecionar"></select>
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

                            <!-- fecha afiliacion -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 col-xxl-3" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="fecha_afiliacion">Fecha Afiliación <sup class="text-danger">(no editable)*</sup></label>
                                <input type="date" name="fecha_afiliacion" class="form-control inpur_edit" id="fecha_afiliacion" readonly />
                              </div>
                            </div>                            
                           
                            <!--NOTA -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" style="margin-bottom: 20px;">
                              <div class="form-group">
                                <label class="form-label" for="nota">Nota </label>
                                <textarea class="form-control inpur_edit" name="nota" id="nota" cols="30" rows="2" placeholder="ejemp: Se removio el servicio por deuda" ></textarea>
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
           
          </div>          
          <!-- End::row-1 -->           

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

          <!-- MODAL::REGISTRAR CENTRO POBLADO - charge 5 -->
          <div class="modal fade modal-effect" id="modal-agregar-centro-poblado" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-centro-poblado" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-scrollabel">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modal-agregar-centro-poblado">Centro poblado</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form name="form-agregar-centro-poblado" id="form-agregar-centro-poblado" method="POST" class="needs-validation" novalidate>
                    <div class="row" id="cargando-5-fomulario">
                      <input type="hidden" name="idcentro_poblado" id="idcentro_poblado">
                      <input type="hidden" name="idubigeo_distrito" id="idubigeo_distrito">

                      <div class="col-12">
                        <div class="form-label">
                          <label for="nombre_cp" class="form-label">Nombre(*)</label>
                          <input class="form-control" name="nombre_cp" id="nombre_cp" />
                        </div>
                      </div>                        

                      <!-- Distrito -->
                      <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-xl-12 col-xxl-12" style="margin-bottom: 20px;">
                        <div class="form-group">
                          <label for="distrito_cp" class="form-label">Distrito: </label></label>
                          <select name="distrito_cp" id="distrito_cp" class="form-control" placeholder="Seleccionar" onchange="delay(function(){llenar_dep_prov_cp(this)}, 50 ); ">
                          </select>
                        </div>
                      </div>

                      <div class="col-6">
                        <div class="form-label">
                          <label for="provincia_cp" class="form-label">Provincia(*)</label>
                          <input class="form-control" name="provincia_cp" id="provincia_cp" readonly />
                        </div>
                      </div>

                      <div class="col-6">
                        <div class="form-label">
                          <label for="departamento_cp" class="form-label">Departamento(*)</label>
                          <input class="form-control" name="departamento_cp" id="departamento_cp" readonly />
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="descripcion_cp" class="form-label">Descripcion</label>
                          <textarea class="form-control" name="descripcion_cp" id="descripcion_cp" cols="30" rows="2"></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="row" id="cargando-6-fomulario" style="display: none;">
                      <div class="col-lg-12 text-center">
                        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h4 class="bx-flashing">Cargando...</h4>
                      </div>
                    </div>
                    <button type="submit" style="display: none;" id="submit-form-cp">Submit</button>
                  </form>
                </div>
                <div class="modal-footer justify-content-between">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal" onclick="limpiar_centro_poblado();"><i class="las la-times"></i> Close</button>
                  <button type="button" class="btn btn-sm btn-primary" id="guardar_registro_cp"><i class="bx bx-save bx-tada"></i> Guardar</button>
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

    <script src="scripts/cliente.js?version_jdl=1.31"></script>
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