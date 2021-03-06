<?php
namespace App\Repositories;

use App\Exceptions\UnprocessableEntityException;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Dotenv\Exception\ValidationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository implements UserRepositoryInterface {

    /**
     * UserRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $data
     * @return Model
     */

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param string $id
     * @return User
     */
    public function getById(string $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return User
     */
    public function getByProviderToken(array $data): User
    {
        return $this->model->where([
            ['provider_id', $data['provider_id']],
            ['provider', $data['provider']],
            ['user_type', $data['user_type']]
        ])->first();
    }

    /**
     * @param array $data
     * @param User $user
     * @return User
     */
    public function edit(array $data, User $user): User
    {
        $user = $this->model->findOrFail($user->id);
        $user->fill($data);
        if ($user->isClean()) {
            throw new UnprocessableEntityException("User cannot be updated as details is the same");
        }
        return $user;
    }

    /**
     * @return Authenticatable
     */
    public function profile(): Authenticatable
    {
        // TODO: Implement profile() method.
        return Auth::user();
    }

    /**
     * @param array $data
     * @param User $user
     * @return User
     */
    public function resetPassword(array $data, User $user): User
    {

    }

    /**
     * @param string $userId
     */
    public function delete(string $userId)
    {
        $this->model->where("id", $userId)->delete();
    }
}
