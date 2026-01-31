<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        // Check if this is for the profile page or admin management page
        if (request()->is('admin/profile')) {
            return view('admin.admin.profile.admin-profile');
        }
        
        // Otherwise, show admin management page
        return view('admin.admin.management.admin-management');
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.admin.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Split fullname into firstname and lastname
        $nameParts = explode(' ', $validated['fullname'], 2);
        $firstname = $nameParts[0];
        $lastname = $nameParts[1] ?? '';

        // Create the user
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Admin',
            'status' => 'active',
        ]);

        // Create admin profile
        AdminProfile::create([
            'user_id' => $user->id,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ]);

        // Log notification
        $this->logNotification(
            'admin_created',
            'Created new admin account',
            [
                'admin_name' => $validated['fullname'],
                'username' => $validated['username'],
                'email' => $validated['email']
            ]
        );

        return redirect()->route('admin.management.admins')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $user)
    {
        if ($user->role !== 'Admin') {
            abort(404);
        }

        return view('admin.admin.edit', compact('user'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $user = null)
    {
        // If no user is passed, this is a profile update for the logged-in admin
        if (!$user) {
            $user = Auth::guard('admin')->user();
        }

        // Check if this is a profile update (no user parameter in route)
        if (request()->is('admin/profile')) {
            return $this->updateProfile($request);
        }

        // Otherwise, it's an admin management update
        if ($user->role !== 'Admin') {
            abort(404);
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'grade_level_focus' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->adminProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'school_name' => $validated['school_name'] ?? null,
                'grade_level_focus' => $validated['grade_level_focus'] ?? null,
            ]
        );

        // Log notification
        $this->logNotification(
            'admin_updated',
            'Updated admin account',
            [
                'admin_name' => $validated['firstname'] . ' ' . $validated['lastname'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password_changed' => !empty($validated['password'])
            ]
        );

        return redirect()->route('admin.management.admins')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Update the admin profile (for logged-in user).
     */
    protected function updateProfile(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'school_name' => ['nullable', 'string', 'max:255'],
            'grade_level_focus' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
        ]);

        // Split fullname into firstname and lastname
        $nameParts = explode(' ', $validated['fullname'], 2);
        $firstname = $nameParts[0];
        $lastname = $nameParts[1] ?? '';

        // Update user basic info
        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->adminProfile && $user->adminProfile->profile_photo) {
                Storage::disk('public')->delete($user->adminProfile->profile_photo);
            }

            // Store new photo
            $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            
            $user->adminProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'school_name' => $validated['school_name'] ?? null,
                    'grade_level_focus' => $validated['grade_level_focus'] ?? null,
                    'profile_photo' => $photoPath,
                ]
            );
        } else {
            // Update profile without changing photo
            $user->adminProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'school_name' => $validated['school_name'] ?? null,
                    'grade_level_focus' => $validated['grade_level_focus'] ?? null,
                ]
            );
        }

        // Log notification
        $this->logNotification(
            'profile_updated',
            'Updated profile information',
            [
                'admin_name' => $validated['fullname'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'photo_updated' => $request->hasFile('profile_photo')
            ]
        );

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the admin password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Current password is incorrect.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Log notification
        $this->logNotification(
            'password_changed',
            'Changed account password',
            [
                'admin_name' => $user->adminProfile ? $user->adminProfile->firstname . ' ' . $user->adminProfile->lastname : $user->username
            ]
        );

        return redirect()->route('admin.profile.index')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(Request $request, User $user = null)
    {
        // If no user is passed, this is account deletion for logged-in admin
        if (!$user) {
            return $this->deleteOwnAccount($request);
        }

        // Otherwise, it's deleting another admin
        if ($user->role !== 'Admin' || $user->id === auth()->id()) {
            return redirect()->route('admin.management.admins')
                ->with('error', 'Cannot delete this admin.');
        }

        $adminName = $user->adminProfile ? $user->adminProfile->firstname . ' ' . $user->adminProfile->lastname : $user->username;
        
        $user->adminProfile()->delete();
        $user->delete();

        // Log notification
        $this->logNotification(
            'admin_deleted',
            'Deleted admin account',
            [
                'deleted_admin_name' => $adminName,
                'deleted_admin_username' => $user->username,
                'deleted_admin_email' => $user->email
            ]
        );

        return redirect()->route('admin.management.admins')
            ->with('success', 'Admin deleted successfully.');
    }

    /**
     * Delete own account (for logged-in admin).
     */
    protected function deleteOwnAccount(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verify password
        if (!Hash::check($validated['password'], $user->password)) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Password is incorrect.');
        }

        // Prevent deleting yourself if you're the last admin
        $adminCount = User::where('role', 'Admin')->where('status', 'active')->count();
        if ($adminCount <= 1) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Cannot delete the last admin account.');
        }

        // Delete profile photo if exists
        if ($user->adminProfile && $user->adminProfile->profile_photo) {
            Storage::disk('public')->delete($user->adminProfile->profile_photo);
        }

        // Delete profile and user
        $user->adminProfile()->delete();
        $user->delete();

        // Logout
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Account deleted successfully.');
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
                \DB::table('admin_notifications')->insert([
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
            \Log::error('Failed to log admin notification: ' . $e->getMessage());
        }
    }

    /**
     * Get notifications for the authenticated admin
     */
    public function getNotifications()
    {
        try {
            $admin = Auth::guard('admin')->user();
            $adminProfile = $admin ? $admin->adminProfile : null;
            
            if (!$adminProfile) {
                return response()->json(['notifications' => []]);
            }

            $notifications = \DB::table('admin_notifications')
                ->where('admin_id', $adminProfile->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($notification) {
                    $details = json_decode($notification->details, true);
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'action' => $notification->action,
                        'details' => $details,
                        'is_read' => (bool)$notification->is_read,
                        'created_at' => $notification->created_at,
                        'time_ago' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans()
                    ];
                });

            $unreadCount = \DB::table('admin_notifications')
                ->where('admin_id', $adminProfile->id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get admin notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load notifications'], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $admin = Auth::guard('admin')->user();
            $adminProfile = $admin ? $admin->adminProfile : null;
            
            if (!$adminProfile) {
                return response()->json(['success' => false], 403);
            }

            \DB::table('admin_notifications')
                ->where('id', $id)
                ->where('admin_id', $adminProfile->id)
                ->update(['is_read' => true, 'updated_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Failed to mark admin notification as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark as read'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $admin = Auth::guard('admin')->user();
            $adminProfile = $admin ? $admin->adminProfile : null;
            
            if (!$adminProfile) {
                return response()->json(['success' => false], 403);
            }

            \DB::table('admin_notifications')
                ->where('admin_id', $adminProfile->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'updated_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Failed to mark all admin notifications as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark all as read'], 500);
        }
    }
}