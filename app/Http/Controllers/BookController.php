<?php

namespace App\Http\Controllers;

use App\Services\GoogleBooks;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Inyectamos el servicio (Laravel lo resuelve solo)
    public function index(Request $request, GoogleBooks $books)
    {
        // Validación básica del query y de la página
        $data = $request->validate([
            'q'    => ['nullable', 'string', 'min:2'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $q    = trim($data['q'] ?? '');
        $page = (int)($data['page'] ?? 1);
        $perPage = 12; // si hace falta, se mueve a config

        $results = null;
        if ($q !== '') {
            // Llamada al servicio; devuelve items, totalItems, page, perPage, error
            $results = $books->search($q, $page, $perPage);
        }

        return view('books.index', [
            'q'       => $q,
            'results' => $results,
        ]);
    }

    public function show(string $id, GoogleBooks $books)
    {
        // Detalle por ID
        $res = $books->getVolume($id);
        abort_if($res['error'] && !$res['item'], 404);

        return view('books.show', [
            'book'  => $res['item'],
            'error' => $res['error'],
        ]);
    }
}
