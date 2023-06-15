<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrevetResource;
use App\Models\Brevet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrevetController extends Controller
{
    const root_path = 'public/';
    const path_imagen = 'brevet';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brevets = Brevet::all();

        return new BrevetResource($brevets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'nro' => ['required', 'numeric'],
            'expiration_date' => ['date', 'date_format:Y/m/d'],
            'broadcast_date' => ['date', 'date_format:Y/m/d'],
            'category' => ['required', 'max:1'],
            'photo' => ['image'],
        ]);

        // Guardar solo el ID de la foto en la base de datos
        // $imagen_controller = new ImagenController();
        $path = ImagenController::store('brevet', $validated_data['photo']);

        $validated_data['photo'] = $path;

        $brevet = Brevet::create($validated_data);

        return response([
            'brevet' => new BrevetResource($brevet),
            'message' => 'Datos corectos'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brevet = Brevet::findOrFail($id);

        return response([
            'brevet' =>  new BrevetResource($brevet),
            'message' => 'Solicitud exitosa'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated_data = $request->validate([
            'nro' => ['numeric'],
            'expiration_date' => ['date', 'date_format:Y/m/d'],
            'broadcast_date' => ['date', 'date_format:Y/m/d'],
            'category' => ['max:1'],
            'photo' => ['image'],
        ]);

        $brevet = Brevet::findOrFail($id);

        // $brevet->fill($validated_data);

        if ($request->hasFile('photo')) {
            if ($brevet->photo) {
                ImagenController::destroy('brevet/' . $brevet->photo);
            }

            $brevet->photo = ImagenController::store('brevet', $request->photo);
        }

        $brevet->save();

        return response([
            'brevet' => new BrevetResource($brevet),
            'message' => 'Datos actualizados'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brevet = Brevet::findOrFail($id);

        if ($brevet->photo) {
            ImagenController::destroy('brevet/' . $brevet->photo);
        }

        $brevet->delete();

        return response([
            'message' => 'Brevet eliminado corectamente',
        ], 204);
    }
}
