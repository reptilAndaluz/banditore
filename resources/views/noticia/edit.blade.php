@extends('template.base')

@section('title', 'Editar Noticia: ' . $noticia->titulo . ' · Banditore')

@section('content')
<div class="row g-5">
    {{-- Columna del Formulario (col-md-8) --}}
    <div class="col-md-8">
        {{-- Botón de regreso --}}
        <div class="mb-4">
            <a href="{{ route('noticia.show', $noticia) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver a la Noticia
            </a>
        </div>

        <div class="card shadow-sm border-0 bg-white p-4 p-md-5">
            {{-- Cabecera --}}
            <div class="border-bottom pb-3 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h1 class="h2 fw-bold text-dark mb-1">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Editar Noticia
                    </h1>
                    <span class="badge bg-secondary">
                        ID: #{{ $noticia->id }}
                    </span>
                </div>
                <p class="text-muted small mb-0">
                    Modifica el título o el contenido de la publicación antes de que expire la ventana de tiempo.
                </p>
            </div>

            {{-- Alerta informativa con contador de minutos restantes --}}
            <div class="alert alert-warning border-warning-subtle d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-stopwatch-fill fs-3 text-warning me-3 flex-shrink-0"></i>
                <div>
                    <strong>Ventana de edición activa:</strong>
                    <div class="small">
                        Dispones de <strong>{{ $noticia->minutosRestantesEdicion() }} minutos</strong> restantes para guardar cambios en esta noticia. Transcurridos los 20 minutos desde su publicación, la edición quedará permanentemente bloqueada.
                    </div>
                </div>
            </div>

            <form action="{{ route('noticia.update', $noticia) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Campo Título --}}
                <div class="mb-4">
                    <label for="titulo" class="form-label fw-bold">
                        Título de la noticia <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="titulo"
                           id="titulo"
                           class="form-control form-control-lg @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo', $noticia->titulo) }}"
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
                              rows="12"
                              class="form-control font-monospace @error('texto') is-invalid @enderror"
                              placeholder="Escribe aquí el contenido en formato Markdown...">{{ old('texto', $noticia->texto) }}</textarea>
                    @error('texto')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">
                        Puedes editar el texto directamente o reemplazarlo adjuntando un nuevo archivo <code>.md</code> o <code>.txt</code>.
                    </div>
                </div>

                {{-- Campo Reemplazar con Archivo .md o .txt --}}
                <div class="mb-4 p-3 bg-light rounded border">
                    <label for="archivo_md" class="form-label fw-bold mb-1">
                        <i class="bi bi-filetype-md text-primary me-1"></i> Opcional: Reemplazar contenido subiendo un archivo <code>.md</code> o <code>.txt</code>
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
                        Si seleccionas un archivo, su texto sustituirá el contenido actual al guardar. (Máx. 2MB).
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('noticia.show', $noticia) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Columna Lateral (Sidebar de Información) (col-md-4) --}}
    <div class="col-md-4">
        <div class="position-sticky" style="top: 2rem;">
            {{-- Módulo 1: Datos de la Noticia --}}
            <div class="p-4 mb-3 bg-light rounded border">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-info-circle text-primary me-1"></i> Datos del Artículo
                </h5>
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-2">
                        <strong>Publicada:</strong><br>
                        {{ $noticia->created_at->translatedFormat('d/m/Y H:i:s') }}
                    </li>
                    <li class="mb-2">
                        <strong>Última actualización:</strong><br>
                        {{ $noticia->updated_at->translatedFormat('d/m/Y H:i:s') }}
                    </li>
                    <li class="mb-2">
                        <strong>Comentarios asociados:</strong>
                        <span class="badge bg-secondary ms-1">{{ $noticia->comentarios->count() }}</span>
                    </li>
                </ul>
            </div>

            {{-- Módulo 2: Recordatorio de Reglas --}}
            <div class="p-4 mb-3 border rounded bg-white shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-shield-check text-warning me-1"></i> Reglas de Modificación
                </h5>
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-clock-history text-warning fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Límite estricto de 20 minutos:</strong>
                            <p class="mb-0 text-muted">Asegúrate de enviar el formulario antes de que transcurran los 20 minutos, de lo contrario la solicitud será rechazada.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-chat-square-dots text-info fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Comentarios existentes:</strong>
                            <p class="mb-0 text-muted">La edición es posible independientemente de que existan comentarios, siempre que esté dentro del plazo.</p>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Módulo 3: Acceso a la Noticia --}}
            <div class="p-3 border rounded bg-white text-center">
                <a href="{{ route('noticia.show', $noticia) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-eye me-1"></i> Ver Noticia en el Blog
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
