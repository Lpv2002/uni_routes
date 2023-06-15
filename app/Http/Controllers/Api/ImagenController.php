<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{

    public static function store(string $path, $imagen)
    {
        $ruta = $imagen->store('public/' . $path);
        return str_replace('public/' . $path . '/', '', $ruta); // Solo devuelvo el id
    }

    // path ruta del directorio  brevet/
    public static function show(string $path, string $file_name)
    {
        return Storage::url($path . $file_name);
    }

    public static function destroy(String $path)
    {

        Storage::delete('public/' . $path);
    }
}
