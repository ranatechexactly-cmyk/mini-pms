<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Task;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Determine if the user can view the project.
     */
    public function canView(User $user)
    {
        return $user->isAdmin() || 
               $user->isManager() || 
               $this->developers->contains($user) || 
               $this->manager_id === $user->id;
    }

    /**
     * Determine if the user can create projects.
     */
    public function canCreate(User $user)
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can update the project.
     */
    public function canUpdate(User $user)
    {
        return $user->isAdmin() || 
               ($user->isManager() && $this->manager_id === $user->id);
    }

    /**
     * Determine if the user can delete the project.
     */
    public function canDelete(User $user)
    {
        return $user->isAdmin();
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function developers()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
