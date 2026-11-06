<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $units = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(StoreUnitRequest $request)
    {
        Unit::create($request->validated());

        return redirect()
            ->route('admin.units.index')
            ->with('success', __('units.created'));
    }

    public function show(Unit $unit)
    {
        return view('admin.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return redirect()
            ->route('admin.units.index')
            ->with('success', __('units.updated'));
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()
            ->route('admin.units.index')
            ->with('success', __('units.deleted'));
    }
}