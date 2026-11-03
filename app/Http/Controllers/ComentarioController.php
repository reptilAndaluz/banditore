<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    private const RULES = [
        'noticia_id' => 'required|exists:noticia,id',
        'mensaje' => 'required|string|min:2|max:1000',
        'valoracion' => 'required|in:pos,neg',
    ];

    private const MESSAGES = [
        'mensaje.required' => 'El comentario no puede estar vacío.',
        'valoracion.in' => 'La valoración debe ser positiva (pos) o negativa (neg).',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
