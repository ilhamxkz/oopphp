<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GajiController extends Controller
{
    public function index(): View
    {
        $rows = Gaji::with(['karyawan.jabatan'])
            ->orderByDesc('id_gaji')
            ->get();
        return view('kantor.gaji.index', compact('rows'));
    }

    public function create(): View
    {
        $karyawans = Karyawan::with('jabatan')->orderBy('nama')->get();
        $lemburs = Lembur::orderByDesc('id_lembur')->get();
        return view('kantor.gaji.form', [
            'karyawans' => $karyawans,
            'lemburs' => $lemburs,
            'editing' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|integer|exists:karyawan,id_karyawan',
            'id_lembur' => 'nullable|integer|exists:lembur,id_lembur',
            'periode' => 'required|string|max:7',
            'lama_lembur' => 'nullable|numeric',
        ]);

        $karyawan = Karyawan::with(['jabatan', 'rating'])->findOrFail($validated['id_karyawan']);
        $lembur = null;
        if (!empty($validated['id_lembur'])) {
            $lembur = Lembur::find($validated['id_lembur']);
        }

        $gajiPokok = (float) optional($karyawan->jabatan)->gaji_pokok;
        $tunjangan = (float) optional($karyawan->jabatan)->tunjangan;
        $persentaseBonus = (float) optional($karyawan->rating)->persentase_bonus;
        $tarifLembur = (float) optional($lembur)->tarif;
        $lamaLembur = (float) ($validated['lama_lembur'] ?? 0);

        $totalLembur = $lamaLembur * $tarifLembur;
        $totalBonus = ($persentaseBonus / 100.0) * $gajiPokok;
        $totalPendapatan = $gajiPokok + $tunjangan + $totalLembur + $totalBonus;

        Gaji::create([
            'id_karyawan' => $karyawan->id_karyawan,
            'id_lembur' => $validated['id_lembur'] ?? null,
            'periode' => $validated['periode'],
            'lama_lembur' => $lamaLembur,
            'total_lembur' => $totalLembur,
            'total_bonus' => $totalBonus,
            'total_tunjangan' => $tunjangan,
            'total_pendapatan' => $totalPendapatan,
            'created_at' => now(),
        ]);

        return redirect()->route('gaji.index')->with('success', 'Gaji dihitung dan disimpan');
    }

    public function edit(int $id): View
    {
        $editing = Gaji::findOrFail($id);
        $karyawans = Karyawan::with('jabatan')->orderBy('nama')->get();
        $lemburs = Lembur::orderByDesc('id_lembur')->get();
        return view('kantor.gaji.form', compact('editing', 'karyawans', 'lemburs'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|integer|exists:karyawan,id_karyawan',
            'id_lembur' => 'nullable|integer|exists:lembur,id_lembur',
            'periode' => 'required|string|max:7',
            'lama_lembur' => 'nullable|numeric',
        ]);

        $karyawan = Karyawan::with(['jabatan', 'rating'])->findOrFail($validated['id_karyawan']);
        $lembur = null;
        if (!empty($validated['id_lembur'])) {
            $lembur = Lembur::find($validated['id_lembur']);
        }

        $gajiPokok = (float) optional($karyawan->jabatan)->gaji_pokok;
        $tunjangan = (float) optional($karyawan->jabatan)->tunjangan;
        $persentaseBonus = (float) optional($karyawan->rating)->persentase_bonus;
        $tarifLembur = (float) optional($lembur)->tarif;
        $lamaLembur = (float) ($validated['lama_lembur'] ?? 0);

        $totalLembur = $lamaLembur * $tarifLembur;
        $totalBonus = ($persentaseBonus / 100.0) * $gajiPokok;
        $totalPendapatan = $gajiPokok + $tunjangan + $totalLembur + $totalBonus;

        Gaji::where('id_gaji', $id)->update([
            'id_karyawan' => $karyawan->id_karyawan,
            'id_lembur' => $validated['id_lembur'] ?? null,
            'periode' => $validated['periode'],
            'lama_lembur' => $lamaLembur,
            'total_lembur' => $totalLembur,
            'total_bonus' => $totalBonus,
            'total_tunjangan' => $tunjangan,
            'total_pendapatan' => $totalPendapatan,
            'updated_at' => now(),
        ]);

        return redirect()->route('gaji.index')->with('success', 'Gaji diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        Gaji::where('id_gaji', $id)->delete();
        return redirect()->route('gaji.index')->with('success', 'Gaji dihapus');
    }

    public function show(int $id): View
    {
        $detailGaji = Gaji::with(['karyawan.jabatan', 'karyawan.rating', 'lembur'])->findOrFail($id);
        return view('kantor.gaji.show', compact('detailGaji'));
    }
}
