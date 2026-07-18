<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingZoneRequest;
use App\Http\Requests\Admin\UpdateShippingZoneRequest;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    public function index(): View
    {
        $zones = ShippingZone::orderBy('zone_name')->paginate(20);

        return view('admin.shipping-zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.shipping-zones.create');
    }

    public function store(StoreShippingZoneRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['districts'] = $this->parseDistricts($data['districts']);
        $data['is_active'] = $request->boolean('is_active', true);

        ShippingZone::create($data);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping zone created.');
    }

    public function edit(ShippingZone $shippingZone): View
    {
        return view('admin.shipping-zones.edit', ['zone' => $shippingZone]);
    }

    public function update(UpdateShippingZoneRequest $request, ShippingZone $shippingZone): RedirectResponse
    {
        $data = $request->validated();
        $data['districts'] = $this->parseDistricts($data['districts']);
        $data['is_active'] = $request->boolean('is_active');

        $shippingZone->update($data);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping zone updated.');
    }

    public function destroy(ShippingZone $shippingZone): RedirectResponse
    {
        if ($shippingZone->orders()->exists()) {
            return back()->withErrors(['zone' => 'Cannot delete a zone that has orders.']);
        }

        $shippingZone->delete();

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping zone deleted.');
    }

    private function parseDistricts(string $input): array
    {
        return collect(explode(',', $input))
            ->map(fn ($d) => trim($d))
            ->filter()
            ->values()
            ->all();
    }
}
