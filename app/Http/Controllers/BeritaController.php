<?php

namespace App\Http\Controllers;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $beritas = Berita::query();

            if ($request->search) {
                $beritas->where(function ($query) use ($request) {
                    $query->where('judul_berita', 'like', '%' . $request->search . '%');
                });
            }

            $data = $beritas->orderBy('id_berita', 'desc')->get();

            if ($data->isEmpty()) throw new \Exception('Berita Tidak Ditemukan');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menampilkan berita',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    //Store
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul_berita' => 'required|string',
                'deskripsi' => 'required|string',
                'tanggal' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $beritas = Berita::create([
                'judul_berita' => $request->judul_berita,
                'deskripsi' => $request->deskripsi,
                'tanggal' => $request->tanggal
            ]);

            return response()->json([
                "status" => true,
                "message" => 'Insert Berita Success',
                "data" => $beritas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    //Show 1
    public function show($id)
    {
        try {
            $beritas = Berita::find($id);

            if (!$beritas) throw new \Exception("Berita Not Found");

            return response()->json([
                "status" => true,
                "message" => 'Berita Found',
                "data" => $beritas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    //Update
    public function update(Request $request, String $id)
    {
        try {
            $beritas = Berita::find($id);

            if (!$beritas) throw new \Exception("Berita Not Found");

            $validator = Validator::make($request->all(), [
                'judul_berita' => 'string',
                'deskripsi' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $beritas->update($request->all());

            return response()->json([
                "status" => true,
                "message" => 'Update Berita Success',
                "data" => $beritas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    //Delete
    public function destroy($id)
    {
        try {
            $beritas = Berita::find($id);

            if (!$beritas) throw new \Exception("Berita Not Found");

            $beritas->delete();

            return response()->json([
                "status" => true,
                "message" => 'Delete Berita Success',
                "data" => $beritas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }
}
