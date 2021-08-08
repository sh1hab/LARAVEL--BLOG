<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public $afterCommit = true;

    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        $user->created_by = Auth::user() ? Auth::user()->id : null;
    }

    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        $user->updated_by = Auth::user() ? Auth::user()->id : null;
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        $user->deleted_by = Auth::user() ? Auth::user()->id : null;
        $user->save();
    }
}
