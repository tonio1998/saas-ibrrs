<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\QrCodes;
use App\Models\Residents;
use App\Models\Teachers;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    use TCommonFunctions;
    public function index()
    {

        $users = User::latest()->paginate(10);

        return view('pages.users.index', compact('users'));

    }

    public function users_search(Request $request)
    {
        $search = $request->search;
        $users = User::query()
            ->when($search,function($q) use ($search){
                $q->where('name','like',"%{$search}%");
            })
            ->limit(10)
            ->get();
        return $users->map(function($user){
            return [
                'id'=>$user->id,
                'text'=>$user->name
            ];
        });
    }

    public function editRoles(Request $request)
    {
        $user = User::findOrFail(decrypt($request->segment(2)));
        $roles = Role::all();
        return view('pages.users.roles',compact('user','roles'));
    }

    public function editPermissions(Request $request)
    {
        $user = User::findOrFail(decrypt($request->segment(2)));
        $permissions = Permission::all();
        return view('pages.users.permissions',compact('user','permissions'));
    }

    public function updateRoles(Request $request, User $user)
    {
        $roles = $request->roles ?? [];
        $user->syncRoles($roles);

        return redirect()->back()->with('success','Roles updated successfully.');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $permissions = $request->permissions ?? [];
        $user->syncPermissions($permissions);

        return redirect()->back()->with('success','Permissions updated successfully.');
    }

    public function users_data(Request $request)
    {
        $users = User::with('roles')->select(['id','name','email']);

        return DataTables::of($users)
            ->addColumn('name', fn($user) => e($user->name))
            ->addColumn('email', fn($user) => e($user->email))
            ->addColumn('role', function($user){
                return $user->roles->pluck('name')->implode(', ');
            })
            ->addColumn('actions', function($user){
                $permissions = route('users.permissions',encrypt($user->id));
                $roles = route('users.roles',encrypt($user->id));

                return '
                <div class="d-flex align-items-center gap-2">
                    <a href="'.$permissions.'" class="btn btn-soft-primary btn-sm">
                        <i class="bi bi-key"></i> Assign Permissions
                    </a>
                    <a href="'.$roles.'" class="btn btn-soft-warning btn-sm">
                        <i class="bi bi-person-badge"></i> Assign Roles
                    </a>
                </div>
            ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {

        return view('pages.users.create');

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6']
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('users.index')
            ->with('success','User created successfully');

    }

    public function edit(User $user)
    {

        return view('pages.users.edit', compact('user'));

    }

    public function update(Request $request, User $user)
    {

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'password' => ['nullable','string','min:6']
        ]);

        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success','User updated successfully');

    }

    public function destroy(User $user)
    {

        $user->delete();

        return redirect()->route('users.index')->with('success','User deleted successfully');

    }

    private function generateQrCode()
    {
        $prefix = env('SCHOOL_ID');

        $lastRow = QrCodes::where('prefix', 1)
            ->orderBy('last_number', 'desc')
            ->first();

        $newNumber = ($lastRow->last_number ?? 0) + 1;

        $qrCodeRow = new QrCodes();
        $qrCodeRow->prefix = $prefix;
        $qrCodeRow->last_number = $newNumber;
        $qrCodeRow->created_by = 0;
        $qrCodeRow->updated_by = 0;
        $qrCodeRow->created_at = now();
        $qrCodeRow->updated_at = now();
        $qrCodeRow->status = 'active';
        $qrCodeRow->archived = 0;
        $qrCodeRow->save();

        return $prefix . str_pad($newNumber, 12, '0', STR_PAD_LEFT);
    }

    public function generatePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $UserTypeID = $request->segment(4);
        $user_type = $request->segment(3);
        $UserID = $request->segment(5);

        $newPassword = strtoupper(Str::random(6));

        DB::beginTransaction();

        try {

            if($user_type === 'residents'){
                $UserT = Residents::findOrFail($UserTypeID);
            }elseif($user_type === 'teachers'){
                $UserT = Teachers::findOrFail($UserTypeID);
            }elseif($user_type === 'parents'){
                $UserT = Parents::findOrFail($UserTypeID);
            }else{
                return response()->json(['message'=>'Invalid user type'],400);
            }

            $user = User::find($UserID);

            if(!$user){

                $base = strtolower(substr($UserT->FirstName,0,1).$UserT->LastName);
                $username = $base;
                $counter = 1;

                while(User::where('email',$username.env('SCHOOL_EMAIL'))->exists()){
                    $username = $base.$counter;
                    $counter++;
                }

                $email = $username.env('SCHOOL_EMAIL');

                $user = new User();
                $user->conn_id = $UserTypeID;
                $user->SchoolID = env('SCHOOL_ID');
                $user->name = $UserT->FirstName.' '.$UserT->LastName;
                $user->email = $email;
                $user->password = Hash::make($newPassword);
                $user->qr_code = $this->generateQrCode();

                $this->setCommonFields($user);

                $user->save();

            }else{

                $user->conn_id = $UserTypeID;
                $user->name = $UserT->FirstName.' '.$UserT->LastName;
                $user->password = Hash::make($newPassword);

                if(empty($user->qr_code) || $user->qr_code == '0'){
                    $user->qr_code = $this->generateQrCode();
                }

                $user->save();

                $email = $user->email;
            }

            $user->syncRoles($user_type);

            $UserT->UserID = $user->id;
            $UserT->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password generated successfully.',
                'password' => $newPassword,
                'username' => $email
            ]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],500);

        }
    }

}
