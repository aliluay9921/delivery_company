<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        $user = User::with('permissions')->find($user->id);
        $get = $user->permissions()->whereIn('name', ['view employee', 'admin'])->where('active', 1)->first();
        return $get ? Response::allow()  : Response::deny('غير مصرح لك بالدخول الى هنا ');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $user = User::with('permissions')->find($user->id);
        $get = $user->permissions()->whereIn('name', ['add employee', 'admin'])->where('active', 1)->first();
        return $get ? Response::allow()  : Response::deny('غير مصرح لك بالدخول الى هنا ');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        $user = User::with('permissions')->find($user->id);
        $get = $user->permissions()->whereIn('name', ['edit employee', 'admin'])->where('active', 1)->first();
        return $get ? Response::allow()  : Response::deny('غير مصرح لك بالدخول الى هنا ');
    }

    public function toggleActiveUser(User $user)
    {
        $user = User::with('permissions')->find($user->id);
        $get = $user->permissions()->whereIn('name', ['edit employee', 'admin'])->where('active', 1)->first();
        return $get ? Response::allow()  : Response::deny('غير مصرح لك بالدخول الى هنا ');
    }

    public function companyBalance(User $user)
    {
        $user = User::with('permissions')->find($user->id);
        $get = $user->permissions()->whereIn('name', ['Company Balance', 'admin'])->where('active', 1)->first();
        return $get ? Response::allow()  : Response::deny('غير مصرح لك بالدخول الى هنا ');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}