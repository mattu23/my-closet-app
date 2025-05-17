<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    /**
     * モデルからエンティティに変換
     */
    private function toEntity(UserModel $model): User
    {
        return new User(
            $model->id,
            $model->name,
            $model->email,
            $model->password,
            $model->deleted_at ? $model->deleted_at->format('Y-m-d H:i:s') : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();
        return $model ? $this->toEntity($model) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $models = UserModel::all();
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllActive(): array
    {
        $models = UserModel::whereNull('deleted_at')->get();
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function save(User $user): void
    {
        DB::transaction(function () use ($user) {
            $model = UserModel::findOrNew($user->getId() ?: null);
            $model->name = $user->getName();
            $model->email = $user->getEmail();
            $model->password = $user->getPassword();
            
            if ($user->isDeleted()) {
                $model->deleted_at = $user->getDeletedAt();
            } else {
                $model->deleted_at = null;
            }
            
            $model->save();
            
            // IDが0（新規作成）の場合、エンティティにIDを設定
            if ($user->getId() === 0) {
                $reflectionClass = new \ReflectionClass($user);
                $reflectionProperty = $reflectionClass->getProperty('id');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($user, $model->id);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(User $user): void
    {
        $user->delete();
        $this->save($user);
    }
} 