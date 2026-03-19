<?php

namespace App\Http\Controllers;

use App\Models\CertificatesType;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class CertificateTypesController extends Controller
{
    use TCommonFunctions;
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

    public function index()
    {
        return view('pages.certificate-types.index');
    }

    public function create()
    {
        return view('pages.certificate-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:255'],
            'template' => ['nullable','string','max:255'],
            'fee' => ['required','numeric','min:0'],
        ]);

        DB::beginTransaction();

        try {

            $exists = CertificatesType::where('name', $data['name'])
                ->orWhere('description', $data['description'])
                ->exists();

            if ($exists) {
                return back()->with('error','Certificate types already exists');
            }

            $type = new CertificatesType();
            $type->fill($data);
            $this->setCommonFields($type);
            $type->save();

            DB::commit();

            return redirect()
                ->route('certificate-types.index')
                ->with('success','Certificate type created successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Certificate type store failed', [
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error','Failed to create certificate type');
        }
    }

    public function edit($id)
    {
        try { $id = decrypt($id); } catch (\Throwable $e) { abort(404); }

        $type = CertificatesType::findOrFail($id);

        return view('pages.certificate-types.create', compact('type'));
    }

    public function update(Request $request, $id)
    {
        try { $id = decrypt($id); } catch (\Throwable $e) { abort(404); }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:255'],
            'template' => ['nullable','string','max:255'],
            'fee' => ['required','numeric','min:0'],
        ]);

        DB::beginTransaction();

        try {

            $type = CertificatesType::findOrFail($id);

            $type->update($data);

            DB::commit();

            return redirect()
                ->route('certificate-types.index')
                ->with('success','Certificate type updated successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Certificate type update failed', [
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error','Failed to update certificate type');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $type = CertificatesType::findOrFail($id);

            $type->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Certificate type deleted successfully'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Certificate type delete failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete'
            ], 500);
        }
    }

    public function ajaxData(Request $request, DataTables $datatables)
    {
        $query = CertificatesType::query();

        return $datatables->eloquent($query)
            ->addColumn('actions', function ($type) {

                $menu = '';

                $menu .= '
                <li>
                    <a href="'.route('certificate-types.edit',encrypt($type->id)).'" class="dropdown-item">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>';

                $menu .= '
                <li>
                    <a href="#" data-id="'.($type->id).'" class="dropdown-item deleteType">
                        <i class="bi bi-trash me-2"></i> Delete
                    </a>
                </li>';

                return '
                <div class="dropdown">
                    <button class="btn btn-soft-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        '.$menu.'
                    </ul>
                </div>';
            })
            ->addColumn('name', function ($type) {
                return "<div class='fw-bold'>".($type->name ?? '')."</div>";
            })
            ->addColumn('description', function ($type) {
                return $type->description ?? '-';
            })
            ->addColumn('fee', function ($type) {
                return number_format($type->fee ?? 0, 2);
            })
            ->editColumn('created_at', function ($type) {
                return $type->created_at
                    ? $type->created_at->format('M d, Y h:i A')
                    : '';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions','name','description','fee'])
            ->make(true);
    }
}
