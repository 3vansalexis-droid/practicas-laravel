<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $permitidosPorPagina = [10, 15, 25, 50];
        $porPagina = (int) $request->get('por_pagina', 10);

        if (! in_array($porPagina, $permitidosPorPagina, true)) {
            $porPagina = 10;
        }

        $columnasPermitidas = ['nombre', 'precio', 'existencias', 'created_at'];
        $ordenarPor = $request->get('ordenar_por', 'created_at');
        $direccion = $request->get('direccion', 'desc');

        if (! in_array($ordenarPor, $columnasPermitidas, true)) {
            $ordenarPor = 'created_at';
        }

        if (! in_array($direccion, ['asc', 'desc'], true)) {
            $direccion = 'desc';
        }

        $productos = Producto::with('categoria')
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'ilike', '%' . $request->buscar . '%'))
            ->when($request->filled('categoria_id'), fn ($q) => $q->where('categoria_id', $request->categoria_id))
            ->when($request->filled('precio_min') && is_numeric($request->precio_min), fn ($q) => $q->where('precio', '>=', $request->precio_min))
            ->when($request->filled('precio_max') && is_numeric($request->precio_max), fn ($q) => $q->where('precio', '<=', $request->precio_max))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->orderBy($ordenarPor, $direccion)
            ->paginate($porPagina)
            ->withQueryString();

        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'porPagina', 'ordenarPor', 'direccion'));
    }

    public function create(): View
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto): View
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
