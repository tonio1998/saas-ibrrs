<?php

namespace App\Http\Controllers;

use App\Models\BusinessInformation;
use Illuminate\Http\Request;

class BusinessInfoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'business_name' => 'required|string|max:150',
            'operator_id' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:50',
            'purok' => 'nullable|string|max:50',
            'barangay' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:10',
        ]);

        $data['created_by'] = auth()->id() ?? 0;
        $data['updated_by'] = auth()->id() ?? 0;
        $data['status'] = 'active';

        BusinessInformation::create($data);

        return back()->with('success', 'Business added.');
    }

    public function update(Request $request, $id)
    {
        $biz = BusinessInformation::findOrFail($id);

        $data = $request->validate([
            'business_name' => 'required|string|max:150',
            'operator_id' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:50',
            'purok' => 'nullable|string|max:50',
            'barangay' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:10',
        ]);

        $data['updated_by'] = auth()->id() ?? 0;

        $biz->update($data);

        return back()->with('success', 'Business updated.');
    }

    public function destroy($id)
    {
        $biz = BusinessInformation::findOrFail($id);

        $biz->update([
            'archived' => 1,
            'deleted_at' => now(),
            'updated_by' => auth()->id() ?? 0,
        ]);

        return back()->with('success', 'Business removed.');
    }
}
