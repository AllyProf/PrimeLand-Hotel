<?php

namespace App\Traits;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

trait ShiftManager
{
    /**
     * Get the current active shift for the authenticated staff
     */
    protected function getActiveShift()
    {
        return Shift::where('staff_id', Auth::guard('staff')->id())
            ->where('status', 'open')
            ->first();
    }

    /**
     * Check if the current user has an active shift
     */
    protected function hasActiveShift()
    {
        return $this->getActiveShift() !== null;
    }

    /**
     * Redirect to the open shift page if no shift is active
     */
    protected function requireShift()
    {
        if (!$this->hasActiveShift()) {
            $role = $this->getStaffRole();
            $route = 'reception.shift.open';
            
            if ($role === 'head_chef') {
                $route = 'chef-master.shift.open';
            } elseif ($role === 'bar_keeper') {
                $route = 'bar-keeper.shift.open';
            }
            
            return redirect()->route($route)->with('warning', 'You must open a shift before performing this action.')->send();
        }
    }

    /**
     * Get normalized staff role
     */
    protected function getStaffRole()
    {
        $user = Auth::guard('staff')->user();
        if (!$user) return null;
        return strtolower(trim($user->role));
    }
}
