<?php

namespace App\Observers;

use App\Notifications\DataChangeEmailNotification;
use App\User;
use Illuminate\Support\Facades\Notification;

class UserActionObserver
{
    public function created(User $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'User'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function updated(User $model)
    {
        $data  = ['action' => 'updated', 'model_name' => 'User'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function deleting(User $model)
    {
        $data  = ['action' => 'deleted', 'model_name' => 'User'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}
