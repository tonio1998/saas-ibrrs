<?php

namespace App\Http\Controllers;

use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use TCommonFunctions;
    public function search(Request $request)
    {
        $search = $request->search;

        $roles = Role::query()
            ->when($search,function($q) use ($search){
                $q->where('name','like',"%{$search}%");
            })
            ->limit(10)
            ->get();

        return $roles->map(function($role){
            return [
                'id'=>$role->id,
                'text'=>$role->name
            ];
        });
    }

    public function index()
    {
        return view('pages.roles.index');
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('pages.roles.create',[
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:roles,name'],
            'details' => ['nullable','string','max:255'],
            'permissions' => ['nullable','array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'details' => $data['details']
        ]);

        if(isset($data['permissions'])){
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()
            ->route('roles.index')
            ->with('success','Role created successfully');
    }

    public function edit(Request $request)
    {
        $role = Role::findOrFail(decrypt($request->segment(3)));
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('pages.roles.create',[
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail(decrypt($id));

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles','name')->ignore($role->id)
            ],
            'description' => ['nullable','string','max:255'],
            'permissions' => ['nullable','array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);

        $permissions = Permission::whereIn('id', $data['permissions'] ?? [])->get();

        $role->syncPermissions($permissions);

        return redirect()
            ->route('roles.index')
            ->with('success','Role updated successfully');
    }

    public function ajaxData(Request $request)
    {
        $query = Role::with(['permissions']);

        return datatables()->eloquent($query)

            ->addColumn('actions', function ($role) {

                $menu = '';

                    $menu .= '
                <li>
                    <a href="'.route('roles.edit',encrypt($role->id)).'" class="dropdown-item">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>';

                if(!$menu) return '';

                return '
            <div class="dropdown">
                <button class="btn btn-soft-primary btn-sm p-2 px-3 dropdown-toggle" data-bs-toggle="dropdown">
                    Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    '.$menu.'
                </ul>
            </div>';
            })

            ->addColumn('name', function ($role) {
                return "<div class='fw-bold'>".($role->name ?? '')."</div>";
            })

            ->addColumn('permissions', function ($role) {

                $html = '';

                foreach($role->permissions as $permission){
                    $html .= "<span class='badge bg-soft-secondary text-secondary me-1 mb-1 border' style='width: 100px'>".$permission->name."</span>";
                }

                return $html;
            })

            ->editColumn('created_at', function ($role) {
                return $role->created_at
                    ? $role->created_at->format('M d, Y h:i A')
                    : '';
            })
            ->rawColumns(['actions','permissions','name', 'permissions'])
            ->make(true);
    }
}
