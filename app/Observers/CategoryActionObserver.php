<?php

namespace App\Observers;

use App\Category;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class CategoryActionObserver
{
    public function created(Category $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Category'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function deleting(Category $model)
    {
        $data  = ['action' => 'deleted', 'model_name' => 'Category'];
        $users = \App\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}
