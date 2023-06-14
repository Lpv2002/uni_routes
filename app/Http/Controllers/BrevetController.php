<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrevetResource;
use App\Models\Brevet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrevetController extends Controller
{
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
            'expiration_date' => ['required', 'date', 'date_format:d/m/Y'],
            'broadcast_date' => ['required', 'date', 'date_format:d/m/Y'],
            'category' => ['required', 'max:1'],
            'photo' => ['image'],
        ]);

        // Guardar solo el ID de la foto en la base de datos
        $validated_data['photo'] = $request->photo->store('photos');

        // Convertir las fechas al formato correcto
        $validated_data['expiration_date'] = Carbon::createFromFormat('d/m/Y', $validated_data['expiration_date'])->format('Y-m-d');
        $validated_data['broadcast_date'] = Carbon::createFromFormat('d/m/Y', $validated_data['broadcast_date'])->format('Y-m-d');

        $brevet = Brevet::create($validated_data);

        return response([
            'brevet' => $brevet,
            'message' => 'Datos corecto'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brevet = Brevet::findOrFail($id);

        return response(['brevet' => $brevet, 'message' => 'Solicitud exitosa']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $brevet = Brevet::findOrFail($id);

        $validated_data = $request->validate([
            'nro' => ['numeric'],
            'expiration_date' => ['date', 'date_format:Y/m/d'],
            'broadcast_date' => ['date', 'date_format:Y/m/d'],
            'category' => ['max:1'],
            'photo' => ['image'],
        ]);

        $brevet->fill($validated_data);

        // Si se proporciona una nueva foto, actualizar el campo "photo"
        if ($request->hasFile('photo')) {
            // Eliminar la foto anterior
            Storage::delete($brevet->photo);

            // Guardar solo el ID de la nueva foto en la base de datos
            $brevet->photo = $request->photo->store('photos');
        }

        $brevet->save();

        return response([
            'brevet' => $brevet,
            'message' => 'Datos autualizados'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brevet = Brevet::findOrFail($id);

        // Eliminar la foto asociada 
        Storage::delete($brevet->photo);

        $brevet->delete();

        return response()->json(null, 204);
    }
}
