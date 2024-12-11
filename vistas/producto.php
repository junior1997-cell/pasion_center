<?php
//Activamos el almacenamiento en el buffer
ob_start();
date_default_timezone_set('America/Lima');
require "../config/funcion_general.php";
session_start();
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-bg-img="bgimg4" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close">

  <head>
    <?php $title_page = "Productos";
    include("template/head.php"); ?>

    <style>
      #tabla-productos td {        
        vertical-align: middle !important;
        line-height: 1.462 !important;
        font-size: .625rem !important;
        font-weight: 50 !important;
      }
      #tabla-productos_filter label{ width: 100% !important; }
      #tabla-productos_filter label input{ width: 100% !important; }
    </style>
  </head>

  <body id="body-productos">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['producto'] == 1) { ?> <!-- .:::: PERMISO DE MODULO ::::. -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_producto(); create_code_producto('PR');  calculo_precios();"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Productos</p>
                    <span class="fs-semibold text-muted">Administra los productos.</span>
                  </div>
                </div>
              </div>
              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <!-- <li class="breadcrumb-item">
                    <div class="form-check form-switch mb-0">
                      <label class="form-check-label" for="generar-cod-correlativo"></label>
                      <input class="form-check-input cursor-pointer" type="checkbox" id="generar-cod-correlativo" name="generar-cod-correlativo" onchange="create_code_producto('PR');" checked data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Activar generador código de barra correlativamente automático">
                    </div>
                  </li> -->
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Articulos</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xxl-12 col-xl-12">
                <div>
                  <div class="card custom-card">
                    <div class="card-header">
                      <!-- ::::::::::::::::::::: FILTRO CATEGORIA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="filtro_categoria" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_categoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Categoría
                            <span class="charge_filtro_categoria"></span>
                          </label>
                          <select class="form-control" name="filtro_categoria" id="filtro_categoria" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>
                      <!-- ::::::::::::::::::::: FILTRO UNIDAD DE MEDIDA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="filtro_unidad_medida" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_unidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Unidad Medida
                            <span class="charge_filtro_unidad_medida"></span>
                          </label>
                          <select class="form-control" name="filtro_unidad_medida" id="filtro_unidad_medida" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>
                      <!-- ::::::::::::::::::::: FILTRO MARCA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="filtro_marca" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_marca();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Unidad Marca
                            <span class="charge_filtro_marca"></span>
                          </label>
                          <select class="form-control" name="filtro_marca" id="filtro_marca" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <!-- ------------ Tabla de Productos ------------- -->
                      <div class="table-responsive" id="div-tabla">
                        <table class="table table-bordered w-100" style="width: 100%;" id="tabla-productos">
                          <thead>
                            <tr>
                              <th colspan="15" class="bg-danger buscando_tabla" style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                            </tr>
                            <tr>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">#</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">Acciones</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Stock</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">U.M.</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Compra</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Venta</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Descripción</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Estado</th>

                              <th style="border-top: 1px solid #f3f3f3 !important;">Categoria</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Marca</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código Alterno</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código Alterno</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-center">#</th>
                              <th class="text-center">Acciones</th>
                              <th>Código</th>
                              <th>Nombre</th>
                              <th>Stock</th>
                              <th>U.M.</th>
                              <th>P. Compra</th>
                              <th>P. Venta</th>
                              <th>Descripción</th>
                              <th>Estado</th>

                              <th>Categoria</th>
                              <th>Marca</th>
                              <th>Nombre</th>
                              <th>Código</th>
                              <th>Código Alterno</th>
                            </tr>
                          </tfoot>

                        </table>

                      </div>
                      <!-- ------------ Formulario de Productos ------------ -->
                      <div class="div-form" style="display: none;">
                        <form name="form-agregar-producto" id="form-agregar-producto" method="POST" class="needs-validation" novalidate>

                          <ul class="nav nav-tabs tab-style-2 mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="info_producto" data-bs-toggle="tab" data-bs-target="#info_producto-pane" type="button" role="tab" aria-selected="true"><i class="ri-gift-line me-1 align-middle"></i> Producto</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="info_presentacion" data-bs-toggle="tab" data-bs-target="#info_presentacion-pane" type="button" role="tab" aria-selected="false" onclick="capt_nombre_um();"><i class="fa-solid fa-layer-group fa-fw"></i> Presentaciones</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="imagenes" data-bs-toggle="tab" data-bs-target="#imagenes-pane" type="button" role="tab" aria-selected="false"><i class="fa-solid fa-photo-film"></i> Imagenes</button>
                            </li>
                          </ul>


                          <div class="tab-content">
                            <div class="tab-pane fade show active text-muted" id="info_producto-pane" role="tabpanel" tabindex="0">
                              <div class="row gy-2" id="cargando-1-formulario">
                                <!-- ID -->
                                <input type="hidden" name="idproducto" id="idproducto" />
                                <input type="hidden" name="tipo" id="tipo" value="PR" />
                                <input type="hidden" name="sucursal" id="sucursal" value="1" />

                                <!-- ----------------- CODIGO --------------- -->
                                <div class="col-md-4 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="codigo" class="form-label">Código Sistema <span class="charge_codigo"></span></label>
                                    <input type="text" class="form-control bg-light" name="codigo" id="codigo" onkeyup="mayus(this);" readonly data-bs-toggle="tooltip" data-bs-original-title="No se puede editar" />
                                  </div>
                                </div>
                                <div class="col-md-4 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="codigo_alterno" class="form-label">
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="generarcodigonarti('no');" data-bs-toggle="tooltip" title="Generar Codigo con el nombre de producto."><i class="las la-sync-alt"></i></span>
                                      Código Propio <span class="charge_codigo_alterno"></span>
                                    </label>
                                    <input type="text" class="form-control " name="codigo_alterno" id="codigo_alterno" onkeyup="mayus(this);" placeholder="ejemp: PR00001" />
                                  </div>
                                </div>
                                <!-- ----------------- Unidad Medida --------------- -->
                                <div class="col-md-4 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="u_medida" class="form-label">
                                      <!-- <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_u_medida(); limpiar_form_um();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idunidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      U. Medida
                                      <span class="charge_idunidad_medida"></span>
                                    </label>
                                    <select class="form-control" name="u_medida" id="u_medida">
                                      <!-- lista de u medidas -->
                                    </select>
                                  </div>
                                </div>
                                <!-- ----------------- Categoria --------------- -->
                                <div class="col-md-4 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                  <div class="form-group">
                                    <label for="categoria" class="form-label">
                                      <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_categoria(); limpiar_form_cat();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idcategoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Categoría
                                      <span class="charge_idcategoria"></span>
                                    </label>
                                    <select class="form-control" name="categoria" id="categoria">
                                      <!-- lista de categorias -->
                                    </select>
                                  </div>
                                </div>

                                <!-- ----------------- Marca --------------- -->
                                <div class="col-md-4 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                  <div class="form-group">
                                    <label for="marca" class="form-label">
                                      <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_marca(); limpiar_form_marca();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                      <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idmarca();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                      Marca
                                      <span class="charge_idmarca"></span>
                                    </label>
                                    <select class="form-control" name="marca" id="marca">
                                      <!-- lista de marcas -->
                                    </select>
                                  </div>
                                </div>
                                <!-- ----------------- Tipo igv --------------- -->
                                <div class="col-md-4 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                                  <div class="form-group">
                                    <label for="tipo_igv" class="form-label">Tipo igv </label>
                                    <select class="form-control" name="tipo_igv" id="tipo_igv" onchange="select_tipo_igv();">
                                      <!-- lista de tipo_igvs -->
                                    </select>
                                  </div>
                                </div>
                                <!-- --------- NOMBRE ------ -->
                                <div class="col-md-6 col-lg-6 col-xl-4 col-xxl-4 mt-3">
                                  <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre(*)</label>
                                    <textarea class="form-control" name="nombre" id="nombre" rows="1"></textarea>
                                  </div>
                                </div>

                                <!-- --------- DESCRIPCION ------ -->
                                <div class="col-md-6 col-lg-6 col-xl-5 col-xxl-5 mt-3">
                                  <div class="form-group">
                                    <label for="descripcion" class="form-label">Descripción(*)</label>
                                    <textarea class="form-control" name="descripcion" id="descripcion" rows="1"></textarea>
                                  </div>
                                </div>

                                <!-- ----------------- STOCK --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="stock" class="form-label">Stock(*)</label>
                                    <input type="number" class="form-control" name="stock" id="stock" />
                                  </div>
                                </div>

                                <!-- ----------------- STOCK MININO --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="stock_min" class="form-label">Stock Minimo(*)</label>
                                    <input type="number" class="form-control" name="stock_min" id="stock_min" />
                                  </div>
                                </div>

                                <!-- ----------------- PRECIO COMPRA --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="precio_c" class="form-label">Precio Compra(*)</label>
                                    <input type="number" class="form-control" name="precio_c" id="precio_c" step="0.01" />
                                  </div>
                                </div>

                                <!-- ----------------- PRECIO VENTA  --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 mt-3 precio_v_igv">
                                  <div class="form-group">
                                    <label for="precio_v" class="form-label">P. Venta <span class="segun_t_igv"> IGV</span> (*)</label>
                                    <input type="number" class="form-control" name="precio_v" id="precio_v" step="0.01" />
                                  </div>
                                </div>
                                <!-- ----------------- Ganancia Máxima --------------- -->
                                <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="x_ganancia_max" class="form-label">% Ganancia Máx</label>
                                    <input type="number" class="form-control" name="x_ganancia_max" id="x_ganancia_max" step="0.01" />
                                  </div>
                                </div>

                                <!-- ----------------- PRECIO VENTA MINIMA --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="precio_v_min" class="form-label">P. Venta Min(*)</label>
                                    <input type="number" class="form-control" name="precio_v_min" id="precio_v_min" step="0.01" />
                                  </div>
                                </div>

                                <!-- ----------------- Ganancia Minima --------------- -->
                                <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="x_ganancia_min" class="form-label"> % Ganancia Min</label>
                                    <input type="number" class="form-control" name="x_ganancia_min" id="x_ganancia_min" step="0.01" />
                                  </div>
                                </div>

                                <!-- ----------------- PESO --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 mt-3">
                                  <div class="form-group">
                                    <label for="Peso_kg" class="form-label">Peso (KGM)</label>
                                    <input type="number" class="form-control" name="Peso_kg" id="Peso_kg" step="0.01" />
                                  </div>
                                </div>
                                <!-- ----------------- Multi Precios --------------- -->
                                <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2 pt-3 div_multi_precio">
                                  <div class="form-group">
                                    <label for="multi_precio" class="form-label">¿MultiPlecio?</label>
                                    <div class="toggle toggle-secondary multi_precio" onclick="delay(function(){multiplecio()}, 100 );"> <span></span> </div>

                                  </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-8 col-xxl-8 pt-3 data_multi_p" style="display: none !important;">

                                  <div class="card-body" style="border-radius: 5px; box-shadow: 0 0 2px rgb(0 0 0), 0 1px 5px 4px rgb(255 255 255 / 60%);">
                                    <div class="row ">

                                      <div class="table-responsive">
                                        <span class="btn btn-teal-light btn-border-down btn-sm" onclick="add_fila();"> <i class="las la-plus"></i> Agregar</span>
                                        <table class="table tbl_multiprecio" style="display: none;">
                                          <thead>
                                            <tr style="background-color: rgb(102, 255, 153);">
                                              <th scope="col">Acc</th>
                                              <th scope="col">Nombre</th>
                                              <th scope="col">Precio</th>
                                            </tr>
                                          </thead>
                                          <tbody class="new_row_table_precio">
                                          </tbody>
                                        </table>
                                        <div class="message_init">
                                          <div class="alert alert-solid-secondary alert-dismissible fade show m-3">
                                            No tiene ningun precio agregado, click en el boton + agregar!
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                              </div>
                              <div class="row" id="cargando-2-fomulario" style="display: none;">
                                <div class="col-lg-12 text-center">
                                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                                  <h4 class="bx-flashing">Cargando...</h4>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane fade " id="info_presentacion-pane" role="tabpanel" tabindex="0">
                              <div class="row gy-2" id="cargando-1-formulario">

                                <div class="col-12 col-lg-6 col-xl-5 col-xxl-5">
                                  <div class="row">
                                    <div class="div_datos badge bg-primary-transparent mb-2">
                                      <span class="view_name"></span> |
                                      <span class="view_um"></span> |
                                      <span class="view_pv"></span>

                                    </div>
                                    <div class="row justify-content-end ">
                                      <div class="col-4 btn btn-info btn-sm boton_add_presentacion" onclick="add_presentacion(event);"> Add Presentación</div>
                                    </div>

                                    <!-- ----------------- CODIGO --------------- -->
                                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4  mt-3">
                                      <div class="form-group">
                                        <label for="codigo_alterno_presentacion" class="form-label">
                                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="generarcodigonarti('_presentacion');" data-bs-toggle="tooltip" title="Generar Código con el nombre de la presentación."><i class="las la-sync-alt"></i></span>
                                          Código Propio <span class="charge_codigo_alterno_presentacion"></span>
                                        </label>
                                        <input type="text" class="form-control " name="codigo_alterno_presentacion" id="codigo_alterno_presentacion" onkeyup="mayus(this);" placeholder="ejemp: PR00001" />
                                      </div>
                                    </div>
                                    <!-- --------- NOMBRE Presentación ------ -->
                                    <div class="col-md-8 col-lg-8 col-xl-8 col-xxl-8 mt-3">
                                      <div class="form-group">
                                        <label for="nombre_presentacion" class="form-label">Nombre Presentación(*)</label>
                                        <textarea class="form-control" name="nombre_presentacion" id="nombre_presentacion" rows="1"></textarea>
                                      </div>
                                    </div>
                                    <!-- ----------------- Unidad Medida --------------- -->
                                    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6  mt-3">
                                      <div class="form-group">
                                        <label for="u_medida" class="form-label">
                                          <!-- <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_u_medida(); limpiar_form_um();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idunidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                          Nueva U.M
                                          <span class="charge_idunidad_medida"></span>
                                        </label>
                                        <select class="form-control" name="u_medida_presentacion" id="u_medida_presentacion">
                                          <!-- lista de u medidas -->
                                        </select>
                                      </div>
                                    </div>
                                    <!-- ----------------- Contiene --------------- -->
                                    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                                      <div class="form-group">
                                        <label for="cantidadxpresent" class="form-label">Contiene en <span class="text-primary um_antigua"><i class="fas fa-spinner fa-pulse fa-sm"></i></span> </label>
                                        <input type="number" class="form-control" name="cantidadxpresent" id="cantidadxpresent" step="0.01" onkeyup="calcular_pr_c();" />
                                      </div>
                                    </div>
                                    <!-- ----------------- PRECIO COMPRA --------------- -->
                                    <div class=" col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                      <div class="form-group">
                                        <label for="precio_c_presentacion" class="form-label">Precio Compra(*)</label>
                                        <input type="number" class="form-control" name="precio_c_presentacion" id="precio_c_presentacion" step="0.01" readonly />
                                      </div>
                                    </div>

                                    <!-- ----------------- PRECIO VENTA  --------------- -->
                                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                      <div class="form-group">
                                        <label for="precio_v_presentacion" class="form-label">Precio Venta(*)</label>
                                        <input type="number" class="form-control" name="precio_v_presentacion" id="precio_v_presentacion" step="0.01" />
                                      </div>
                                    </div>
                                    <!-- ----------------- PRECIO VENTA MINIMA --------------- -->
                                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3">
                                      <div class="form-group">
                                        <label for="precio_v_min_presentacion" class="form-label">Precio Venta Min(*)</label>
                                        <input type="number" class="form-control" name="precio_v_min_presentacion" id="precio_v_min_presentacion" step="0.01" />
                                      </div>
                                    </div>

                                  </div>
                                </div>
                                <div class="col-12 col-lg-6 col-xl-7 col-xxl-7">

                                  <div class=" col-12 badge bg-primary-transparent font-size-15px text-center ">Lista de Presentaciones </div>

                                  <div class="table-responsive">
                                    <table class="table text-nowrap">
                                      <thead class="table-primary">
                                        <tr>
                                          <th scope="col">Acc.</th>
                                          <th scope="col">Código</th>
                                          <th scope="col">Presentación</th>
                                          <th scope="col">U.M</th>
                                          <th scope="col">Cantidad</th>
                                          <th scope="col">P.C</th>
                                          <th scope="col">P.V</th>
                                          <th scope="col">P.V.M</th>
                                        </tr>
                                      </thead>
                                      <tbody class="tabla_new_row">
                                        <tr class="tabla_sindata">
                                          <th colspan="8" class="bg-info " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Ningún Registro... </th>
                                        </tr>

                                      </tbody>
                                    </table>
                                  </div>

                                </div>
                              </div>


                              <div class="row" id="cargando-2-fomulario" style="display: none;">
                                <div class="col-lg-12 text-center">
                                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                                  <h4 class="bx-flashing">Cargando...</h4>
                                </div>
                              </div>

                            </div>

                            <div class="tab-pane fade " id="imagenes-pane" role="tabpanel" tabindex="0">

                              <div class="row">

                                <!-- ----------- Imagen Cuadrado -------------- -->
                                <div class="p-3 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2">
                                  <h6 class="card-title text-center">Imagen Cuadrado:</h6>
                                  <div class="col-md-12 border-top p-3">

                                    <div class="my-2 text-center">
                                      <div class="btn-group edit_img">
                                        <button type="button" class="btn btn-primary py-1" id="doc1_i"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir </button>
                                        <input type="hidden" id="doc_old_1" name="doc_old_1" />
                                        <input style="display: none;" id="doc1" type="file" name="doc1" accept="application/pdf, image/*" class="docpdf" />
                                        <button type="button" class="btn btn-info py-1" onclick="re_visualizacion(1, 'assets/modulo/productos', '60%'); reload_zoom();"><i class='bx bx-refresh bx-spin fs-5'></i>Refrescar</button>
                                      </div>
                                    </div>

                                    <!-- imagen -->
                                    <div id="doc1_ver" class="text-center ">
                                      <img id="img_defect" src="../assets/images/default/product.jpg" alt="" width="40%" />
                                    </div>
                                    <div id="doc1_nombre"><!-- aqui va el nombre del pdf --></div>
                                  </div>
                                </div>
                                <!-- ----------- Imagen Horizontal 2222222222 -------------- -->
                                <div class="p-3 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2">
                                  <h6 class="card-title text-center">Imagen Horizontal:</h6>
                                  <div class="col-md-12 border-top p-3">

                                    <div class="my-2 text-center">
                                      <div class="btn-group edit_img">
                                        <button type="button" class="btn btn-primary py-1" id="doc2_i"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir </button>
                                        <input type="hidden" id="doc_old_2" name="doc_old_2" />
                                        <input style="display: none;" id="doc2" type="file" name="doc2" accept="application/pdf, image/*" class="docpdf" />
                                        <button type="button" class="btn btn-info py-1" onclick="re_visualizacion(2, 'assets/modulo/productos', '60%'); reload_zoom();"><i class='bx bx-refresh bx-spin fs-5'></i>Refrescar</button>
                                      </div>
                                    </div>

                                    <!-- imagen -->
                                    <div id="doc2_ver" class="text-center ">
                                      <img id="img_defect" src="../assets/images/default/img_defecto_rectangulo.png" alt="" width="60%" />
                                    </div>
                                    <div id="doc2_nombre"><!-- aqui va el nombre del pdf --></div>
                                  </div>
                                </div>
                                <!-- ----------- Imagen vertical 3333 -------------- -->
                                <div class="p-3 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mt-2">
                                  <h6 class="card-title text-center">Imagen vertical:</h6>
                                  <div class="col-md-12 border-top p-3">

                                    <div class="my-2 text-center">
                                      <div class="btn-group edit_img">
                                        <button type="button" class="btn btn-primary py-1" id="doc3_i"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir </button>
                                        <input type="hidden" id="doc_old_3" name="doc_old_3" />
                                        <input style="display: none;" id="doc3" type="file" name="doc3" accept="application/pdf, image/*" class="docpdf" />
                                        <button type="button" class="btn btn-info py-1" onclick="re_visualizacion(3, 'assets/modulo/productos', '60%'); reload_zoom();"><i class='bx bx-refresh bx-spin fs-5'></i>Refrescar</button>
                                      </div>
                                    </div>

                                    <!-- imagen -->
                                    <div id="doc3_ver" class="text-center ">
                                      <img id="img_defect" src="../assets/images/default/img_defecto_vert.png" alt="" width="40%" />
                                    </div>
                                    <div id="doc3_nombre"><!-- aqui va el nombre del pdf --></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Chargue -->
                          <div class="p-l-25px col-lg-12" id="barra_progress_producto_div" style="display: none;">
                            <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                              <div id="barra_progress_producto" class="progress-bar" style="width: 0%">
                                <div class="progress-bar-value">0%</div>
                              </div>
                            </div>
                          </div>
                          <!-- Submit -->
                          <button type="submit" style="display: none;" id="submit-form-producto">Submit</button>

                        </form>
                      </div>
                    </div>
                    <div class="card-footer border-top-0">
                      <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                      <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::row-1 -->


            <!-- MODAL - VER DETALLE -->
            <div class="modal fade" id="modal-ver-detalle-producto">
              <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="modal-ver-detalle-productoLabel1"><b>Detalles</b> - Producto - <b class="name_producto"></b> </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!--<div ></div>
                    <div class="text-center" id="html-detalle-imagen">-->
                    <div class="row" id="html-detalle-producto">
                      
                      <div class="col-xl-4">
                        <div class="row">
                          <div class="col-xl-12">
                            <div class="card custom-card">
                              <div class="card-header">
                                <div class="card-title">
                                  Datos del Producto - <span class="text-primary">#SPK-1023</span>
                                </div>
                              </div>
                              <div class="card-body p-0">
                                <div class="p-3 border-bottom border-block-end-dashed">
                                  <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fs-14 fw-semibold">Delivery address :</span>
                                    <button class="btn btn-icon btn-wave btn-primary btn-sm waves-effect waves-light"><i class="ri-pencil-line"></i></button>
                                  </div>
                                  <p class="mb-2 text-muted"><span class="fw-semibold text-default">Landmark : </span>MIG-1-11</p>
                                  <p class="mb-2 text-muted"><span class="fw-semibold text-default">Street : </span>Monroe Street</p>
                                  <p class="mb-2 text-muted"><span class="fw-semibold text-default">City : </span>Georgetown</p>
                                  <p class="mb-2 text-muted"><span class="fw-semibold text-default">State : </span>Washington,D.C</p>
                                  <p class="mb-2 text-muted"><span class="fw-semibold text-default">Country : </span>USA</p>
                                  <p class="mb-0 text-muted"><span class="fw-semibold text-default">Zipcode : </span>200071</p>
                                </div>
                                <div class="p-3 border-bottom border-block-end-dashed">
                                  <div class="mb-3">
                                    <span class="fs-14 fw-semibold">Send updates to :</span>
                                  </div>
                                  <p class="mb-2 text-muted">
                                    <span class="fw-semibold text-default">Phone : </span>
                                    (555)-0123-1210
                                  </p>
                                  <p class="mb-0 text-muted">
                                    <span class="fw-semibold text-default">Email : </span>
                                    jsontaylor2134@gmail.com
                                  </p>
                                </div>
                                <div class="p-3">
                                  <div class="mb-3">
                                    <span class="fs-14 fw-semibold">Order Summary</span>
                                  </div>
                                  <p class="mb-2 text-muted">
                                    <span class="fw-semibold text-default">Ordered Date</span>
                                    24,Nov 2022
                                  </p>
                                  <p class="mb-2 text-muted">
                                    <span class="fw-semibold text-default">Ordered Time :</span>
                                    11:47AM
                                  </p>
                                  <p class="mb-0 text-muted">
                                    <span class="fw-semibold text-default">Payment Interface :</span>
                                    UPI
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-xl-4">
                        <div class="card custom-card">
                          <div class="card-header">
                            <div class="card-title">
                              Presentaciones
                            </div>
                            <div class="ms-auto text-success">#SPK1218153635</div>
                          </div>
                          <div class="card-body">
                            <div class="order-track">
                              <div class="accordion" id="basicAccordion3">
                                <div class="accordion-item border-0 bg-transparent next-step">
                                  <div class="accordion-header" id="headingFour">
                                    <a class="px-0 pt-0 collapsed" href="javascript:void(0)" role="button" data-bs-toggle="collapse" data-bs-target="#basicFour" aria-expanded="false" aria-controls="basicFour">
                                      <div class="d-flex mb-2">
                                        <div class="me-2">
                                          <span class="avatar avatar-md avatar-rounded bg-primary-transparent text-primary border"><i class="fe fe-package fs-12"></i></span>
                                        </div>
                                        <div class="flex-fill">
                                          <p class="fw-semibold mb-0 fs-14">Out For Delivery</p>
                                          <span class="text-muted fs-12">Nov 03, 15:10 (expected)</span>
                                        </div>
                                      </div>
                                    </a>
                                  </div>
                                  <div id="basicFour" class="accordion-collapse border-top-0 collapse show" aria-labelledby="headingFour" data-bs-parent="#basicAccordion3">
                                    <div class="accordion-body pt-0 ps-5">
                                      <div class="fs-11">
                                        <p class="mb-0">Your order is out for devlivery</p>
                                        <span class="text-muted op-5">Nov 03, 2022, 15:36 (expected)</span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-xl-4">
                        <div class="row">
                          <div class="col-xl-12">
                            <div class="card custom-card">
                              <div class="card-header d-flex justify-content-between">
                                <div class="card-title">
                                  Imagenes 
                                </div>
                              </div>
                              <div class="card-body p-0">

                                <div class="kanban-content mt-2 mb-2 border-bottom border-block-end-dashed">
                                  <div class="task-image mt-2 mb-2">
                                    <img src="../assets/images/media/media-41.jpg" class="img-fluid rounded kanban-image" style="width: 60%;" alt="">
                                  </div>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>




                  </div>
                  
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
                </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End::Modal-VerDetalles -->


        </div>
    </div>
    <!-- End::app-content -->
  <?php } else {
        $title_submodulo = 'Producto';
        $descripcion = 'Lista de Producto del sistema!';
        $title_modulo = 'Articulos';
        include("403_error.php");
      } ?>

  <?php include("template/search_modal.php"); ?>
  <?php include("template/footer.php"); ?>
  </div>

  <?php include("template/scripts.php"); ?>
  <?php include("template/custom_switcherjs.php"); ?>

  <?php include("template/new_general_regist.php"); ?>

  <script src="scripts/producto.js?version_jdl=1.12"></script>

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