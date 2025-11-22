<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        if ($user->id !== $model->id) {
            return Response::deny('Csak a saját profilodat nézheted.');
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    //Ez egy ellnőrző függvény
    //Ha rendben van: true
    //Ha nem, akkor false
    public function update(User $user, User $model): Response
    {
        // Először ellenőrizzük, hogy a bejelentkezett felhasználó azonos-e a módosítandóval.
        if ($user->id !== $model->id) {
            // Ha nem önmódosítás, és nem adminról van szó (az adminra vonatkozik a before() metódus), 
            // megtagadjuk. De a before() miatt ez a rész főleg a nem-adminokra érvényes.
            if ($user->role !== 1) {
                //vissza: false
                return Response::deny('Csak a saját profilodat módosíthatod.');
            }
        }

        // 3. ADMIN SPECIÁLIS SZABÁLY: Megnézzük, próbál-e a user a saját 'role' mezőjén módosítani.
        // Ezt az ellenőrzést csak akkor végezzük el, ha valóban az admin magát módosítja.
        if ($user->role === 1 && $user->id === $model->id) {

            // Ha az admin megpróbálja a bemeneti adatokkal megváltoztatni a role mezőt:
            $request = request();
            if ($request->has('role') && (int)$request->input('role') !== $user->role) {
                return Response::deny('Admin: Nem módosíthatod a saját szerepkörödet.');
            }
        }

        // Ha minden ellenőrzésen átment (önmódosítás, és nem sérti az admin korlátozásokat).
        //Viszza: true
        return Response::allow();
    }

    public function updateAdmin(User $user, User $model): Response
    {

        // Amin  aját 'role' mezőjét nem módosíthatja.
        if ($user->role === 1 && $user->id === $model->id) {

            // Ha az admin megpróbálja a bemeneti adatokkal megváltoztatni a role mezőt:
            $request = request();
            if ($request->has('role') && (int)$request->input('role') !== $user->role) {
                return Response::deny('Admin: Nem módosíthatod a saját szerepkörödet.');
            }
        }

        // Ha minden ellenőrzésen átment (önmódosítás, és nem sérti az admin korlátozásokat).
        return Response::allow();
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        // Csak akkor engedélyezzük a törlést, ha a bejelentkezett felhasználó azonos a törlendővel.

        return $user->id === $model->id && $user->role > 1
            ? Response::allow()
            : Response::deny('Mehiúsult a delete: Csak a saját profilodat törölheted csak, vagy redszergazda vagy.');
    }

    public function deleteAdmin(User $user, User $model): Response
    {

        // Öntörlés kizárva
        return $user->id !== $model->id
            ? Response::allow()
            : Response::deny('Csak a saját profilodat törölheted.');
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
