@extends('template.base')

@section('title', 'Publicar Nueva Noticia · Banditore')

@section('content')
<div class="row g-5">
    {{-- Columna del Formulario (col-md-8) --}}
    <div class="col-md-8">
        {{-- Botón de regreso --}}
        <div class="mb-4">
            <a href="{{ route('noticia.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver a Portada
            </a>
        </div>

        <div class="card shadow-sm border-0 bg-white p-4 p-md-5">
            <div class="border-bottom pb-3 mb-4">
                <h1 class="h2 fw-bold text-dark mb-1">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Publicar Nueva Noticia
                </h1>
                <p class="text-muted small mb-0">
                    Comparte un artículo, noticia de actualidad o análisis con la comunidad.
                </p>
            </div>

            <form action="{{ route('noticia.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Campo Título --}}
                <div class="mb-4">
                    <label for="titulo" class="form-label fw-bold">
                        Título de la noticia <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="titulo"
                           id="titulo"
                           class="form-control form-control-lg @error('titulo') is-invalid @enderror"
                           placeholder="Ejemplo: Gran avance en las tecnologías de código abierto"
                           value="{{ old('titulo') }}"
                           maxlength="200"
                           required>
                    @error('titulo')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">
                        Debe tener entre 3 y 200 caracteres.
                    </div>
                </div>

                {{-- Campo Texto (Markdown) --}}
                <div class="mb-4">
                    <label for="texto" class="form-label fw-bold">
                        Contenido del artículo (Soporta Markdown)
                    </label>
                    <textarea name="texto"
                              id="texto"
                              rows="10"
                              class="form-control font-monospace @error('texto') is-invalid @enderror"
                              placeholder="# Escribe aquí el contenido de tu noticia...&#10;&#10;Puedes usar negritas con **texto**, cursivas con *texto*, listas o citas en bloque.">{{ old('texto') }}</textarea>
                    @error('texto')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">
                        Puedes escribir el texto directamente aquí o adjuntar un archivo <code>.md</code> abajo.
                    </div>
                </div>

                {{-- Campo Subir Archivo .md o .txt --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label for="archivo_md" class="form-label fw-bold mb-1">
                        <i class="bi bi-filetype-md text-primary me-1"></i> Opcional: Subir nota en formato <code>.md</code> o <code>.txt</code>
                    </label>
                    <input type="file"
                           name="archivo_md"
                           id="archivo_md"
                           class="form-control @error('archivo_md') is-invalid @enderror"
                           accept=".md,.txt,text/plain,text/markdown">
                    @error('archivo_md')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">
                        Si seleccionas un archivo, su texto se utilizará como contenido de la publicación. (Máx. 2MB).
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('noticia.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="bi bi-send-fill me-1"></i> Publicar Noticia
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Columna Lateral (Sidebar de Ayuda) (col-md-4) --}}
    <div class="col-md-4">
        <div class="position-sticky" style="top: 2rem;">
            {{-- Módulo 1: Guía de Formato Markdown --}}
            <div class="p-4 mb-3 bg-light rounded border">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-markdown-fill text-primary me-1"></i> Guía Rápida de Markdown
                </h5>
                <p class="small text-muted mb-3">Puedes enriquecer tu texto utilizando la sintaxis estándar de Markdown:</p>
                <div class="small">
                    <div class="mb-2">
                        <code># Encabezado 1</code> &rarr; Título grande
                    </div>
                    <div class="mb-2">
                        <code>## Encabezado 2</code> &rarr; Subtítulo
                    </div>
                    <div class="mb-2">
                        <code>**negrita**</code> &rarr; <strong>negrita</strong>
                    </div>
                    <div class="mb-2">
                        <code>*cursiva*</code> &rarr; <em>cursiva</em>
                    </div>
                    <div class="mb-2">
                        <code>> Cita</code> &rarr; Cita en bloque
                    </div>
                    <div class="mb-2">
                        <code>- Elemento</code> &rarr; Lista desordenada
                    </div>
                    <div class="mb-2">
                        <code>[Enlace](https://...)</code> &rarr; Enlace web
                    </div>
                </div>
            </div>

            {{-- Módulo 2: Recordatorio de Reglas --}}
            <div class="p-4 mb-3 border rounded bg-white shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-shield-check text-warning me-1"></i> Reglas de Publicación
                </h5>
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-stopwatch text-warning fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Margen de 20 minutos:</strong>
                            <p class="mb-0 text-muted">Una vez publicada, tendrás exactamente 20 minutos para realizar cualquier edición.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-slash-circle text-danger fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Protección por comentarios:</strong>
                            <p class="mb-0 text-muted">No podrás eliminar la noticia una vez que reciba su primer comentario.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
