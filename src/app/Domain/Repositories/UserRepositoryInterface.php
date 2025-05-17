<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    /**
     * ユーザーをIDで検索
     */
    public function findById(int $id): ?User;

    /**
     * メールアドレスでユーザーを検索
     */
    public function findByEmail(string $email): ?User;

    /**
     * 全てのユーザーを取得
     * @return User[]
     */
    public function findAll(): array;

    /**
     * 削除されていないユーザーを取得
     * @return User[]
     */
    public function findAllActive(): array;

    /**
     * ユーザーを保存（新規作成または更新）
     */
    public function save(User $user): void;

    /**
     * ユーザーを削除
     */
    public function delete(User $user): void;
} 