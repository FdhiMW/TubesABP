<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages    = Package::orderBy('sort_order')->orderBy('id')->paginate(15);
        $activeCount = Package::activeCount();

        return view('admin.packages.index', compact('packages', 'activeCount'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackage($request);

        // Cek max active
        if ($request->boolean('is_active') && Package::activeCount() >= Package::MAX_ACTIVE) {
            return back()->withErrors([
                'is_active' => 'Maksimal ' . Package::MAX_ACTIVE . ' paket aktif. Nonaktifkan salah satu dulu.',
            ])->withInput();
        }

        $validated['features']    = array_filter(array_map('trim', explode("\n", $request->features)));
        $validated['slug']        = Str::slug($validated['name']);
        $validated['is_popular']  = $request->boolean('is_popular');
        $validated['is_active']   = $request->boolean('is_active');
        $validated['sort_order']  = $request->sort_order ?? 0;

        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        // ATURAN: kalau paket sedang aktif, tidak boleh diedit
        if ($package->is_active) {
            return back()->withErrors([
                'is_active' => 'Paket sedang aktif. Nonaktifkan dulu sebelum mengedit.',
            ])->withInput();
        }

        $validated = $this->validatePackage($request, $package->id);

        // Cek max active jika user mau aktifkan
        if ($request->boolean('is_active') && Package::activeCount() >= Package::MAX_ACTIVE) {
            return back()->withErrors([
                'is_active' => 'Maksimal ' . Package::MAX_ACTIVE . ' paket aktif. Nonaktifkan salah satu dulu.',
            ])->withInput();
        }

        $validated['features']    = array_filter(array_map('trim', explode("\n", $request->features)));
        $validated['slug']        = Str::slug($validated['name']);
        $validated['is_popular']  = $request->boolean('is_popular');
        $validated['is_active']   = $request->boolean('is_active');
        $validated['sort_order']  = $request->sort_order ?? 0;

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil diupdate.');
    }

    public function destroy(Package $package)
    {
        if ($package->is_active) {
            return back()->withErrors([
                'is_active' => 'Paket sedang aktif. Nonaktifkan dulu sebelum menghapus.',
            ]);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    /**
     * Toggle status active dari halaman index (quick action).
     */
    public function toggleActive(Package $package)
    {
        if (! $package->is_active) {
            // Mau aktifkan — cek limit
            if (Package::activeCount() >= Package::MAX_ACTIVE) {
                return back()->withErrors([
                    'is_active' => 'Maksimal ' . Package::MAX_ACTIVE . ' paket aktif. Nonaktifkan salah satu dulu.',
                ]);
            }
        }

        $package->update(['is_active' => ! $package->is_active]);

        return back()->with('success',
            $package->is_active
                ? "Paket '{$package->name}' diaktifkan."
                : "Paket '{$package->name}' dinonaktifkan."
        );
    }

    /**
     * Validasi shared antara store & update.
     */
    private function validatePackage(Request $request, ?int $excludeId = null): array
    {
        return $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'price_label' => 'required|string|max:50',
            'tagline'     => 'nullable|string|max:255',
            'features'    => 'required|string',
            'color'       => 'required|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
        ]);
    }
}