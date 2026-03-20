<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address\Regions;
use App\Models\Address\Provinces;
use App\Models\Address\Cities;
use App\Models\Address\Barangays;

class AddressController extends Controller
{
    private function res($data)
    {
        return response()->json($data);
    }

    public function regions(Request $request)
    {
        $search = $request->input('search');

        $data = Regions::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('code', 'asc')
            ->limit(15)
            ->get(['code', 'name'])
            ->map(function ($item) {
                return [
                    'id' => $item->code,
                    'text' => $item->name
                ];
            });

        return $this->res($data);
    }

    public function provinces(Request $request)
    {
        $search = $request->input('search');
        $region = $request->input('region');

        $data = Provinces::query()
            ->when($region, function ($q) use ($region) {
                $q->where('region_code', $region);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->limit(15)
            ->get(['code', 'name', 'region_code'])
            ->map(function ($item) {
                return [
                    'id' => $item->code,
                    'text' => $item->name
                ];
            });

        return $this->res($data);
    }

    public function cities(Request $request)
    {
        $search = $request->input('search');
        $province = $request->input('province');

        $data = Cities::query()
            ->when($province, function ($q) use ($province) {
                $q->where('province_code', $province);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->limit(15)
            ->get(['code', 'name', 'province_code'])
            ->map(function ($item) {
                return [
                    'id' => $item->code,
                    'text' => $item->name
                ];
            });

        return $this->res($data);
    }

    public function barangays(Request $request)
    {
        $search = $request->input('search');
        $city = $request->input('city');

        $data = Barangays::query()
            ->when($city, function ($q) use ($city) {
                $q->where('city_code', $city);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->limit(20)
            ->get(['code', 'name', 'city_code'])
            ->map(function ($item) {
                return [
                    'id' => $item->code,
                    'text' => $item->name
                ];
            });

        return $this->res($data);
    }
}
