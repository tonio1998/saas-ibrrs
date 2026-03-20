<?php

namespace App\Http\Controllers;

use App\Models\Puroks;
use App\Models\Residents;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ResidentsController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.residents.index');
    }

    public function show($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $resident = Residents::with(['info', 'businesses'])->findOrFail($id);
//        dd($resident);
        return view('pages.residents.show', compact('resident'));
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $resident = Residents::findOrFail($id);

        return view('pages.residents.create', compact('resident'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $resident = Residents::findOrFail($id);

        $data = $request->validate(
            [
                'FirstName'     => ['required','string','max:255'],
                'MiddleName'    => ['nullable','string','max:255'],
                'LastName'      => ['required','string','max:255'],
                'Suffix'        => ['nullable','string','max:10'],
                'gender'           => ['required','in:Male,Female'],
                'BirthDate'     => ['required','date','before:today'],
                'CivilStatus'   => ['nullable','string','max:50'],
                'Occupation'    => ['nullable','string','max:100'],
                'household_id'  => ['required','exists:households,id'],
                'is_head'       => ['nullable','boolean'],
                'is_voter'      => ['nullable','boolean'],
            ],
            [
                'FirstName.required'    => 'First name is required.',
                'LastName.required'     => 'Last name is required.',
                'gender.required'          => 'Gender is required.',
                'BirthDate.required'    => 'Birth date is required.',
                'BirthDate.before'      => 'Birth date must be in the past.',
                'household_id.required' => 'Household is required.',
                'household_id.exists'   => 'Selected household is invalid.',
            ]
        );

        $data['is_voter'] = $data['is_voter'] ?? 0;

        $resident->update($data);

        return redirect()
            ->route('residents.index')
            ->with('success','Resident updated successfully');
    }

    public function create()
    {
        return view('pages.residents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'FirstName'     => ['required','string','max:255'],
                'MiddleName'    => ['nullable','string','max:255'],
                'LastName'      => ['required','string','max:255'],
                'Suffix'        => ['nullable','string','max:10'],
                'gender'           => ['required','in:Male,Female'],
                'BirthDate'     => ['required','date'],
                'CivilStatus'   => ['nullable','string','max:50'],
                'Occupation'    => ['nullable','string','max:100'],
                'household_id'  => ['required','exists:households,id'],
                'is_head'       => ['nullable','boolean'],
                'is_voter'      => ['nullable','boolean'],
            ],
            [
                'FirstName.required'    => 'First name is required.',
                'LastName.required'     => 'Last name is required.',
                'gender.required'          => 'Gender is required.',
                'BirthDate.required'    => 'Birth date is required.',
                'household_id.required' => 'Household is required.',
                'household_id.exists'   => 'Selected household is invalid.',
            ]
        );

        $data['is_voter'] = $data['is_voter'] ?? 0;

        $resident = new Residents();
        $resident->fill($data);
        $this->setCommonFields($resident);
        $resident->save();

        return redirect()
            ->route('residents.show',encrypt($resident->id))
            ->with('success','Resident created successfully');
    }

    public function ajaxData(Request $request, DataTables $datatables)
    {
        $query = Residents::with(['createdBy','household']);
        return $datatables->eloquent($query)
            ->addColumn('actions', function ($resident) {
                $menu = [];
                $menu[] = '
                    <li>
                        <a href="'.route('residents.edit',encrypt($resident->id)).'" class="dropdown-item">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                    </li>';

                $menu[] = '
                    <li>
                        <a href="'.route('residents.show',encrypt($resident->id)).'" class="dropdown-item">
                            <i class="bi bi-eye me-2"></i> Show
                        </a>
                    </li>';

                        if(empty($menu)) return '';

                        return '
                    <div class="dropdown">
                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            '.implode('', $menu).'
                        </ul>
                    </div>';
            })
            ->addColumn('name', function ($resident) {
                $name = $resident->FirstName.' '.$resident->LastName;
                if($resident->Suffix){
                    $name .= ' '.$resident->Suffix;
                }
                $html = "<div class='fw-bold'>{$name}</div>";
                return $html;
            })
            ->addColumn('household', function ($resident) {

                if(!$resident->household) return '-';

                return "
                <div>{$resident->household->household_code}</div>
            ";
            })
            ->addColumn('gender', function ($resident) {
                return $resident->gender;
            })
            ->addColumn('birthdate', function ($resident) {
                return $resident->BirthDate ? \Carbon\Carbon::parse($resident->BirthDate)->format('M d, Y') : '-';
            })
            ->addColumn('age', function ($resident) {
                return $resident->BirthDate ? \Carbon\Carbon::parse($resident->BirthDate)->age : '-';
            })
            ->addColumn('civil_status', function ($resident) {
                return $resident->CivilStatus ?? '-';
            })
            ->addColumn('occupation', function ($resident) {
                return $resident->Occupation ?? '-';
            })
            ->addColumn('voter', function ($resident) {
                return $resident->is_voter ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
            })
            ->editColumn('created_at', function ($resident) {
                return $resident->created_at->format('M d, Y h:i A');
            })
            ->addColumn('createdBy', function ($resident) {
                return $resident->createdBy->name ?? '-';
            })
            ->filterColumn('household', function ($query, $keyword) {
                return $query->where('FirstName', 'like', "%{$keyword}%")
                    ->orWhere('LastName', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions','name','household','voter'])
            ->make(true);
    }

    public function residents_search(Request $request)
    {
        $search = $request->input('search');

        $puroks = Residents::query()
            ->when($search, function ($q) use ($search) {
                $q->where('FirstName', 'like', "%{$search}%")
                    ->orWhere('LastName', 'like', "%{$search}%");
            })
            ->where('archived', 0)
            ->whereNull('deleted_at')
            ->orderBy('FirstName', 'asc')
            ->limit(10)
            ->get(['id','FirstName','LastName']);

        return response()->json(
            $puroks->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->FirstName.' '.$item->LastName
                ];
            })
        );
    }
}
