@extends('template.base')

@section('title', $noticia->titulo . ' · Banditore')

@section('content')
<div class="row g-5">
    {{-- Columna Principal (col-md-8) --}}
    <div class="col-md-8">
        {{-- Botón de regreso --}}
        <div class="mb-4">
            <a href="{{ route('noticia.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver a Portada
            </a>
        </div>

        {{-- Artículo de la Noticia --}}
        <article class="blog-post mb-5 pb-4 border-bottom">
            {{-- Título principal --}}
            <h1 class="blog-post-title mb-3 fw-bold text-dark">
                {{ $noticia->titulo }}
            </h1>

            {{-- Metadatos y badges de estado temporal --}}
            <div class="blog-post-meta d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                <span class="text-secondary">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $noticia->created_at->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }}
                </span>
                <span>&bull;</span>
                <span class="text-muted">
                    <i class="bi bi-clock me-1"></i> {{ $noticia->created_at->diffForHumans() }}
                </span>
                <span>&bull;</span>
                <span class="badge bg-secondary">
                    <i class="bi bi-chat-dots me-1"></i> {{ $noticia->comentarios->count() }} {{ $noticia->comentarios->count() === 1 ? 'comentario' : 'comentarios' }}
                </span>

                {{-- Badge de control de 20 min --}}
                @if($noticia->sePuedeEditar())
                    <span class="badge bg-warning text-dark" title="Dispones de 20 minutos desde su publicación para editarla">
                        <i class="bi bi-stopwatch me-1"></i> Quedan {{ $noticia->minutosRestantesEdicion() }} min para editar
                    </span>
                @else
                    <span class="badge bg-light text-muted border" title="Han transcurrido más de 20 minutos">
                        <i class="bi bi-lock me-1"></i> Edición cerrada (> 20 min)
                    </span>
                @endif

                {{-- Badge de estado de borrado --}}
                @if($noticia->sePuedeBorrar())
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                        <i class="bi bi-check-circle me-1"></i> Borrable (sin comentarios)
                    </span>
                @else
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                        <i class="bi bi-shield-lock me-1"></i> Protegida (con comentarios)
                    </span>
                @endif
            </div>

            {{-- Barra de herramientas de gestión de la noticia --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 p-2 bg-light rounded border">
                <span class="small text-muted ps-2">
                    <i class="bi bi-gear me-1"></i> Gestión de la publicación:
                </span>
                <div class="d-flex align-items-center gap-2">
                    {{-- Botón Editar --}}
                    @if($noticia->sePuedeEditar())
                        <a href="{{ route('noticia.edit', $noticia) }}" class="btn btn-sm btn-outline-warning text-dark">
                            <i class="bi bi-pencil me-1"></i> Editar Noticia
                        </a>
                    @else
                        <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="No se puede editar: han transcurrido más de 20 minutos desde su publicación">
                            <button class="btn btn-sm btn-outline-secondary disabled" type="button" disabled>
                                <i class="bi bi-pencil me-1"></i> Editar Noticia
                            </button>
                        </span>
                    @endif

                    {{-- Botón Borrar --}}
                    @if($noticia->sePuedeBorrar())
                        <form action="{{ route('noticia.destroy', $noticia) }}" method="POST" class="d-inline confirm-dialog" data-message="¿Estás completamente seguro de que deseas eliminar la noticia '{{ addslashes($noticia->titulo) }}'? Esta acción no se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash3 me-1"></i> Borrar Noticia
                            </button>
                        </form>
                    @else
                        <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="No se puede borrar porque contiene {{ $noticia->comentarios->count() }} comentario(s) asociado(s)">
                            <button class="btn btn-sm btn-outline-danger disabled" type="button" disabled>
                                <i class="bi bi-trash3 me-1"></i> Borrar Noticia
                            </button>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Contenido de la Noticia renderizado con Markdown --}}
            <div class="blog-post-content fs-5 lh-lg my-4 text-dark">
                {!! \Illuminate\Support\Str::markdown($noticia->texto ?? '') !!}
            </div>
        </article>

        {{-- Sección de Comentarios --}}
        <section class="mt-5" id="seccion-comentarios">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <h3 class="fst-italic m-0">
                    <i class="bi bi-chat-left-dots text-primary me-2"></i>
                    Comentarios ({{ $noticia->comentarios->count() }})
                </h3>
                <a href="#form-comentario" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle me-1"></i> Dejar un comentario
                </a>
            </div>

            {{-- Listado de comentarios --}}
            @forelse($noticia->comentarios->sortByDesc('created_at') as $comentario)
                <div class="card mb-3 shadow-sm border {{ $comentario->valoracion === 'pos' ? 'border-success-subtle' : 'border-danger-subtle' }}">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">
                                <i class="bi bi-person-circle text-secondary me-1"></i> Lector
                            </span>
                            <span class="text-muted small">&bull;</span>
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i> {{ $comentario->created_at->translatedFormat('d/m/Y H:i') }}
                            </span>
                            <span class="text-muted small">({{ $comentario->created_at->diffForHumans() }})</span>
                        </div>

                        {{-- Badge de valoración --}}
                        <div>
                            @if($comentario->valoracion === 'pos')
                                <span class="badge bg-success">
                                    <i class="bi bi-hand-thumbs-up-fill me-1"></i> Positiva
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-hand-thumbs-down-fill me-1"></i> Negativa
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="card-text text-dark mb-0" style="white-space: pre-line;">{{ $comentario->mensaje }}</p>
                    </div>

                    {{-- Footer del comentario con acciones y control temporal de 5 min --}}
                    <div class="card-footer bg-light py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            @if($comentario->sePuedeModificarOBorrar())
                                <span class="badge bg-warning text-dark" title="Dispones de 5 minutos desde la publicación del comentario para editarlo o borrarlo">
                                    <i class="bi bi-stopwatch me-1"></i> Quedan {{ $comentario->minutosRestantes() }} min para editar o borrar
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">
                                    <i class="bi bi-lock me-1"></i> Comentario cerrado (> 5 min)
                                </span>
                            @endif
                        </div>

                        {{-- Botones de acción del comentario --}}
                        @if($comentario->sePuedeModificarOBorrar())
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('comentario.edit', $comentario) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Editar comentario">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </a>

                                <form action="{{ route('comentario.destroy', $comentario) }}" method="POST" class="d-inline confirm-dialog" data-message="¿Estás seguro de que deseas eliminar este comentario?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Eliminar comentario">
                                        <i class="bi bi-trash3 me-1"></i> Borrar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 my-3 bg-light rounded border">
                    <div class="text-muted fs-1 mb-2"><i class="bi bi-chat-square-text"></i></div>
                    <h5 class="fw-bold">Aún no hay comentarios</h5>
                    <p class="text-muted small mb-0">Sé el primero en compartir tu opinión sobre esta noticia.</p>
                </div>
            @endforelse

            {{-- Formulario para añadir nuevo comentario --}}
            <div id="form-comentario" class="card p-4 bg-light border-0 shadow-sm mt-4">
                <h4 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Deja tu comentario
                </h4>

                <form action="{{ route('comentario.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="noticia_id" value="{{ $noticia->id }}">

                    {{-- Campo Mensaje --}}
                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-semibold">Tu opinión / comentario <span class="text-danger">*</span></label>
                        <textarea name="mensaje" id="mensaje" rows="4" class="form-control @error('mensaje') is-invalid @enderror" placeholder="Escribe aquí tu comentario con respeto..." required>{{ old('mensaje') }}</textarea>
                        @error('mensaje')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Campo Valoración --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Valoración de la noticia <span class="text-danger">*</span></label>
                        <div class="btn-group" role="group" aria-label="Valoración del comentario">
                            <input type="radio" class="btn-check" name="valoracion" id="val_pos" value="pos" {{ old('valoracion', 'pos') === 'pos' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-success" for="val_pos">
                                <i class="bi bi-hand-thumbs-up-fill me-1"></i> Positiva
                            </label>

                            <input type="radio" class="btn-check" name="valoracion" id="val_neg" value="neg" {{ old('valoracion') === 'neg' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="val_neg">
                                <i class="bi bi-hand-thumbs-down-fill me-1"></i> Negativa
                            </label>
                        </div>
                        @error('valoracion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i> Podrás editar o borrar tu comentario durante los primeros 5 minutos.
                        </small>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send-fill me-1"></i> Publicar Comentario
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    {{-- Columna Lateral (Sidebar) (col-md-4) --}}
    <div class="col-md-4">
        <div class="position-sticky" style="top: 2rem;">
            {{-- Módulo 1: Ficha de la Noticia --}}
            <div class="p-4 mb-3 bg-light rounded border">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-file-earmark-text text-primary me-1"></i> Ficha del Artículo
                </h5>
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-2">
                        <strong>Identificador:</strong> #{{ $noticia->id }}
                    </li>
                    <li class="mb-2">
                        <strong>Fecha de publicación:</strong><br>
                        {{ $noticia->created_at->translatedFormat('d/m/Y H:i:s') }}
                    </li>
                    <li class="mb-2">
                        <strong>Comentarios registrados:</strong>
                        <span class="badge bg-primary ms-1">{{ $noticia->comentarios->count() }}</span>
                    </li>
                    @php
                        $pos = $noticia->comentarios->where('valoracion', 'pos')->count();
                        $neg = $noticia->comentarios->where('valoracion', 'neg')->count();
                    @endphp
                    <li class="mb-2">
                        <strong>Balance de valoraciones:</strong><br>
                        <span class="text-success me-2"><i class="bi bi-hand-thumbs-up-fill"></i> {{ $pos }} positivas</span>
                        <span class="text-danger"><i class="bi bi-hand-thumbs-down-fill"></i> {{ $neg }} negativas</span>
                    </li>
                </ul>
            </div>

            {{-- Módulo 2: Estado de las Reglas en esta Noticia --}}
            <div class="p-4 mb-3 border rounded bg-white shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-shield-check text-warning me-1"></i> Reglas de Negocio
                </h5>
                <div class="small text-secondary mb-3">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi {{ $noticia->sePuedeEditar() ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-muted' }} me-2"></i>
                        <strong>Edición de noticia (20 min):</strong>
                    </div>
                    <p class="text-muted ms-4 mb-0">
                        @if($noticia->sePuedeEditar())
                            Activa. Quedan <strong>{{ $noticia->minutosRestantesEdicion() }} minutos</strong>.
                        @else
                            Bloqueada. Ya transcurrieron más de 20 minutos.
                        @endif
                    </p>
                </div>

                <div class="small text-secondary">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi {{ $noticia->sePuedeBorrar() ? 'bi-check-circle-fill text-success' : 'bi-shield-lock-fill text-info' }} me-2"></i>
                        <strong>Borrado de noticia:</strong>
                    </div>
                    <p class="text-muted ms-4 mb-0">
                        @if($noticia->sePuedeBorrar())
                            Permitido porque no cuenta con comentarios.
                        @else
                            Imposible de eliminar por integridad referencial (tiene comentarios).
                        @endif
                    </p>
                </div>
            </div>

            {{-- Módulo 3: Otras Noticias Recientes --}}
            @if(isset($otrasNoticias) && $otrasNoticias->isNotEmpty())
                <div class="p-4 mb-3 bg-light rounded border">
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-journal-text text-primary me-1"></i> Otras Noticias
                    </h5>
                    <ul class="list-unstyled mb-0">
                        @foreach($otrasNoticias as $otra)
                            <li class="mb-3 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <a href="{{ route('noticia.show', $otra) }}" class="text-dark fw-semibold text-decoration-none d-block text-truncate">
                                    {{ $otra->titulo }}
                                </a>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-calendar3 me-1"></i> {{ $otra->created_at->diffForHumans() }}
                                </small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Módulo 4: Acción Rápida --}}
            <div class="p-3 border rounded bg-white text-center">
                <a href="{{ route('noticia.create') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-plus-lg me-1"></i> Publicar otra noticia
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
