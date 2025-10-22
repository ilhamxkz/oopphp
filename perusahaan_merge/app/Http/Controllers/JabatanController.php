<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function index(): View
    {
        $rows = Jabatan::orderByDesc('created_at')->get();
        return view('kantor.jabatan.index', compact('rows'));
    }

    public function create(): View
    {
        return view('kantor.jabatan.form', ['editing' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jabatan' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric',
            'tunjangan' => 'required|numeric',
        ]);
        $data['created_at'] = now();
        Jabatan::create($data);
        return redirect()->route('jabatan.index')->with('success', 'Jabatan disimpan');
    }

    public function edit(int $id): View
    {
        $editing = Jabatan::findOrFail($id);
        return view('kantor.jabatan.form', compact('editing'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'jabatan' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric',
            'tunjangan' => 'required|numeric',
        ]);
        Jabatan::where('id_jabatan', $id)->update($data);
        return redirect()->route('jabatan.index')->with('success', 'Jabatan diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        Jabatan::where('id_jabatan', $id)->delete();
        return redirect()->route('jabatan.index')->with('success', 'Jabatan dihapus');
    }

    public function show(int $id): View
    {
        $detailJabatan = Jabatan::findOrFail($id);
        return view('kantor.jabatan.show', compact('detailJabatan'));
    }
}
