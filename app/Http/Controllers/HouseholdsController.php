<?php

namespace App\Http\Controllers;

use App\Models\Households;
use App\Models\Puroks;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class HouseholdsController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        $puroks = Puroks::get();
        return view('pages.households.index', compact('puroks'));
    }

    public function create()
    {
        return view('pages.households.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'purok_id' => ['required','integer','exists:puroks,id'],
            'head_id'  => ['required','integer','exists:residents,id'],
        ]);

        DB::beginTransaction();

        try {

            $household = new Households();
            $household->fill($data);
            $this->setCommonFields($household);
            $household->save();

            DB::commit();

            return redirect()
                ->route('households.index')
                ->with('success','Household created successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Household store failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->with('error','Failed to create household');
        }
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $household = Households::findOrFail($id);

        return view('pages.households.create', compact('household'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $data = $request->validate([
            'purok_id' => ['required','integer','exists:puroks,id'],
            'head_id'  => ['required','integer','exists:residents,id'],
        ]);

        DB::beginTransaction();

        try {

            $household = Households::findOrFail($id);
            $household->update($data);

            DB::commit();

            return redirect()
                ->route('households.index')
                ->with('success','Household updated successfully');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Household update failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->with('error','Failed to update household');
        }
    }

    public function destroy($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $household = Households::findOrFail($id);
        $household->delete();

        return response()->json(['success' => true]);
    }

    public function ajaxData(Request $request, DataTables $datatables)
    {
        $query = Households::with(['purok','head'])
            ->withCount('residents')
            ->orderByDesc('id');

        if ($request->filled('purok')) {
            $query->where('purok_id', $request->purok);
        }

        if ($request->filled('has_head')) {
            if ($request->has_head == '1') {
                $query->where('head_id', '>', 0);
            } else {
                $query->whereIn('head_id', [NULL, 0]);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $datatables->eloquent($query)
            ->addColumn('actions', function ($row) {
                return '
            <div class="dropdown">
                <button class="btn btn-soft-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="'.route('households.edit',encrypt($row->id)).'" class="dropdown-item">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                    </li>
                </ul>
            </div>';
            })
            ->addColumn('purok', fn($row) =>
            $row->purok
                ? 'Purok '.$row->purok->PurokNo.' - '.$row->purok->PurokName
                : '-'
            )
            ->addColumn('head', fn($row) =>
            $row->head
                ? '<div class="fw-bold">'.$row->head->FirstName.' '.$row->head->LastName.'</div>'
                : '<span class="badge bg-danger">No head</span>'
            )
            ->addColumn('members', fn($row) =>
                '<span class="badge bg-primary">'.$row->residents_count.'</span>'
            )
            ->editColumn('created_at', fn($row) =>
                $row->created_at?->format('M d, Y h:i A') ?? '-'
            )
            ->rawColumns(['actions','members','head'])
            ->make(true);
    }

    public function households_search(Request $request)
    {
        $search = $request->input('search');

        $households = Households::query()
            ->with([
                'head:id,FirstName,LastName',
                'purok:id,PurokName'
            ])
            ->when($search, function ($q) use ($search) {
                $q->where('household_code', 'like', "%{$search}%")
                    ->orWhereHas('head', function ($q2) use ($search) {
                        $q2->where('FirstName', 'like', "%{$search}%")
                            ->orWhere('LastName', 'like', "%{$search}%");
                    });
            })
            ->where('archived', 0)
            ->orderBy('household_code', 'asc')
            ->limit(10)
            ->get(['id', 'household_code', 'purok_id', 'head_id']);

        return response()->json(
            $households->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => trim(
                        $item->household_code . ' | ' .
                        ($item->head
                            ? $item->head->FirstName . ' ' . $item->head->LastName
                            : 'No Head')
                    )
                ];
            })
        );
    }
}
