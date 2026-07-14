@extends('layouts.master')
@section('title')
    Actualizaci&oacute&oacute;n de  producto
@endsection
@section('css')
    <style>
        .dropzone-wrapper {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .dropzone-wrapper:hover {
            border-color: #0d6efd;
            background-color: #e9ecef;
        }

        .dropzone-wrapper.dragover {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }

        .galeria-imagen .imagen-card {
            position: relative;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .galeria-imagen .imagen-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .galeria-imagen .imagen-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .galeria-imagen .imagen-card .badge-tipo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 10px;
        }

        .galeria-imagen .imagen-card .acciones {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 8px;
            display: flex;
            justify-content: center;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .galeria-imagen .imagen-card:hover .acciones {
            opacity: 1;
        }

        .galeria-imagen .imagen-card .acciones .btn {
            padding: 2px 8px;
            font-size: 12px;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Modificacion de producto" pagetitle="Productos" />
    <form id="editproduct-form" autocomplete="off" class="needs-validation" method="post"
          novalidate action="{{route('productos.update',$id)}}">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1 ">Informaci&oacute;n</h5>
                                <p class="text-muted mb-0">Ingrese/Modifique los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="display:none !important;">
                            <label class="form-label">Product description</label>

                            <div id="ckeditor-classic">
                                <p>Tommy Hilfiger men striped pink sweatshirt. Crafted with cotton. Material composition is
                                    100% organic cotton. This is one of the world’s leading designer lifestyle brands and is
                                    internationally recognized for celebrating the essence of classic American cool style,
                                    featuring preppy with a twist designs.</p>
                                <ul>
                                    <li>Full Sleeve</li>
                                    <li>Cotton</li>
                                    <li>All Sizes available</li>
                                    <li>4 Different Color</li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <label class="form-label">Instancia de inventario</label>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/instancias" class="float-end text-decoration-underline">+1 Instancia</a>
                                </div>
                            </div>
                            <div>
                                <select onchange="$('.error-msg').hide();  " class="form-select" data-choices  required
                                        id="choices-category-input" name="codinst">
                                    @foreach($instancias as $instancia)
                                        <option  {{($instancia->codinst == $producto->codinst)?'selected':''}} style="margin-left: {{($instancia->nivel-1) * 14}}px !important;" value="{{$instancia->codinst}}">{!! $instancia->label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-msg mt-1">Por favor, seleccione una instancia del inventario para clasificar este producto.</div>
                        </div>
                    </div>
                </div>

                <div class="card "  >

                    <div class="card-body">
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod" disabled value="{{$producto->codprod}}"
                                           placeholder="" required onclick="$('#invalidcodprod').html('C&oacute;digo'); $('#invalidcodprod').removeClass('text-danger');">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"  value="{{$producto->refere}}"
                                           placeholder="Ej: C&oacute;digo Barra">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="edit">

                            <input type="hidden" class="form-control" id="isadmin" name="isadmin"
                                   value="{{(Auth::user() and auth()->user()->type == 'admin')? 1: 0}}">

                            <input type="text" class="form-control d-none" id="product-id-input">

                            <input type="text" class="form-control" id="descrip" value="{{$producto->descrip}}"
                                   placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2"   value="{{$producto->descrip2}}"
                                   placeholder="Descripci&oacute;n 2" >
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3"  value="{{$producto->descrip3}}"
                                   placeholder="Descripci&oacute;n 3" >
                        </div>
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="marca">Marca</label>
                                    <input type="text" class="form-control" id="marca"  value="{{$producto->marca}}" name="marca"
                                           placeholder="Ej: POLAR">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad"value="{{$producto->unidad}}"
                                           placeholder="Ej: Kg">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6"  style="display: none">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="esexento" name="esexento"  {{($producto->esexento)?'checked':''}} value="1">
                                    <label class="form-check-label" for="esexento">Este producto es Exento?</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal" {{(isset($producto->exdecimal) and $producto->exdecimal == 1)?'checked':''}}    value="1">
                                    <label class="form-check-label" for="exdecimal">Uso de decimales para este producto?</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Sección de imágenes -->


                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Modificar</button>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <div>

                            <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false name="activo">
                                <option value="1" {{( $producto->activo == 1)?'selected':''}}>Activo</option>
                                <option value="0" {{( $producto->activo == 0)?'selected':''}}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>

                @if(Auth::user() and ( auth()->user()->can('products_cots_view')  or auth()->user()->can('products_prices')  )  )
                <div class="card"  >
                    <div class="card-header">
                        @if(Auth::user() and ( auth()->user()->can('products_cots')   )  )
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="preciod">Costo anterior</label>
                                <input type="text" class="form-control" id="preciodant" value="{{$producto->preciodant}}"  name="preciodant"
                                       placeholder="Ej: 15" required style="width: 70px">
                            </div>
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="preciod">Costo promedio</label>
                                <input type="text" class="form-control" id="preciodpro" value="{{$producto->preciodpro}}"  name="preciodpro"
                                       placeholder="Ej: 15" required style="width: 70px">
                            </div>
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="preciod">Costo</label>
                                <input type="text" class="form-control" id="preciod" value="{{$producto->preciod}}"  name="preciod"
                                       placeholder="Ej: 15" required style="width: 70px">
                            </div>
                        @endif
                        @if(Auth::user() and ( auth()->user()->can('products_cots_view')   )  and !( auth()->user()->can('products_cots')   )  )
                                <div class="d-flex justify-content-between">
                                   <label class="form-label" for="">Costo anterior: </label>
                                   <div>{{$producto->preciodant}}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                   <label class="form-label" for="">Costo promedio: </label>
                                   <div> {{$producto->preciodpro}}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="" >Costo actual: </label>
                                    <div>{{$producto->preciod}}</div>
                                </div>
                        @endif

                        @if(Auth::user() and (  auth()->user()->can('products_prices')  )  )
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="costod">Precio1</label>
                                    <input type="text" class="form-control" id="costod" value="{{$producto->costod}}" name="costod"
                                           placeholder="Ej: 18" style="width: 70px">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="costod2">Precio2</label>
                                    <input type="text" class="form-control" id="costod2" value="{{$producto->costod2}}"name="costod2"
                                           placeholder="Ej: 20" style="width: 70px">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="costod3">Precio3</label>
                                    <input type="text" class="form-control" id="costod3" value="{{$producto->costod3}}"name="costod3"
                                           placeholder="Ej: 22.5" required style="width: 70px">
                                </div>
                        @endif
                    </div>

                </div>
                @endif

            </div>
        </div>
    </form>
    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Imágenes del Producto</h5>
                <button type="button" class="btn btn-primary btn-sm" id="btnAgregarImagenes">
                    <i class="bi bi-plus-circle"></i> Agregar Imágenes
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Drop zone para subir imágenes -->
            <div class="dropzone-wrapper mb-3" id="dropzoneWrapper">
                <div class="dropzone-area" id="dropzoneArea">
                    <div class="text-center">
                        <i class="bi bi-cloud-upload" style="font-size: 48px;"></i>
                        <h5>Arrastra y suelta imágenes aquí</h5>
                        <p class="text-muted">o haz clic para seleccionar archivos</p>
                        <p class="text-muted small">Formatos: JPG, PNG, GIF, WebP (max 5MB)</p>
                        <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
                    </div>
                </div>
            </div>

            <!-- Galería de imágenes -->
            <div id="galeriaImagenes" class="row g-3">
                <!-- Aquí se cargarán las imágenes vía JavaScript -->
            </div>

            <!-- Barra de progreso -->
            <div id="progressBar" style="display: none;" class="mt-3">
                <div class="progress">
                    <div id="progressBarInner" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%">0%</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- create-product -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ URL::asset('build/js/backend/edit-product.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script> $(document).ready(function() {
            const codprod = $('#codprod').val();
            const dropzoneArea = $('#dropzoneArea');
            const fileInput = $('#fileInput');
            const galeria = $('#galeriaImagenes');
            const progressBar = $('#progressBar');
            const progressBarInner = $('#progressBarInner');
            const urlBase = '/productos-imagenes';

            let cargando = false;
            let cargandoImagenes = false; // Nuevo flag para controlar carga de imágenes

            // Cargar imágenes existentes
            cargarImagenes();

            // Eventos de dropzone
            dropzoneArea.on('click', function(e) {
                // Evitar que el click se propague al input file si ya estamos en él
                if (!$(e.target).closest('#fileInput').length) {
                    fileInput.click();
                }
            });

            dropzoneArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            dropzoneArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            dropzoneArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation(); // IMPORTANTE: evitar propagación
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    subirImagenes(files);
                }
            });

            // Manejar el cambio del input file con control de recursión
            fileInput.on('change', function(e) {
                // Prevenir múltiples disparos
                e.preventDefault();
                e.stopPropagation();

                if (this.files && this.files.length > 0) {
                    // Guardar referencia a los archivos
                    const files = this.files;

                    // IMPORTANTE: Resetear el input ANTES de procesar para evitar loops
                    // Usar setTimeout para que el reset no interfiera con el event loop
                    const inputElement = this;
                    setTimeout(function() {
                        inputElement.value = '';
                    }, 10);

                    subirImagenes(files);
                }
            });

            $('#btnAgregarImagenes').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.click();
            });

            // ============================================
            // EVENT DELEGATION - Los eventos se mantienen
            // incluso después de recargar la galería
            // ============================================

            // Evento para establecer como principal
            $(document).on('click', '.set-principal', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    setPrincipal(id);
                }
            });

            // Evento para establecer como thumbnail
            $(document).on('click', '.set-thumbnail', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    setThumbnail(id);
                }
            });

            // Evento para establecer como icono
            $(document).on('click', '.set-icono', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    setIcono(id);
                }
            });

            // Evento para eliminar imagen
            $(document).on('click', '.eliminar-imagen', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            eliminarImagen(id);
                        }
                    });
                }
            });

            // ============================================
            // FUNCIONES
            // ============================================

            function cargarImagenes() {
                // Prevenir múltiples llamadas simultáneas
                if (cargandoImagenes) {
                    return;
                }
                cargandoImagenes = true;

                $.ajax({
                    url: urlBase + '/' + codprod,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            renderizarGaleria(response);
                        }
                        cargandoImagenes = false;
                    },
                    error: function(xhr) {
                        console.error('Error al cargar imágenes:', xhr);
                        mostrarError('Error al cargar las imágenes');
                        cargandoImagenes = false;
                    }
                });
            }

            function renderizarGaleria(data) {
                // Limpiar la galería de manera segura
                const galeriaElement = galeria[0];
                if (galeriaElement) {
                    galeriaElement.innerHTML = '';
                }

                if (!data.imagenes || data.imagenes.length === 0) {
                    galeria.html(`
                <div class="col-12 text-center text-muted py-4">
                    <i class="bi bi-image" style="font-size: 48px;"></i>
                    <p>No hay imágenes para este producto</p>
                </div>
            `);
                    return;
                }

                // Construir HTML de manera eficiente
                let html = '';
                data.imagenes.forEach(function(imagen) {
                    const esPrincipal = imagen.tipo === 'principal' && imagen.activo === 1;
                    const esThumbnail = imagen.tipo === 'thumbnail' && imagen.activo === 1;
                    const esIcono = imagen.tipo === 'icono' && imagen.activo === 1;

                    let badges = '';
                    if (esPrincipal) badges += '<span class="badge bg-success me-1">Principal</span>';
                    if (esThumbnail) badges += '<span class="badge bg-info me-1">Thumbnail</span>';
                    if (esIcono) badges += '<span class="badge bg-warning me-1">Icono</span>';
                    if (!esPrincipal && !esThumbnail && !esIcono && imagen.tipo === 'secundaria') {
                        badges += '<span class="badge bg-secondary me-1">Secundaria</span>';
                    }

                    let botones = '';

                    if (!esPrincipal) {
                        botones += `<button class="btn btn-sm btn-success set-principal" data-id="${imagen.id}" title="Establecer como principal">
                    <i class="bi bi-star"></i>
                </button>`;
                    } else {
                        botones += `<button class="btn btn-sm btn-outline-success" disabled title="Ya es principal">
                    <i class="bi bi-star-fill"></i>
                </button>`;
                    }

                    if (!esPrincipal && !esThumbnail) {
                        botones += `<button class="btn btn-sm btn-info set-thumbnail" data-id="${imagen.id}" title="Establecer como thumbnail">
                    <i class="bi bi-image"></i>
                </button>`;
                    } else if (esThumbnail) {
                        botones += `<button class="btn btn-sm btn-outline-info" disabled title="Ya es thumbnail">
                    <i class="bi bi-image-fill"></i>
                </button>`;
                    }

                    if (!esPrincipal && !esIcono) {
                        botones += `<button class="btn btn-sm btn-warning set-icono" data-id="${imagen.id}" title="Establecer como icono">
                    <i class="bi bi-square"></i>
                </button>`;
                    } else if (esIcono) {
                        botones += `<button class="btn btn-sm btn-outline-warning" disabled title="Ya es icono">
                    <i class="bi bi-square-fill"></i>
                </button>`;
                    }

                    botones += `<button class="btn btn-sm btn-danger eliminar-imagen" data-id="${imagen.id}" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>`;

                    html += `
                <div class="col-md-3 col-sm-4 col-6 galeria-imagen">
                    <div class="imagen-card">
                        <img src="/${imagen.ruta}" alt="${imagen.nombre_original}" loading="lazy"
                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                        ${badges ? `<div class="badge-tipo">${badges}</div>` : ''}
                        <div class="acciones">
                            ${botones}
                        </div>
                        ${imagen.orden !== undefined ? `<small class="text-muted d-block text-center">Orden: ${imagen.orden}</small>` : ''}
                    </div>
                </div>
            `;
                });

                galeria.html(html);

                // Actualizar el contador de imágenes
                const totalActivas = data.imagenes.filter(img => img.activo === 1).length;
                $('.card-header .card-title').each(function() {
                    const text = $(this).text();
                    if (text.includes('Imágenes del Producto')) {
                        $(this).text(`Imágenes del Producto (${totalActivas})`);
                    }
                });
            }

            function subirImagenes(files) {
                // Validar cantidad de archivos
                if (files.length > 10) {
                    mostrarError('Solo puedes subir máximo 10 imágenes a la vez');
                    return;
                }

                const formData = new FormData();
                formData.append('codprod', codprod);

                let archivosValidos = 0;
                $.each(files, function(index, file) {
                    if (file.size > 5 * 1024 * 1024) {
                        mostrarError(`El archivo ${file.name} excede el tamaño máximo de 5MB`);
                        return;
                    }
                    formData.append('imagenes[]', file);
                    archivosValidos++;
                });

                if (archivosValidos === 0) {
                    return;
                }

                progressBar.show();
                progressBarInner.css('width', '0%');
                progressBarInner.text('0%');

                $.ajax({
                    url: urlBase + '/upload-multiple',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: false,
                    xhr: function() {
                        const xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBarInner.css('width', percent + '%');
                                progressBarInner.text(percent + '%');
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        if (response.success) {
                            progressBarInner.css('width', '100%');
                            progressBarInner.text('100%');
                            setTimeout(() => {
                                progressBar.hide();
                                progressBarInner.css('width', '0%');
                            }, 1500);

                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            cargarImagenes();
                        } else {
                            progressBar.hide();
                            if (response.errores && response.errores.length > 0) {
                                mostrarError('Errores: ' + response.errores.join(', '));
                            } else {
                                mostrarError('Error al subir las imágenes');
                            }
                        }
                    },
                    error: function(xhr) {
                        progressBar.hide();
                        let mensaje = 'Error al subir las imágenes';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errores = Object.values(xhr.responseJSON.errors).flat();
                            mensaje = errores.join(', ');
                        }
                        mostrarError(mensaje);
                    }
                });
            }

            function setPrincipal(id) {
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Estableciendo imagen como principal',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: urlBase + '/' + id + '/set-principal',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            cargarImagenes();
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        console.error('Error:', xhr);
                        let mensaje = 'Error al establecer como principal';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            mensaje = xhr.responseJSON.error;
                        }
                        mostrarError(mensaje);
                    }
                });
            }

            function setThumbnail(id) {
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Estableciendo imagen como thumbnail',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: urlBase + '/' + id + '/set-thumbnail',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            cargarImagenes();
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        console.error('Error:', xhr);
                        let mensaje = 'Error al establecer como thumbnail';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            mensaje = xhr.responseJSON.error;
                        }
                        mostrarError(mensaje);
                    }
                });
            }

            function setIcono(id) {
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Estableciendo imagen como icono',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: urlBase + '/' + id + '/set-icono',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            cargarImagenes();
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        console.error('Error:', xhr);
                        let mensaje = 'Error al establecer como icono';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            mensaje = xhr.responseJSON.error;
                        }
                        mostrarError(mensaje);
                    }
                });
            }

            function eliminarImagen(id) {
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: urlBase + '/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            cargarImagenes();
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        console.error('Error:', xhr);
                        let mensaje = 'Error al eliminar la imagen';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            mensaje = xhr.responseJSON.error;
                        }
                        mostrarError(mensaje);
                    }
                });
            }

            function mostrarError(mensaje) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje,
                    timer: 3000,
                    showConfirmButton: true
                });
            }
        });
    </script>
@endsection
