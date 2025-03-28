<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Config;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
    
    protected function initCachePrefixAndTimes()
    {
        $this->cachePrefix = 'user_';
        $this->cacheTimes = [
            'single' => Config::get('cache-times.users.single', 3600),
            'collection' => Config::get('cache-times.users.collection', 3600),
            'by_field' => Config::get('cache-times.users.by_field', 3600),
        ];
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers(array $relations = [])
    {
        $duration = $this->cacheTimes['by_field'];
        $cacheKey = $this->cachePrefix . 'active';
        
        if (!empty($relations)) {
            $cacheKey .= '_' . md5(serialize($relations));
        }
        
        return \Cache::remember($cacheKey, $duration, function () use ($relations) {
            $query = $this->model->where('status', 'active');
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->get();
        });
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email, array $relations = [])
    {
        return $this->findByField('email', $email, $relations)->first();
    }
    
    /**
     * Get users by role
     */
    public function getByRole($roleName, array $relations = [])
    {
        $duration = $this->cacheTimes['by_field'];
        $cacheKey = $this->cachePrefix . 'role_' . $roleName;
        
        if (!empty($relations)) {
            $cacheKey .= '_' . md5(serialize($relations));
        }
        
        return \Cache::remember($cacheKey, $duration, function () use ($roleName, $relations) {
            $query = $this->model->role($roleName);
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->get();
        });
    }
    
    /**
     * Get users by team
     */
    public function getByTeam($teamId, array $relations = [])
    {
        $duration = $this->cacheTimes['by_field'];
        $cacheKey = $this->cachePrefix . 'team_' . $teamId;
        
        if (!empty($relations)) {
            $cacheKey .= '_' . md5(serialize($relations));
        }
        
        return \Cache::remember($cacheKey, $duration, function () use ($teamId, $relations) {
            $query = $this->model->whereHas('teams', function($q) use ($teamId) {
                $q->where('teams.id', $teamId);
            });
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->get();
        });
    }
    
    /**
     * Override parent method to handle additional cache clearing for users
     */
    public function clearCache($id = null)
    {
        parent::clearCache($id);
        
        if ($id) {
            $user = $this->model->find($id);
            
            if ($user) {
                // Clear by email cache
                \Cache::forget($this->cachePrefix . 'email_' . $user->email);
                
                // Clear other specialized caches
                \Cache::forget($this->cachePrefix . 'active');
                
                // Clear role caches for this user
                if (method_exists($user, 'getRoleNames')) {
                    foreach ($user->getRoleNames() as $roleName) {
                        \Cache::forget($this->cachePrefix . 'role_' . $roleName);
                    }
                }
                
                // Clear team caches for this user
                if ($user->teams && $user->teams->count() > 0) {
                    foreach ($user->teams as $team) {
                        \Cache::forget($this->cachePrefix . 'team_' . $team->id);
                    }
                }
            }
        }
    }
}