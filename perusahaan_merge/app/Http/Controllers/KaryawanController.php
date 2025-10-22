<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    public function index(): View
    {
        $rows = Karyawan::with(['jabatan', 'rating'])->orderByDesc('created_at')->get();
        return view('kantor.karyawan.index', compact('rows'));
    }

    public function create(): View
    {
        $jabatanList = Jabatan::orderBy('jabatan')->get();
        $ratingList = Rating::orderBy('rating')->get();
        return view('kantor.karyawan.form', [
            'editing' => null,
            'jabatanList' => $jabatanList,
            'ratingList' => $ratingList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'umur' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string|max:15',
            'status' => 'nullable|string|max:50',
            'id_jabatan' => 'nullable|integer|exists:jabatan,id_jabatan',
            'id_rating' => 'nullable|integer|exists:rating,id_rating',
        ]);

        $data['created_at'] = now();
        Karyawan::create($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan disimpan');
    }

    public function edit(int $id): View
    {
        $editing = Karyawan::findOrFail($id);
        $jabatanList = Jabatan::orderBy('jabatan')->get();
        $ratingList = Rating::orderBy('rating')->get();
        return view('kantor.karyawan.form', compact('editing', 'jabatanList', 'ratingList'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'umur' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string|max:15',
            'status' => 'nullable|string|max:50',
            'id_jabatan' => 'nullable|integer|exists:jabatan,id_jabatan',
            'id_rating' => 'nullable|integer|exists:rating,id_rating',
        ]);

        Karyawan::where('id_karyawan', $id)->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        Karyawan::where('id_karyawan', $id)->delete();
        return redirect()->route('karyawan.index')->with('success', 'Karyawan dihapus');
    }

    public function show(int $id): View
    {
        $detailKaryawan = Karyawan::with(['jabatan', 'rating'])->findOrFail($id);
        return view('kantor.karyawan.show', compact('detailKaryawan'));
    }
}
