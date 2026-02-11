<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\PatchUserRequest;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User :: query()
        ->when(
            $request->has('username'),
            fn ($query)=> $query->where('username', 'like', '%' . $request->input('username').'%')
        )
        ->when(
            $request->has('email'),
            fn ($query)=> $query->where('email', 'like', '%'. $request->input('email').'%')
        )->when(
            $request->boolean('is_trashed'), 
            fn ($query) => $query->onlyTrashed()
        )

        ->get();

        return UserResource::collection($users);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Str::random(8); // Le colocamos una contraseña por defecto

        $user = User::create($data);
        
        return response()->json(UserResource::make($user), 201);
    }

    public function show (User $user){
        return UserResource::make($user);
    }

   public function update (UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        $user->update($data);
        
        return UserResource::make($user);
    }

    public function partialUpdate (PatchUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        $user->update($data);
        
        return UserResource::make($user);
    }

    public function delete($id) 
    {
       $user = User::find($id);

        if(!$user){
            return response()->json([
                'message' => 'El usuario no existe.'
            ], 404);
        }
            
        $user->delete();

        return response()->json([
            'message' => 'El usuario ha sido eliminado correctamente.'
        ], 200);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

    
        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado en los registros eliminados.'
            ], 404);
        }

        $user->restore();

        return response()->json([
            'message' => 'Usuario restaurado correctamente.',
            'user' => $user
        ], 200);
    }

}
