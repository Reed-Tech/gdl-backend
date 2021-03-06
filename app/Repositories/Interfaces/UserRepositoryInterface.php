<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface {
    public function create(array $data): Model;

    public function getById(string $id): User;

    public function getByProviderToken(array $data): User;

    public function edit(array $data, User $user): User;

    public function profile(): Authenticatable;

    public function resetPassword(array $data, User $user): User;

    public function delete(string $userId);
}
