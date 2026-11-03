@extends('template.base')

@section('title', 'Banditore · Portada y Noticias')

{{-- 1. Noticia Destacada Principal (Hero Banner estilo Jumbotron) --}}
@section('hero')
    @if(isset($noticias) && $noticias->isNotEmpty())
        @php
            $destacada = $noticias->first();
        @endphp
        <div class="p-4 p-md-5 mb-4 rounded text-bg-dark position-relative overflow-hidden shadow-sm">
            <div class="col-md-9 px-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-uppercase px-2 py-1">
                        <i class="bi bi-star-fill me-1"></i> Última Noticia
                    </span>
                    <span class="text-white-50 small">
                        <i class="bi bi-clock me-1"></i> {{ $destacada->created_at->diffForHumans() }}
                    </span>
                    <span class="badge bg-secondary">
                        <i class="bi bi-chat-dots me-1"></i> {{ $destacada->comentarios_count }} {{ $destacada->comentarios_count === 1 ? 'comentario' : 'comentarios' }}
                    </span>
                </div>
                <h1 class="display-5 fst-italic fw-bold text-white mb-3">
                    <a href="{{ route('noticia.show', $destacada) }}" class="text-white text-decoration-none">
                        {{ $destacada->titulo }}
                    </a>
                </h1>
                <p class="lead my-3 text-light text-opacity-75">
                    {{ \Illuminate\Support\Str::limit(strip_tags(\Illuminate\Support\Str::markdown($destacada->texto ?? '')), 220) }}
                </p>
                <p class="lead mb-0">
                    <a href="{{ route('noticia.show', $destacada) }}" class="text-white fw-bold text-decoration-underline">
                        Continuar leyendo... <i class="bi bi-arrow-right"></i>
                    </a>
                </p>
            </div>
        </div>
    @endif
@endsection

{{-- 2. Tarjetas Secundarias Destacadas (2 Columnas) --}}
@section('featured')
    @if(isset($noticias) && $noticias->count() > 1)
        <div class="row mb-2">
            @foreach($noticias->skip(1)->take(2) as $secundaria)
                <div class="col-md-6">
                    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-post bg-white">
                        <div class="col p-4 d-flex flex-column position-static">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-chat-left-text text-primary me-1"></i> {{ $secundaria->comentarios_count }} comentarios
                                </span>
                                <small class="text-muted">{{ $secundaria->created_at->translatedFormat('d M, Y') }}</small>
                            </div>
                            <h4 class="mb-2 fw-bold text-truncate" title="{{ $secundaria->titulo }}">
                                {{ $secundaria->titulo }}
                            </h4>
                            <p class="card-text mb-auto text-secondary small">
                                {{ \Illuminate\Support\Str::limit(strip_tags(\Illuminate\Support\Str::markdown($secundaria->texto ?? '')), 120) }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('noticia.show', $secundaria) }}" class="stretched-link text-primary fw-semibold text-decoration-none">
                                    Leer noticia completa <i class="bi bi-chevron-right small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

