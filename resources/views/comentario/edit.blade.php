@extends('template.base')

@section('title', 'Editar Comentario · Banditore')

@section('content')
<div class="row g-5">
    {{-- Columna del Formulario (col-md-8) --}}
    <div class="col-md-8">
        {{-- Botón de regreso a la noticia --}}
        <div class="mb-4">
            <a href="{{ route('noticia.show', $comentario->noticia_id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver a la Noticia
            </a>
        </div>

        <div class="card shadow-sm border-0 bg-white p-4 p-md-5">
            {{-- Cabecera --}}
            <div class="border-bottom pb-3 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h1 class="h2 fw-bold text-dark mb-1">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Editar Comentario
                    </h1>
                    <span class="badge bg-secondary">
                        ID: #{{ $comentario->id }}
                    </span>
                </div>
                <p class="text-muted small mb-0">
                    Modifica tu comentario o valoración antes de que finalice la ventana de 5 minutos.
                </p>
            </div>

            {{-- Alerta informativa de tiempo restante (5 minutos) --}}
            <div class="alert alert-warning border-warning-subtle d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-stopwatch-fill fs-3 text-warning me-3 flex-shrink-0"></i>
                <div>
                    <strong>Ventana de edición activa (5 minutos):</strong>
                    <div class="small">
                        Dispones de <strong>{{ $comentario->minutosRestantes() }} minutos</strong> restantes para modificar tu comentario. Una vez transcurridos los 5 minutos desde su publicación, quedará permanentemente fijado.
                    </div>
                </div>
            </div>

            <form action="{{ route('comentario.update', $comentario) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Campo Mensaje --}}
                <div class="mb-4">
                    <label for="mensaje" class="form-label fw-bold">
                        Tu opinión / comentario <span class="text-danger">*</span>
                    </label>
                    <textarea name="mensaje"
                              id="mensaje"
                              rows="5"
                              class="form-control @error('mensaje') is-invalid @enderror"
                              placeholder="Escribe aquí tu comentario..."
                              required>{{ old('mensaje', $comentario->mensaje) }}</textarea>
                    @error('mensaje')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo Valoración --}}
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">
                        Valoración de la noticia <span class="text-danger">*</span>
                    </label>
                    <div class="btn-group" role="group" aria-label="Valoración del comentario">
                        <input type="radio"
                               class="btn-check"
                               name="valoracion"
                               id="val_pos"
                               value="pos"
                               {{ old('valoracion', $comentario->valoracion) === 'pos' ? 'checked' : '' }}
                               required>
                        <label class="btn btn-outline-success" for="val_pos">
                            <i class="bi bi-hand-thumbs-up-fill me-1"></i> Positiva
                        </label>

                        <input type="radio"
                               class="btn-check"
                               name="valoracion"
                               id="val_neg"
                               value="neg"
                               {{ old('valoracion', $comentario->valoracion) === 'neg' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger" for="val_neg">
                            <i class="bi bi-hand-thumbs-down-fill me-1"></i> Negativa
                        </label>
                    </div>
                    @error('valoracion')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('noticia.show', $comentario->noticia_id) }}" class="btn btn-outline-secondary">
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
            {{-- Módulo 1: Noticia a la que pertenece --}}
            @if($comentario->noticia)
                <div class="p-4 mb-3 bg-light rounded border">
                    <h5 class="fw-bold mb-2 text-dark">
                        <i class="bi bi-file-earmark-text text-primary me-1"></i> Noticia Comentada
                    </h5>
                    <h6 class="fw-bold text-dark mb-2">
                        {{ $comentario->noticia->titulo }}
                    </h6>
                    <p class="text-muted small mb-3">
                        Publicada el {{ $comentario->noticia->created_at->translatedFormat('d/m/Y H:i') }}
                    </p>
                    <a href="{{ route('noticia.show', $comentario->noticia_id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-eye me-1"></i> Ver Noticia Completa
                    </a>
                </div>
            @endif

            {{-- Módulo 2: Datos del Comentario --}}
            <div class="p-4 mb-3 bg-white border rounded shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-info-circle text-primary me-1"></i> Datos del Comentario
                </h5>
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-2">
                        <strong>Publicado:</strong><br>
                        {{ $comentario->created_at->translatedFormat('d/m/Y H:i:s') }}
                    </li>
                    <li class="mb-2">
                        <strong>Última modificación:</strong><br>
                        {{ $comentario->updated_at->translatedFormat('d/m/Y H:i:s') }}
                    </li>
                    <li class="mb-2">
                        <strong>Tiempo transcurrido:</strong><br>
                        {{ $comentario->created_at->diffForHumans() }}
                    </li>
                </ul>
            </div>

            {{-- Módulo 3: Recordatorio de Regla de 5 Minutos --}}
            <div class="p-4 mb-3 border rounded bg-light">
                <h5 class="fw-bold mb-2 text-dark">
                    <i class="bi bi-clock-history text-warning me-1"></i> Regla de los 5 Minutos
                </h5>
                <p class="small text-secondary mb-0">
                    Para asegurar la coherencia del hilo de debate, los comentarios solo pueden modificarse o eliminarse durante los primeros <strong>5 minutos</strong> tras ser publicados.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
