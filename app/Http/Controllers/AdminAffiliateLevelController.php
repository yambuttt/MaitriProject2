<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLevel;
use Illuminate\Http\Request;

class AdminAffiliateLevelController extends Controller
{
    public function index()
    {
        $levels = AffiliateLevel::orderBy('id')->paginate(20);
        return view('dashboard.admin.affiliate_levels.index', compact('levels'));
    }

    public function create()
    {
        return view('dashboard.admin.affiliate_levels.form', [
            'level' => new AffiliateLevel(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateLevel($request);

        AffiliateLevel::create($data);

        return redirect()->route('admin.affiliate-levels.index')
            ->with('ok', 'Level berhasil dibuat.');
    }

    public function edit(AffiliateLevel $level)
    {
        return view('dashboard.admin.affiliate_levels.form', [
            'level' => $level,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, AffiliateLevel $level)
    {
        $data = $this->validateLevel($request);

        $level->update($data);

        return redirect()->route('admin.affiliate-levels.index')
            ->with('ok', 'Level berhasil diupdate.');
    }

    public function toggle(AffiliateLevel $level)
    {
        $level->is_active = !$level->is_active;
        $level->save();

        return back()->with('ok', 'Status level berhasil diubah.');
    }

    private function validateLevel(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'window_days' => ['required', 'integer', 'min:1', 'max:365'],
            'digiflazz_points' => ['required', 'integer', 'min:0', 'max:1000000'],
            'marketplace_points' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'window_days' => 'Window Days',
            'digiflazz_points' => 'Digiflazz Points',
            'marketplace_points' => 'Marketplace Points',
        ]);
    }
}
