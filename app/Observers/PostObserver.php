<?php

namespace App\Observers;

use App\Models\Post;
use Auth;

class PostObserver
{
    public $afterCommit = false;

    /**
     * Handle the Post "creating" event.
     *
     * @param  \App\Models\Post  $user
     * @return void
     */
    public function creating(Post $post)
    {
        $post->created_by = Auth::user() ? Auth::user()->id : null;
    }

    /**
     * Handle the Post "updating" event.
     *
     * @param  \App\Models\Post  $user
     * @return void
     */
    public function updating(Post $post)
    {
        $post->updated_by = Auth::user() ? Auth::user()->id : null;
    }

    /**
     * Handle the Post "deleted" event.
     *
     * @param  \App\Models\Post  $user
     * @return void
     */
    public function deleted(Post $post)
    {
        $post->deleted_by = Auth::user() ? Auth::user()->id : null;
    }
}
