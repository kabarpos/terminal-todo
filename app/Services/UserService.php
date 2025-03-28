<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    protected $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        parent::__construct($userRepository);
    }
    
    /**
     * Get all users
     */
    public function getAllUsers(array $relations = [])
    {
        return $this->repository->all($relations);
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers(array $relations = [])
    {
        return $this->userRepository->getActiveUsers($relations);
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole($roleName, array $relations = [])
    {
        return $this->userRepository->getByRole($roleName, $relations);
    }
    
    /**
     * Get users by team
     */
    public function getUsersByTeam($teamId, array $relations = [])
    {
        return $this->userRepository->getByTeam($teamId, $relations);
    }
    
    /**
     * Find user by ID
     */
    public function getUserById($id, array $relations = [])
    {
        return $this->repository->find($id, $relations);
    }
    
    /**
     * Find user by email
     */
    public function getUserByEmail($email, array $relations = [])
    {
        return $this->userRepository->getByEmail($email, $relations);
    }
    
    /**
     * Create a new user
     */
    public function createUser(array $data)
    {
        // Set default values
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        try {
            $user = $this->create($data);
            
            // Assign roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
            
            // Assign teams if provided
            if (isset($data['teams']) && is_array($data['teams'])) {
                $teamData = [];
                foreach ($data['teams'] as $teamId => $role) {
                    $teamData[$teamId] = ['role' => $role];
                }
                $user->teams()->sync($teamData);
            }
            
            return $user;
        } catch (Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update an existing user
     */
    public function updateUser($id, array $data)
    {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        try {
            $user = $this->update($id, $data);
            
            if ($user) {
                // Sync roles if provided
                if (isset($data['roles']) && is_array($data['roles'])) {
                    $user->syncRoles($data['roles']);
                }
                
                // Sync teams if provided
                if (isset($data['teams']) && is_array($data['teams'])) {
                    $teamData = [];
                    foreach ($data['teams'] as $teamId => $role) {
                        $teamData[$teamId] = ['role' => $role];
                    }
                    $user->teams()->sync($teamData);
                }
            }
            
            return $user;
        } catch (Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        try {
            return $this->delete($id);
        } catch (Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update user status
     */
    public function updateUserStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Approve user registration
     */
    public function approveUser($id)
    {
        return $this->updateUserStatus($id, 'active');
    }
    
    /**
     * Reject user registration
     */
    public function rejectUser($id)
    {
        return $this->updateUserStatus($id, 'rejected');
    }
    
    /**
     * Deactivate user
     */
    public function deactivateUser($id)
    {
        return $this->updateUserStatus($id, 'inactive');
    }
    
    /**
     * Ban user
     */
    public function banUser($id)
    {
        return $this->updateUserStatus($id, 'banned');
    }
}