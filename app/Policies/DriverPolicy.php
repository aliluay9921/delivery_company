<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
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
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Driver $driver)
    {
        //
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
        $get = $user->permissions()->where('name', 'add driver')->where('active', 1)->first();
        return $get ? Response::allow()  : Response::deny('You are not the author of the post.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Driver $driver)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Driver $driver)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Driver $driver)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Driver $driver)
    {
        //
    }
}