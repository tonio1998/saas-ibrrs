<?php

namespace App\Http\Controllers;

use App\Models\CertificatesType;
use Illuminate\Http\Request;

class CertificateTypesController extends Controller
{
    public function certificate_types_search(Request $request)
    {
        $search = $request->search;

        $types = CertificatesType::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->where('archived', 0)
            ->whereNull('deleted_at')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json(
            $types->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            })
        );
    }
}
