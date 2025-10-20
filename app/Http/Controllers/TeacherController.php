<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index()
    {
        return view('admin.admin.management.teacher-management');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'sections' => ['nullable', 'array'],
        ]);

        // Split fullname into firstname and lastname
        $nameParts = explode(' ', $validated['fullname'], 2);
        $firstname = $nameParts[0];
        $lastname = $nameParts[1] ?? '';

        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'Teacher',
                'status' => 'active',
            ]);

            // Create teacher profile
            $teacherProfile = TeacherProfile::create([
                'user_id' => $user->id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'school_name' => 'AralSip School', // Default school name
            ]);

            // Create teacher sections if provided
            if (!empty($validated['sections'])) {
                foreach ($validated['sections'] as $section) {
                    DB::table('teacher_sections')->insert([
                        'teacher_id' => $teacherProfile->id,
                        'section' => $section,
                        'grade_level' => '6',
                        'school_year' => date('Y') . '-' . (date('Y') + 1),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.management.teachers')
                ->with('success', 'Teacher created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create teacher: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(User $user)
    {
        if ($user->role !== 'Teacher') {
            abort(404);
        }

        return view('admin.teacher.edit', compact('user'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'Teacher') {
            abort(404);
        }

        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'sections' => ['nullable', 'array'],
            'status' => ['required', 'in:active,archived'],
        ]);

        // Split fullname into firstname and lastname
        $nameParts = explode(' ', $validated['fullname'], 2);
        $firstname = $nameParts[0];
        $lastname = $nameParts[1] ?? '';

        DB::beginTransaction();
        try {
            $user->update([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'status' => $validated['status'],
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            $user->teacherProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                ]
            );

            // Update sections if provided
            if (isset($validated['sections'])) {
                $teacherProfile = $user->teacherProfile;
                
                // Delete existing sections
                DB::table('teacher_sections')
                    ->where('teacher_id', $teacherProfile->id)
                    ->delete();

                // Create new sections
                foreach ($validated['sections'] as $section) {
                    DB::table('teacher_sections')->insert([
                        'teacher_id' => $teacherProfile->id,
                        'section' => $section,
                        'grade_level' => '6',
                        'school_year' => date('Y') . '-' . (date('Y') + 1),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.management.teachers')
                ->with('success', 'Teacher updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update teacher: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'Teacher') {
            return redirect()->route('admin.management.teachers')
                ->with('error', 'Cannot delete this teacher.');
        }

        DB::beginTransaction();
        try {
            $teacherProfile = $user->teacherProfile;
            
            // Delete teacher sections
            if ($teacherProfile) {
                DB::table('teacher_sections')
                    ->where('teacher_id', $teacherProfile->id)
                    ->delete();
                
                $teacherProfile->delete();
            }
            
            $user->delete();

            DB::commit();

            return redirect()->route('admin.management.teachers')
                ->with('success', 'Teacher deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete teacher: ' . $e->getMessage()]);
        }
    }
}