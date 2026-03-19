<?php

namespace App\Http\Controllers;

use App\Models\Puroks;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PuroksController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.puroks.index');
    }

    public function create()
    {
        return view('pages.puroks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'PurokNo'   => ['required','integer'],
            'PurokName' => ['required','string','max:100'],
        ]);

        DB::beginTransaction();

        try {

            $exists = Puroks::where('PurokNo', $data['PurokNo'])
                ->orWhere('PurokName', $data['PurokName'])
                ->exists();

            if ($exists) {
                return back()->with('error','Puroks already exists');
            }

            $purok = new Puroks();
            $purok->fill($data);
            $this->setCommonFields($purok);
            $purok->save();

            DB::commit();

            return redirect()
                ->route('puroks.index')
                ->with('success','Puroks created successfully');

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error','Failed to create purok');
        }
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $purok = Puroks::findOrFail($id);

        return view('pages.puroks.create', compact('purok'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $purok = Puroks::findOrFail($id);

        $data = $request->validate([
            'PurokNo'   => ['required','integer'],
            'PurokName' => ['required','string','max:100'],
        ]);

        $exists = Puroks::where(function($q) use ($data){
            $q->where('PurokNo', $data['PurokNo'])
                ->orWhere('PurokName', $data['PurokName']);
        })
            ->where('id','!=',$purok->id)
            ->exists();

        if ($exists) {
            return back()->with('error','Puroks already exists');
        }

        $purok->update($data);

        return redirect()
            ->route('puroks.index')
            ->with('success','Puroks updated successfully');
    }

    public function destroy($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $purok = Puroks::findOrFail($id);
        $purok->delete();

        return response()->json(['success'=>true]);
    }

    public function ajaxData(Request $request)
    {
        $query = Puroks::query();

        return DataTables::eloquent($query)
            ->addColumn('actions', function ($row) {

                return '
                <div class="dropdown">
                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="'.route('puroks.edit',encrypt($row->id)).'" class="dropdown-item">
                                <i class="bi bi-pencil me-2"></i> Edit
                            </a>
                        </li>
                    </ul>
                </div>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('M d, Y h:i A');
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function puroks_search(Request $request)
    {
        $search = $request->input('search');

        $puroks = Puroks::query()
            ->when($search, function ($q) use ($search) {
                $q->where('PurokName', 'like', "%{$search}%")
                    ->orWhere('PurokNo', 'like', "%{$search}%");
            })
            ->where('archived', 0)
            ->whereNull('deleted_at')
            ->orderBy('PurokNo','asc')
            ->limit(10)
            ->get(['id','PurokNo','PurokName']);

        return response()->json(
            $puroks->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => 'Puroks '.$item->PurokNo.' - '.$item->PurokName
                ];
            })
        );
    }
}
