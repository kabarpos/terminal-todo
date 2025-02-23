<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAllWithRoles()
    {
        return $this->model->with('roles')->paginate(10);
    }

    // Add more methods as needed
}