{{-- 3. Contenido Principal: Listado en dos columnas (col-md-8 y col-md-4) --}}
@section('content')
<div class="row g-5">
    {{-- Columna Principal de Artículos --}}
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <h3 class="fst-italic m-0">
                <i class="bi bi-newspaper me-2 text-primary"></i> Publicaciones Recientes
            </h3>
            <a href="{{ route('noticia.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nueva Noticia
            </a>
        </div>

        @if(!isset($noticias) || $noticias->isEmpty())
            <div class="text-center py-5 my-4 bg-light rounded border">
                <div class="display-3 text-muted mb-3"><i class="bi bi-journal-x"></i></div>
                <h4 class="fw-bold">No hay noticias publicadas todavía</h4>
                <p class="text-muted">Sé el primero en compartir una noticia o debate con la comunidad.</p>
                <a href="{{ route('noticia.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-pencil-square me-1"></i> Publicar primera noticia
                </a>
            </div>
        @else
            @foreach($noticias as $noticia)
                <article class="blog-post mb-5 pb-4 border-bottom">
                    {{-- Título --}}
                    <h2 class="blog-post-title mb-2">
                        <a href="{{ route('noticia.show', $noticia) }}">
                            {{ $noticia->titulo }}
                        </a>
                    </h2>

                    {{-- Metadatos y badges temporales --}}
                    <p class="blog-post-meta d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $noticia->created_at->translatedFormat('d/m/Y H:i') }}
                        </span>
                        <span>&bull;</span>
                        <span class="badge bg-secondary">
                            <i class="bi bi-chat-dots me-1"></i> {{ $noticia->comentarios_count }} {{ $noticia->comentarios_count === 1 ? 'comentario' : 'comentarios' }}
                        </span>

                        {{-- Badge de control de 20 min --}}
                        @if($noticia->sePuedeEditar())
                            <span class="badge bg-warning text-dark" title="Dispones de 20 minutos desde su publicación para editarla">
                                <i class="bi bi-stopwatch me-1"></i> {{ $noticia->minutosRestantesEdicion() }} min para editar
                            </span>
                        @else
                            <span class="badge bg-light text-muted border" title="Han pasado más de 20 minutos">
                                <i class="bi bi-lock me-1"></i> Edición cerrada
                            </span>
                        @endif

                        {{-- Badge de estado de borrado --}}
                        @if($noticia->sePuedeBorrar())
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle me-1"></i> Sin comentarios (borrable)
                            </span>
                        @else
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                <i class="bi bi-shield-lock me-1"></i> Con comentarios
                            </span>
                        @endif
                    </p>

                    {{-- Extracto del contenido --}}
                    <div class="blog-post-content mb-3 text-secondary">
                        {{ \Illuminate\Support\Str::limit(strip_tags(\Illuminate\Support\Str::markdown($noticia->texto ?? '')), 280) }}
                    </div>

                    {{-- Barra de botones de acción --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
                        <a href="{{ route('noticia.show', $noticia) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-book-half me-1"></i> Leer completa
                        </a>

                        <div class="d-flex align-items-center gap-2">
                            {{-- Botón Editar: activo si < 20 min --}}
                            @if($noticia->sePuedeEditar())
                                <a href="{{ route('noticia.edit', $noticia) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </a>
                            @else
                                <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="No se puede editar: han transcurrido más de 20 minutos desde su publicación">
                                    <button class="btn btn-sm btn-outline-secondary disabled" type="button" disabled>
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                </span>
                            @endif

                            {{-- Botón Borrar: activo solo si no tiene comentarios --}}
                            @if($noticia->sePuedeBorrar())
                                <form action="{{ route('noticia.destroy', $noticia) }}" method="POST" class="d-inline confirm-dialog" data-message="¿Estás seguro de que deseas eliminar la noticia '{{ addslashes($noticia->titulo) }}'? Esta acción no se puede deshacer.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash3 me-1"></i> Borrar
                                    </button>
                                </form>
                            @else
                                <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="No se puede borrar porque tiene {{ $noticia->comentarios_count }} comentario(s) asociado(s)">
                                    <button class="btn btn-sm btn-outline-danger disabled" type="button" disabled>
                                        <i class="bi bi-trash3 me-1"></i> Borrar
                                    </button>
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach

            {{-- Paginación Bootstrap 5 --}}
            @if(method_exists($noticias, 'links'))
                <div class="d-flex justify-content-center my-4">
                    {{ $noticias->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>

    {{-- Columna Lateral (Sidebar) --}}
    <div class="col-md-4">
        <div class="position-sticky" style="top: 2rem;">
            {{-- Módulo 1: Acerca del Blog --}}
            <div class="p-4 mb-3 bg-light rounded border">
                <h5 class="fst-italic fw-bold text-dark mb-2">
                    <i class="bi bi-info-circle text-primary me-1"></i> Sobre Banditore
                </h5>
                <p class="mb-0 small text-secondary">
                    Portal de noticias y debate participativo. Publica tus artículos en texto plano o formato Markdown, y participa valorando y comentando las noticias de la comunidad.
                </p>
            </div>

            {{-- Módulo 2: Reglas del Sistema --}}
            <div class="p-4 mb-3 border rounded bg-white shadow-sm">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-shield-check text-warning me-1"></i> Reglas del Sistema
                </h5>
                <ul class="list-unstyled mb-0 small text-secondary">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-clock-history text-warning fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Edición de noticias:</strong>
                            <p class="mb-0 text-muted">Dispones de <strong>20 minutos</strong> tras la publicación para modificarla.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-slash-circle text-danger fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Borrado de noticias:</strong>
                            <p class="mb-0 text-muted">Solo se permite eliminar noticias si <strong>no tienen comentarios</strong>.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-chat-square-dots text-info fs-5 me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Moderar comentarios:</strong>
                            <p class="mb-0 text-muted">Los comentarios solo se pueden editar o borrar durante los primeros <strong>5 minutos</strong>.</p>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Módulo 3: Estadísticas Rápidas --}}
            <div class="p-4 mb-3 bg-light rounded border">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-bar-chart-line text-success me-1"></i> Estadísticas
                </h5>
                <div class="row text-center g-2">
                    <div class="col-6">
                        <div class="p-2 bg-white rounded border">
                            <div class="fs-4 fw-bold text-primary">
                                {{ method_exists($noticias, 'total') ? $noticias->total() : $noticias->count() }}
                            </div>
                            <small class="text-muted">Noticias</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-white rounded border">
                            <div class="fs-4 fw-bold text-success">
                                {{ $totalComentarios ?? \App\Models\Comentario::count() }}
                            </div>
                            <small class="text-muted">Comentarios</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Módulo 4: Enlaces Rápidos --}}
            <div class="p-3 border rounded bg-white text-center">
                <a href="{{ route('noticia.create') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-pencil-square me-1"></i> Publicar nueva noticia
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
