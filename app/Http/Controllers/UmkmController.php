<?php

namespace App\Http\Controllers;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        try {
            $umkms = Umkm::query();

            if ($request->search) {
                $umkms->where(function ($query) use ($request) {
                    $query->where('nama_umkm', 'like', '%' . $request->search . '%');
                });
            }

            $data = $umkms->orderBy('id_umkm', 'desc')->get();

            if ($data->isEmpty()) throw new \Exception('UMKM Tidak Ditemukan');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menampilkan UMKM',
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_umkm' => 'required|string',
                'deskripsi_umkm' => 'required|string',
                'pemilik' => 'required|string',
                'foto_umkm' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $filename = null;
            if ($request->has('foto_umkm')) {
                $base64Image = $request->foto_umkm;
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $imageName = 'foto_umkm_' . time() . '.png';
                $filePath = public_path('storage/' . $imageName);
                file_put_contents($filePath, $image);

                $filename = $imageName;
            }

            $umkms = Umkm::create([
                'nama_umkm' => $request->nama_umkm,
                'deskripsi_umkm' => $request->deskripsi_umkm,
                'pemilik' => $request->pemilik,
                'foto_umkm' => $filename,
                'status_umkm' => 1,
            ]);

            return response()->json([
                "status" => true,
                "message" => 'Insert UMKM Success',
                "data" => $umkms
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
            $umkms = Umkm::find($id);

            if (!$umkms) throw new \Exception("UMKM Not Found");

            return response()->json([
                "status" => true,
                "message" => 'UMKM Found',
                "data" => $umkms
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function update(Request $request, String $id)
    {
        try {
            $umkms = Umkm::find($id);

            if (!$umkms) throw new \Exception("UMKM Not Found");

            $validator = Validator::make($request->all(), [
                'nama_umkm' => 'string',
                'deskripsi_umkm' => 'string',
                'pemilik' => 'string',
                'foto_umkm' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            if ($request->has('foto_umkm')) {
                $base64Image = $request->foto_umkm;
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $imageName = 'foto_umkm_' . time() . '.png';
                $filePath = public_path('storage/' . $imageName);
                file_put_contents($filePath, $image);

                if ($umkms->foto_umkm) {
                    $previousImagePath = public_path('storage/' . $umkms->foto_umkm);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }

                $umkms->foto_umkm = $imageName;
            }

            $umkms->nama_umkm = $request->nama_umkm;
            $umkms->deskripsi_umkm = $request->deskripsi_umkm;
            $umkms->pemilik = $request->pemilik;

            $umkms->save();

            return response()->json([
                "status" => true,
                "message" => 'Update UMKM Success',
                "data" => $umkms
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    //Change Status Aktif
    public function updateStatus(Request $request, String $id)
    {
        try {
            $umkms = Umkm::find($id);

            if (!$umkms) throw new \Exception("UMKM Not Found");

            $umkms->status_umkm = !$umkms->status_umkm;
            $umkms->save();

            return response()->json([
                "status" => true,
                "message" => 'Update UMKM Success',
                "data" => $umkms
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
            $umkms = Umkm::find($id);

            if (!$umkms) throw new \Exception("UMKM Not Found");

            if ($umkms->foto_umkm) {
                $imagePath = public_path('storage/' . $umkms->foto_umkm);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $umkms->delete();

            return response()->json([
                "status" => true,
                "message" => 'Delete UMKM Success',
                "data" => $umkms
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
