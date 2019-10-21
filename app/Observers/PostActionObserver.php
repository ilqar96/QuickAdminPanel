<?php

namespace App\Observers;

use App\Notifications\DataChangeEmailNotification;
use App\Post;
use Illuminate\Support\Facades\Notification;

class PostActionObserver
{
    public function created(Post $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Post'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function deleting(Post $model)
    {
        $data  = ['action' => 'deleted', 'model_name' => 'Post'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}
