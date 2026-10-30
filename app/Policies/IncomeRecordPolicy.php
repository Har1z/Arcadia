<?php

namespace App\Policies;

use App\Models\IncomeRecord;
use App\Models\User;

class IncomeRecordPolicy
{
    /**
     * SECURITY: any authenticated user may see the list page and create
     * records — the index query itself limits staff to their own rows.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Detail page: admins see everything, staff only their own records.
     */
    public function view(User $user, IncomeRecord $incomeRecord): bool
    {
        return $user->isAdmin() || $incomeRecord->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * SECURITY: the ownership rule for edits — admin or record owner only.
     */
    public function update(User $user, IncomeRecord $incomeRecord): bool
    {
        return $user->isAdmin() || $incomeRecord->user_id === $user->id;
    }

    /**
     * SECURITY: the ownership rule for deletes — admin or record owner only.
     */
    public function delete(User $user, IncomeRecord $incomeRecord): bool
    {
        return $user->isAdmin() || $incomeRecord->user_id === $user->id;
    }
}
