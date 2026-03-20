<?php

namespace App\Http\Controllers;

use App\Libraries\CertificatePDF;
use App\Libraries\PDF\BasePDF;
use App\Libraries\PDF\CustomPDF;
use App\Libraries\PDF\TemplateFactory;
use App\Libraries\TemplateResolver;
use App\Models\CertificateRequest;
use App\Models\Certificates;
use App\Models\CertificatesType;
use App\Traits\TCommonFunctions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class CertificateRequestController extends Controller
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
            'resident_id' => ['required','integer','exists:residents,id'],
            'certificate_type_id' => ['required','integer','exists:certificate_types,id'],
            'business_id' => ['nullable','integer','exists:business_information,id'],
            'purpose' => ['required','string','max:150'],
            'remark' => ['nullable','string','max:150'],
        ]);

        DB::beginTransaction();

        try {

            $item = new CertificateRequest();
            $item->fill($data);
            $item->remark = 'Pending';
            $item->requested_at = now();
            $this->setCommonFields($item);
            $item->save();

            DB::commit();

            return redirect()->route('certificates_request.index')
                ->with('success','Certificate issued successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Certificate store failed',[
                'error'=>$e->getMessage()
            ]);

            return back()->withInput()->with('error','Failed to issue certificate');
        }
    }

    public function show($id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Throwable $e) {
            return redirect()
                ->route('certificates_request.index')
                ->with('error', 'Invalid certificate request ID.');
        }

        try {
            $certificate = CertificateRequest::with(['resident', 'certificateType'])
                ->findOrFail($decryptedId);
        } catch (\Throwable $e) {
            return redirect()
                ->route('certificates_request.index')
                ->with('error', 'Certificate request not found.');
        }

        return view('pages.certificates_request.show', compact('certificate'));
    }

    public function edit($id)
    {
        try { $id = decrypt($id); } catch (\Throwable $e) { abort(404); }

        $certificate = CertificateRequest::with(['resident','certificateType'])->findOrFail($id);

        return view('pages.certificates_request.create', compact('certificate'));
    }

    public function approve(Request $request)
    {
        $id = decrypt($request->id);

        return DB::transaction(function() use ($id){

            $cert = CertificateRequest::findOrFail($id);

            if($cert->remark !== 'Pending'){
                return response()->json(['error'=>'Already processed'],403);
            }

            $cert->update([
                'remark' => 'Approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            DB::table('certificate_logs')->insert([
                'certificate_id' => $cert->id,
                'action' => 'Approved',
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'archived' => 0,
                'status' => 'active'
            ]);

//            $fee = CertificatesType::find($cert->certificate_type_id)->fee;
//
//            $cert = new Certificates();
//            $cert->request_id = $id;
//            $cert->issued_by = auth()->id();
//            $cert->Fee = $fee ?? 0;
//            $cert->Remark = 'Processing';
//            $this->setCommonFields($cert);
//            $cert->save();

            return response()->json(['success'=>true]);
        });
    }

    public function issue(Request $request)
    {
        $request->validate([
            'request_id'     => 'required',
            'or_number'      => 'required|string|max:50',
            'amount_paid'    => 'required|numeric|min:0',
            'fee'            => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:20',
        ]);

        try {

            DB::beginTransaction();

            $requestId = decrypt($request->request_id);

            $certificateRequest = CertificateRequest::with('certificateType')
                ->findOrFail($requestId);

            if($certificateRequest->remark !== 'Approved'){
                return response()->json(['message'=>'Request not approved'], 422);
            }

            $existing = Certificates::where('request_id',$certificateRequest->id)->first();

            if($existing){
                return response()->json(['message'=>'Already issued'], 409);
            }

            $fee = $certificateRequest->certificateType->fee ?? 0;

            if($request->amount_paid < $fee){
                return response()->json(['message'=>'Insufficient payment'], 422);
            }

            $cert = new Certificates();
            $cert->request_id = $certificateRequest->id;
            $cert->issued_by = auth()->id();
            $cert->Fee = $fee;
            $cert->or_number = $request->or_number;
            $cert->amount_paid = $request->amount_paid;
            $cert->payment_method = $request->payment_method;
            $cert->payment_date = now();

            $this->setCommonFields($cert);

            $cert->save();

            DB::commit();

            return response()->json([
                'message' => 'Payment recorded successfully'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process payment'
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        $id = decrypt($request->id);

        return DB::transaction(function() use ($id){

            $cert = CertificateRequest::lockForUpdate()->findOrFail($id);

            if($cert->status !== 'Pending'){
                return response()->json(['error'=>'Already processed'],403);
            }

            $cert->update([
                'remark' => 'Rejected'
            ]);

            DB::table('certificate_logs')->insert([
                'certificate_id' => $cert->id,
                'action' => 'Rejected',
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'archived' => 0,
                'status' => 'active'
            ]);

            return response()->json(['success'=>true]);
        });
    }

    public function verify($control_no)
    {
        $certificate = CertificateRequest::with(['resident','certificateType'])
            ->where('ControlNo',$control_no)
            ->first();

        if(!$certificate){
            return view('certificates.verify',['status'=>'invalid']);
        }

        return view('certificates.verify',[
            'status'=>'valid',
            'certificate'=>$certificate
        ]);
    }

    public function print($control_no)
    {
        $cert = CertificateRequest::with(['resident.info','certificateType', 'certificateRecord', 'business'])
            ->where('ControlNo', $control_no)
            ->firstOrFail();

        if ($cert->remark !== 'Approved') {
            abort(403);
        }

        if (!$cert->certificateType) {
            return response()->view('pages.certificates.error', [
                'code' => 500,
                'message' => 'Document type not set',
                'sub' => 'The PDF could not be generated properly.'
            ], 500);
        }

        $template = TemplateFactory::make($cert->certificateType->template);

        if (!$template) {
            return view('pages.certificates.print', compact('cert'));
        }

        $pdf = new CustomPDF();

        $template->render($pdf, $cert);

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="certificate-'.$cert->ControlNo.'.pdf"');
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $data = $request->validate([
            'resident_id' => ['required','integer','exists:residents,id'],
            'certificate_type_id' => ['required','integer','exists:certificate_types,id'],
            'purpose' => ['required','string','max:150'],
            'remark' => ['nullable','string','max:150'],
        ]);

        DB::beginTransaction();

        try {

            $item = CertificateRequest::findOrFail($id);

            $item->update($data);

            DB::commit();

            return redirect()->route('certificates_request.index')
                ->with('success','Certificate updated');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Certificate update failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error','Failed to update');
        }
    }

    public function ajaxData(Request $request, DataTables $datatables)
    {
        $query = CertificateRequest::query()
            ->with([
                'resident:id,FirstName,MiddleName,LastName,Suffix',
                'certificateType:id,name',
                'certificateRecord:id,request_id'
            ])
            ->select([
                'id',
                'ControlNo',
                'resident_id',
                'certificate_type_id',
                'remark',
                'requested_at',
                'created_at'
            ])
            ->latest('id');

        return $datatables->eloquent($query)
            ->addColumn('actions', function ($row) {

                $printBtn = '';

                if ($row->remark === 'Approved' && $row->certificateRecord) {
                    $printBtn = '
                <li>
                    <a target="_blank" href="' . route('certificate-types.print', $row->ControlNo) . '" class="dropdown-item">
                        Print Certificate
                    </a>
                </li>';
                }

                return '
            <div class="dropdown">
                <button class="btn btn-soft-primary dropdown-toggle" data-bs-toggle="dropdown">
                    Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="' . route('certificates_request.edit', encrypt($row->id)) . '" class="dropdown-item">
                            Edit
                        </a>
                    </li>
                    <li>
                        <a href="' . route('certificates_request.show', encrypt($row->id)) . '" class="dropdown-item">
                            Show
                        </a>
                    </li>
                    ' . $printBtn . '
                </ul>
            </div>';
            })
            ->addColumn('control_no', fn($row) => $row->ControlNo)
            ->addColumn('resident', fn($row) => $row->resident->full_name ?? '')
            ->addColumn('type', fn($row) => $row->certificateType->name ?? '-')
            ->addColumn('status_badge', function ($row) {

                $color = match ($row->remark) {
                    'Approved' => 'success',
                    'Rejected' => 'danger',
                    default => 'warning'
                };

                return '<span class="badge bg-' . $color . '">' . $row->remark . '</span>';
            })
            ->editColumn('requested_at', fn($row) => optional($row->requested_at)->format('M d, Y h:i A'))
            ->editColumn('created_at', fn($row) => optional($row->created_at)->format('M d, Y h:i A'))
            ->rawColumns(['actions', 'status_badge'])
            ->make(true);
    }

    public function certificates_search(Request $request)
    {
        $search = $request->search;

        $items = CertificateRequest::query()
            ->with(['type','resident'])
            ->when($search, function ($q) use ($search) {
                $q->where('control_no', 'like', "%{$search}%")
                    ->orWhereHas('resident', function($r) use ($search){
                        $r->where('first_name','like',"%{$search}%")
                            ->orWhere('last_name','like',"%{$search}%");
                    });
            })
            ->where('archived', 0)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return response()->json(
            $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->control_no . ' - ' . ($item->resident->full_name ?? '')
                ];
            })
        );
    }

    private function generateControlNo()
    {
        $latest = Certificates::latest('id')->value('id') ?? 0;
        return 'CERT-' . now()->format('Y') . '-' . str_pad($latest + 1, 5, '0', STR_PAD_LEFT);
    }
}
