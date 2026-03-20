<?php

namespace App\Http\Controllers;

use App\Models\BusinessInformation;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessInfoController extends Controller
{
    use TCommonFunctions;

    public function businesses_search(Request $request){
        $search = $request->search;
        $residentId = $request->resident_id;

        $biz = BusinessInformation::query()
            ->when($search, function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%");
            })
            ->where('resident_id', $residentId)
            ->where('archived', 0)
            ->whereNull('deleted_at')
            ->orderBy('business_name', 'asc')
            ->limit(10)
            ->get(['id','business_name', 'TinNo']);

        return $biz->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->business_name . ' - ' . $item->TinNo
            ];
        });
    }
    public function store(Request $request)
    {
//        dd($request->all());
        $data = $request->validate([
            'resident_id'   => 'required|exists:residents,id',
            'business_name' => 'required|string|max:150',
            'operator_type' => 'required|in:resident,custom',
            'operator_name' => 'nullable|required_if:operator_type,custom|string|max:150',
            'unit'      => 'nullable|string|max:50',
            'street'    => 'nullable|string|max:50',
            'purok'     => 'nullable|string|max:50',
            'barangay'  => 'nullable|string|max:50',
            'city'      => 'nullable|string|max:50',
            'province'  => 'nullable|string|max:50',
            'region'    => 'nullable|string|max:50',
            'zip'       => 'nullable|string|max:10',
            'full_address' => 'nullable|string|max:255',
            'tin_no'    => 'required|string|max:50',
        ]);

        if ($data['operator_type'] === 'custom') {
            $data['operator_name'] = $request->operator_name;
        } else {
            $data['operator_id'] = $request->resident_id;
        }

        $data['TinNo'] = $data['tin_no'];

        DB::transaction(function () use ($data) {
            $biz = new BusinessInformation();
            $biz->fill($data);
            $this->setCommonFields($biz);
            $biz->save();
        });

        return back()->with('success', 'Business added.');
    }

    public function update(Request $request, $id)
    {
        $biz = BusinessInformation::findOrFail($id);

        $data = $request->validate([
            'business_name' => 'required|string|max:150',

            'operator_type' => 'required|in:resident,custom',
            'operator_name' => 'nullable|required_if:operator_type,custom|string|max:150',

            'unit'      => 'nullable|string|max:50',
            'street'    => 'nullable|string|max:50',
            'purok'     => 'nullable|string|max:50',
            'barangay'  => 'nullable|string|max:50',
            'city'      => 'nullable|string|max:50',
            'province'  => 'nullable|string|max:50',
            'region'    => 'nullable|string|max:50',
            'zip'       => 'nullable|string|max:10',
            'full_address' => 'nullable|string|max:255',
        ]);

        if ($data['operator_type'] === 'custom') {
            $data['operator_name'] = $request->operator_name;
            $data['operator_id'] = null;
        } else {
            $data['operator_id'] = $biz->resident_id;
            $data['operator_name'] = null;
        }

        DB::transaction(function () use ($biz, $data) {
            $biz->fill($data);
            $this->setCommonFields($biz);
            $biz->save();
        });

        return back()->with('success', 'Business updated.');
    }

    public function destroy($id)
    {
        $biz = BusinessInformation::findOrFail($id);

        $biz->update([
            'archived'   => 1,
            'status'     => 'inactive',
            'deleted_at' => now(),
            'updated_by' => auth()->id() ?? 0,
        ]);

        return back()->with('success', 'Business removed.');
    }

    private function composeAddress(array $data): string
    {
        return collect([
            $data['unit'] ?? null,
            $data['street'] ?? null,
            isset($data['purok']) && $data['purok'] ? 'Purok '.$data['purok'] : null,
            $data['barangay'] ?? null,
            $data['city'] ?? null,
            $data['province'] ?? null,
            $data['region'] ?? null,
        ])->filter()->implode(', ');
    }
}
