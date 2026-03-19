<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Certificates;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CertificatesController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.certificates_request.index');
    }

    public function create()
    {
        return view('pages.certificates_request.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'request_id' => ['nullable','integer','exists:certificate_requests,id'],
        ]);

        DB::beginTransaction();

        try {

            $certificate = new Certificates();
            $certificate->fill($data);

            $certificate->control_no = $this->generateControlNo();
            $certificate->issued_at = now();

            $this->setCommonFields($certificate);

            $certificate->save();

            DB::commit();

            return redirect()
                ->route('certificates_request.index')
                ->with('success','Certificate issued successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Certificate store failed', ['error'=>$e->getMessage()]);

            return back()
                ->withInput()
                ->with('error','Failed to issue certificate');
        }
    }

    public function edit($id)
    {
        try { $id = decrypt($id); } catch (\Throwable $e) { abort(404); }

        $certificate = Certificates::findOrFail($id);

        return view('pages.certificates_request.create', compact('certificate'));
    }

    public function update(Request $request, $id)
    {
        try { $id = decrypt($id); } catch (\Throwable $e) { abort(404); }

        $data = $request->validate([
            'remark' => ['nullable','string','max:50'],
        ]);

        DB::beginTransaction();

        try {

            $certificate = Certificates::findOrFail($id);
            $certificate->update($data);

            DB::commit();

            return redirect()
                ->route('certificates_request.index')
                ->with('success','Certificate updated');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Certificate update failed', ['error'=>$e->getMessage()]);

            return back()
                ->withInput()
                ->with('error','Failed to update');
        }
    }

    public function destroy($id)
    {
        try { $id = decrypt($id); } catch (\Throwable $e) { abort(404); }

        Certificates::findOrFail($id)->delete();

        return response()->json(['success'=>true]);
    }

    public function ajaxData(Request $request, DataTables $datatables)
    {
        $query = Certificates::query();

        return $datatables->eloquent($query)
            ->addColumn('actions', function ($row) {

                return '
                <div class="dropdown">
                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="'.route('certificates_request.edit',encrypt($row->id)).'" class="dropdown-item">
                                <i class="bi bi-pencil me-2"></i> Edit
                            </a>
                        </li>
                        <li>
                            <button data-id="'.encrypt($row->id).'" class="dropdown-item btn-delete">
                                <i class="bi bi-trash me-2"></i> Delete
                            </button>
                        </li>
                    </ul>
                </div>';
            })
            ->addColumn('status_badge', function($row){
                return '<span class="badge bg-success">'.$row->status.'</span>';
            })
            ->editColumn('issued_at', function ($row) {
                return $row->issued_at ? date('M d, Y h:i A', strtotime($row->issued_at)) : '-';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? date('M d, Y h:i A', strtotime($row->created_at)) : '-';
            })
            ->rawColumns(['actions','status_badge'])
            ->make(true);
    }

    private function generateControlNo()
    {
        do {
            $code = 'CERT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Certificates::where('control_no',$code)->exists());

        return $code;
    }
}
