<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserSelfRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function login(LoginUserRequest $request)
    {
        //Eltároljuk az adatokat változókba
        $email = $request->input(('email'));
        $password = $request->input(('password'));

        //Az email alapján megkeressük a usert
        $user = User::where('email', $email)->first();

        //Stimmel-e az email és a jelszó?
        if (!$user || !Hash::check($password, $password ? $user->password : '')) {
            return response()->json([
                'message' => 'invalid email or password'
            ], 401);
        }

        //Jó az email és a jelszó
        //Kitöröljük az esetleges tokenjeit
        //$user->tokens()->delete();

        //itt adjuk az új tokent időkorlát nélkül
        //$user->token = $user->createToken('access')->plainTextToken;

        //Lejárati idővel
        // $expirationTime = Carbon::now()->addSeconds(20);
        // $name = "20sec";
        // $expirationTime = Carbon::now()->addMinutes(30);
        // $name ="30min";
        // $expirationTime = Carbon::now()->addHours(4);;
        // $name ="4hours";


        $expirationTime = Carbon::now()->addDays(1);
        $role = $user->role;
        $name = "1day-role:$role";
        switch ($role) {
            case 1:
                //Admin
                $abilities = ['*'];
                break;
            case 2:
                //Raktáros
                $abilities = [
                    'usersme:delete',
                    'usersme:patch',
                    'usersme:get',
                    'products:create',
                    'products:delete',
                    'products:update',
                ];
                break;
            default:
                //Vásárló
                $abilities = [
                    'usersme:delete',
                    'usersme:patch',
                    'usersme:get',
                ];
                break;
        }


        $user->token = $user->createToken(
            $name,
            $abilities,
            $expirationTime
        )->plainTextToken;



        //visszaadjuk a usert, ami a tokent is tartalmazni fogja
        $data = [
            'message' => 'ok',
            'data' => $user
        ];
        $status = 200;

        //visszaadjuk a usert, ami a tokent is tartalmazni fogja
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    public function logout(Request $request)
    {
        // Minden tokent töröl (en nem jó, mert egy másik bejelntkezést is kivégez)
        //---------------------
        // // Az $request->user() segítségével hozzáférünk a bejelentkezett felhasználóhoz
        // $user = $request->user();

        // // Töröljük a felhasználó összes tokenjét
        // $user->tokens()->delete();

        // return response()->json(['message' => 'Successfully logged out']);


        //Egy mási módszer
        // Megkeresi a tokent és törli ---------------------
        $token = $request->bearerToken(); // Kivonjuk a bearer tokent a kérésből

        // Megkeressük a token modellt
        $personalAccessToken = PersonalAccessToken::findToken($token);

        if ($personalAccessToken) {
            $personalAccessToken->delete();
            $data = [
                'message' => 'ok',
                'data' => []
            ];
        } else {
            $data = [
                'message' => 'Token not found',
                'data' => []
            ];
        }
        return response()->json($data, options: JSON_UNESCAPED_UNICODE);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //code...
            $rows = User::all();
            // $sql ="SELECT * FROM products";
            // $rows = DB::select($sql);
            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $rows
            ];
        } catch (\Exception $e) {
            //throw $th;
            $status = 500;
            $data = [
                'message' => "Server error {$e->getCode()}",
                'data' => $rows
            ];
        }

        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $row = User::create($request->all());

            $data = [
                'message' => 'ok',
                'data' => $row
            ];
            // Sikeres válasz: 201 Created kód ajánlott új erőforrás létrehozásakor
            return response()->json($data, 201, options: JSON_UNESCAPED_UNICODE);
        } catch (QueryException $e) {
            // Ellenőrizzük, hogy ez egy "Duplicate entry for key" hiba-e (MySQL hibakód: 23000 vagy 1062)
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                $data = [
                    'message' => 'Insert error: The given name already exists, please choose another one',
                    'data' => [
                        'name' => $request->input('name') // Visszaküldhetjük, mi volt a hibás
                    ]
                ];
                // Kliens hiba, ami jelzi a kérés érvénytelenségét
                return response()->json($data, 409, options: JSON_UNESCAPED_UNICODE); // 409 Conflict ajánlott
            }
            // Ha nem ez a hiba volt, dobjuk tovább az eredeti kivételt, vagy kezeljük másképp
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $row = User::find($id);
        if ($row) {
            # code...
            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $row
            ];
        } else {
            # code...
            $status = 404;
            $data = [
                'message' => "Not found id: $id",
                'data' => null
            ];
        }
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $row = User::find($id);
        
        if ($row) {
            # code...
            $status = 200;
            //Szabd-e ezt nekem?
            $userToUpdate = $row;
            $this->authorize('updateAdmin', $userToUpdate);

            $row->update($request->all());

            $data = [
                'message' => 'OK',
                'data' => [
                    'data' => $row
                ]
            ];
        } else {
            # code...
            $status = 404;
            $data = [
                'message' => "Patch error. Not found id: $id",
                'data' => $id
            ];
        }
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $row = User::find($id);
        if ($row) {
            # code...
            $status = 200;
            $userToDestroy = $row;
            $this->authorize('deleteAdmin', $userToDestroy);
            $row->delete();

            $data = [
                'message' => 'OK',
                'data' => [
                    'id' => $id
                ]
            ];
        } else {
            # code...
            $status = 404;
            $data = [
                'message' => "Delete error. Not found id: $id",
                'data' => null
            ];
        }
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    //Önmagam törlése
    public function destroySelf(Request $request)
    {
        //Kivesszük a törlendő user-t
        $userToDestroy = $request->user();
        // A Policy-t használjuk: 
        $this->authorize('delete', $userToDestroy);
        // ... törlés logika
        //A user tokenjeinek törlése
        $userToDestroy->tokens()->delete();
        //A user törlése
        $userToDestroy->delete();

        $status = 404;
        $data = [
            'message' => "Sikeresen törölted a fiókodat",
            'data' => null
        ];
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }


    //Önmagam módosítása
    public function updateSelf(UpdateUserSelfRequest $request)
    {

        //Kivesszük a módosítandó user-t
        $userToUpdate = $request->user();
        // A Policy-t használjuk: 
        $this->authorize('update', $userToUpdate);

        $status = 200;
        $userToUpdate->update($request->all());

        $data = [
            'message' => 'OK',
            'data' => [
                'data' => $userToUpdate
            ]
        ];

        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    //Önmagam megnézése
    public function indexSelf(Request $request)
    {
        //Kivesszük a megmutatandó usert
        $userToGet= $request->user();
        // A Policy-t használjuk: 
        $this->authorize('view', $userToGet);
        $status = 200;
        $data = [
            'message' => 'OK',
            'data' => [
                'data' => $userToGet
            ]
        ];
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }
}