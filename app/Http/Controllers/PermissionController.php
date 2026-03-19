<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        return view('pages.permissions.index');
    }

    public function ajaxData()
    {
        $query = Permission::query();

        return datatables()->eloquent($query)

            ->addColumn('actions', function ($permission) {

                $menu = '';

                $menu .= '
                <li>
                    <a href="'.route('permissions.edit',encrypt($permission->id)).'" class="dropdown-item">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>';

                $menu .= '
                <li>
                    <a href="#" data-id="'.encrypt($permission->id).'" class="dropdown-item deletePermission">
                        <i class="bi bi-trash me-2"></i> Delete
                    </a>
                </li>';

                return '
                <div class="dropdown">
                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        '.$menu.'
                    </ul>
                </div>';
            })

            ->editColumn('created_at', function ($permission) {
                return $permission->created_at
                    ? $permission->created_at->format('M d, Y h:i A')
                    : '';
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    public function create()
    {
        return view('pages.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:permissions,name'],
            'details' => ['nullable','string','max:255'],
        ]);

        Permission::create([
            'name' => $data['name'],
            'details' => $data['details'] ?? null
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success','Permission created successfully');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail(decrypt($id));

        return view('pages.permissions.create',[
            'permission' => $permission
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail(decrypt($id));

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions','name')->ignore($permission->id)
            ],
            'details' => ['nullable','string','max:255'],
        ]);

        $permission->update([
            'name' => $data['name'],
            'details' => $data['details'] ?? null
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success','Permission updated successfully');
    }

    public function destroy(Request $request)
    {
        $permission = Permission::findOrFail(decrypt($request->id));

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
