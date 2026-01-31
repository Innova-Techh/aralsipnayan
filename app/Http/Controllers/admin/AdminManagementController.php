<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AdminManagementController extends Controller
{
    /**
     * Display the admin management page
     */
    public function index()
    {
        // Get all admins with their profiles
        $admins = User::where('role', 'Admin')
            ->with('adminProfile')
            ->orderByRaw("CASE 
                WHEN users.status = 'active' THEN 1 
                WHEN users.status = 'inactive' THEN 2 
                ELSE 3 END ASC")
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get statistics
        $totalAdmins = $admins->count();
        $activeAdmins = $admins->where('status', 'active')->count();
        $inactiveAdmins = $admins->where('status', 'inactive')->count();
        $archivedAdmins = $admins->where('status', 'archive')->count();

        return view('admin.admin.management.admin-management', compact(
            'admins',
            'totalAdmins',
            'activeAdmins',
            'inactiveAdmins',
            'archivedAdmins'
        ));
    }

    /**
     * Store a new admin
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'school_name' => 'nullable|string|max:150',
            'grade_level_focus' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Admin',
                'status' => 'active',
            ]);

            // Create admin profile
            AdminProfile::create([
                'user_id' => $user->id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'grade_level_focus' => $request->grade_level_focus ?? '6',
                'school_name' => $request->school_name ?? 'Pembo Elementary School',
            ]);

            DB::commit();

            // Log notification
            $this->logNotification('admin_management', 'Created new admin account', [
                'admin_name' => $request->firstname . ' ' . $request->lastname,
                'username' => $user->username,
                'email' => $user->email,
                'admin_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully!',
                'admin' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $request->firstname . ' ' . $request->lastname,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin data for editing
     */
    public function edit($adminId)
    {
        try {
            $user = User::with('adminProfile')->findOrFail($adminId);

            if ($user->role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 404);
            }

            $profile = $user->adminProfile;

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin profile not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'admin' => [
                    'id' => $user->id,
                    'firstname' => $profile->firstname,
                    'lastname' => $profile->lastname,
                    'username' => $user->username,
                    'email' => $user->email,
                    'status' => $user->status,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch admin data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin information
     */
    public function update(Request $request, $adminId)
    {
        try {
            $user = User::with('adminProfile')->findOrFail($adminId);

            if ($user->role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:100',
                'lastname' => 'required|string|max:100',
                'username' => 'required|string|max:50|unique:users,username,' . $user->id,
                'email' => 'required|email|max:100|unique:users,email,' . $user->id,
                'school_name' => 'nullable|string|max:150',
                'grade_level_focus' => 'nullable|string|max:10',
                'status' => 'required|in:active,inactive,archive',
                'password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Update user
                $userData = [
                    'username' => $request->username,
                    'email' => $request->email,
                    'status' => $request->status,
                ];

                // Update password if provided
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);

                // Update admin profile
                $user->adminProfile->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'school_name' => $request->school_name ?? 'Pembo Elementary School',
                    'grade_level_focus' => $request->grade_level_focus ?? '6',
                ]);

                DB::commit();

                // Log notification
                $this->logNotification('admin_management', 'Updated admin account', [
                    'admin_name' => $request->firstname . ' ' . $request->lastname,
                    'username' => $request->username,
                    'email' => $request->email,
                    'admin_id' => $user->id,
                    'status' => $request->status,
                    'password_changed' => $request->filled('password')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Admin updated successfully!',
                    'admin' => $user->load('adminProfile')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Failed to update admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive/Activate an admin
     */
    public function archive(Request $request, $adminId)
    {
        try {
            Log::info("Archive request received for admin.", [
                'admin_id' => $adminId,
                'requested_by' => auth()->user()->id ?? 'system'
            ]);

            $user = User::findOrFail($adminId);

            if ($user->role !== 'Admin') {
                Log::warning("Archive aborted: User is not an admin.", [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin.'
                ], 404);
            }

            // Prevent archiving self
            if ($user->id === auth('admin')->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot archive yourself.'
                ], 403);
            }

            Log::debug("Current status before toggle.", ['status' => $user->status]);

            // Toggle logic
            if ($user->status === 'active') {
                $newStatus = 'archive';
                $action = 'archived';
            } else {
                $newStatus = 'active';
                $action = 'activated';
            }

            Log::debug("New status determined.", [
                'old_status' => $user->status,
                'new_status' => $newStatus,
                'action' => $action
            ]);

            // Update user status
            $user->update(['status' => $newStatus]);
            
            Log::info("Admin status updated.", [
                'user_id' => $user->id,
                'new_status' => $newStatus
            ]);

            // Log notification
            $adminProfile = $user->adminProfile;
            $adminName = $adminProfile ? $adminProfile->firstname . ' ' . $adminProfile->lastname : $user->username;
            $this->logNotification('admin_management', "Admin account {$action}", [
                'admin_name' => $adminName,
                'username' => $user->username,
                'admin_id' => $user->id,
                'action' => $action,
                'new_status' => $newStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => "Admin {$action} successfully!",
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update admin status.", [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete an admin
     */
    public function destroy($adminId)
    {
        try {
            $user = User::findOrFail($adminId);

            if ($user->role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 404);
            }

            // Prevent deleting self
            if ($user->id === auth('admin')->user()?->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete yourself.'
                ], 403);
            }

            DB::beginTransaction();

            // Store admin info before deletion for notification
            $adminProfile = $user->adminProfile;
            $adminName = $adminProfile ? $adminProfile->firstname . ' ' . $adminProfile->lastname : $user->username;
            $adminUsername = $user->username;
            $adminEmail = $user->email;

            // Delete admin profile first (if exists)
            if ($user->adminProfile) {
                $user->adminProfile()->delete();
            }

            $user->delete();
            DB::commit();

            // Log notification after successful deletion
            $this->logNotification('admin_management', 'Deleted admin account', [
                'admin_name' => $adminName,
                'username' => $adminUsername,
                'email' => $adminEmail,
                'deleted_admin_id' => $adminId
            ]);

            Log::info("Admin deleted successfully.", [
                'admin_id' => $adminId,
                'deleted_by' => auth('admin')->user()?->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to delete admin.", [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log notification to admin_notifications table
     */
    private function logNotification($type, $action, $details = [])
    {
        try {
            $admin = Auth::guard('admin')->user();
            $adminProfile = $admin ? $admin->adminProfile : null;
            
            if ($adminProfile) {
                DB::table('admin_notifications')->insert([
                    'admin_id' => $adminProfile->id,
                    'type' => $type,
                    'action' => $action,
                    'details' => json_encode(array_merge($details, [
                        'timestamp' => now()->toDateTimeString(),
                        'admin_username' => $admin->username
                    ])),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log admin notification: ' . $e->getMessage());
        }
    }
}