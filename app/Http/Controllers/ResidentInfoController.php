<?php

namespace App\Http\Controllers;

use App\Models\ResidentInfo;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class ResidentInfoController extends Controller
{
    use TCommonFunctions;
    public function store(Request $request)
    {
        $data = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'unit' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:50',
            'purok' => 'nullable|string|max:50',
            'barangay' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:10',
            'full_address' => 'nullable|string|max:600',
        ]);

        $exists = ResidentInfo::where('resident_id', $data['resident_id'])
            ->where('archived', 0)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Resident already has an address.');
        }

//        ResidentInfo::create($data);

            $info = new ResidentInfo();
            $info->fill($data);
            $this->setCommonFields($info);
            $info->save();
        return back()->with('success', 'Address added successfully.');
    }

    public function update(Request $request, $id)
    {
        $info = ResidentInfo::findOrFail($id);

        $data = $request->validate([
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

        $info->update($data);

        return back()->with('success', 'Address updated.');
    }

    public function destroy($id)
    {
        $info = ResidentInfo::findOrFail($id);

        $info->update([
            'archived' => 1,
            'deleted_at' => now(),
            'updated_by' => auth()->id() ?? 0,
        ]);

        return back()->with('success', 'Address removed.');
    }
}
