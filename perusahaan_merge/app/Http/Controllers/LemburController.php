<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LemburController extends Controller
{
    public function index(): View
    {
        $rows = Lembur::orderByDesc('id_lembur')->get();
        return view('kantor.lembur.index', compact('rows'));
    }

    public function create(): View
    {
        return view('kantor.lembur.form', ['editing' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tarif' => 'required|numeric',
        ]);
        Lembur::create($data);
        return redirect()->route('lembur.index')->with('success', 'Tarif lembur disimpan');
    }

    public function edit(int $id): View
    {
        $editing = Lembur::findOrFail($id);
        return view('kantor.lembur.form', compact('editing'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'tarif' => 'required|numeric',
        ]);
        Lembur::where('id_lembur', $id)->update($data);
        return redirect()->route('lembur.index')->with('success', 'Tarif lembur diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        Lembur::where('id_lembur', $id)->delete();
        return redirect()->route('lembur.index')->with('success', 'Tarif lembur dihapus');
    }

    public function show(int $id): View
    {
        $detailLembur = Lembur::findOrFail($id);
        return view('kantor.lembur.show', compact('detailLembur'));
    }
}
