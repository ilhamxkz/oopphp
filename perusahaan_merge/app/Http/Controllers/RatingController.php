<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RatingController extends Controller
{
    public function index(): View
    {
        $rows = Rating::orderByDesc('id_rating')->get();
        return view('kantor.rating.index', compact('rows'));
    }

    public function create(): View
    {
        return view('kantor.rating.form', ['editing' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rating' => 'required|string|max:255',
            'persentase_bonus' => 'required|numeric',
        ]);
        Rating::create($data);
        return redirect()->route('rating.index')->with('success', 'Rating disimpan');
    }

    public function edit(int $id): View
    {
        $editing = Rating::findOrFail($id);
        return view('kantor.rating.form', compact('editing'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'rating' => 'required|string|max:255',
            'persentase_bonus' => 'required|numeric',
        ]);
        Rating::where('id_rating', $id)->update($data);
        return redirect()->route('rating.index')->with('success', 'Rating diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        Rating::where('id_rating', $id)->delete();
        return redirect()->route('rating.index')->with('success', 'Rating dihapus');
    }

    public function show(int $id): View
    {
        $detailRating = Rating::findOrFail($id);
        return view('kantor.rating.show', compact('detailRating'));
    }
}
