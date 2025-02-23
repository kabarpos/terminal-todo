<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Support\Facades\Request;

trait HasActivities
{
    /**
     * Get all activities
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Log an activity
     */
    public function logActivity(
        string $type,
        string $description,
        array $properties = [],
        $subject = null
    ): Activity {
        $activity = new Activity([
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);

        if ($subject) {
            $activity->subject()->associate($subject);
        }

        $this->activities()->save($activity);
        return $activity;
    }

    /**
     * Log login activity
     */
    public function logLogin(): Activity
    {
        return $this->logActivity(
            'auth.login',
            ':name logged in',
            ['name' => $this->name]
        );
    }

    /**
     * Log logout activity
     */
    public function logLogout(): Activity
    {
        return $this->logActivity(
            'auth.logout',
            ':name logged out',
            ['name' => $this->name]
        );
    }

    /**
     * Log password change
     */
    public function logPasswordChange(): Activity
    {
        return $this->logActivity(
            'auth.password_change',
            ':name changed their password',
            ['name' => $this->name]
        );
    }

    /**
     * Log profile update
     */
    public function logProfileUpdate(array $changed): Activity
    {
        return $this->logActivity(
            'profile.update',
            ':name updated their profile',
            array_merge(['name' => $this->name], $changed)
        );
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10)
    {
        return $this->activities()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by type
     */
    public function getActivitiesByType(string $type)
    {
        return $this->activities()
            ->ofType($type)
            ->latest()
            ->get();
    }

    /**
     * Get activities between dates
     */
    public function getActivitiesBetweenDates($startDate, $endDate)
    {
        return $this->activities()
            ->betweenDates($startDate, $endDate)
            ->latest()
            ->get();
    }
} 