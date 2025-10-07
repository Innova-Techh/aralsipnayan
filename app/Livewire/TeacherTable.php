<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeacherTable extends Component
{
    use WithPagination;

    public $search = '';
    public $gradeFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGradeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalTeachers = User::where('role', 'Teacher')->count();
        $activeTeachers = User::where('role', 'Teacher')->where('status', 'active')->count();
        $schools = 8;
        $sectionsManaged = DB::table('teacher_sections')->count();

        $teachers = User::query()
            ->where('role', 'Teacher')
            ->with(['teacherProfile'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->gradeFilter, function ($query) {
                $query->whereHas('teacherProfile', function ($profileQuery) {
                    $profileQuery->where('grade_level_focus', $this->gradeFilter);
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $teachers->getCollection()->transform(function ($teacher) {
            if ($teacher->teacherProfile) {
                $sections = DB::table('teacher_sections')
                    ->where('teacher_id', $teacher->teacherProfile->id)
                    ->pluck('section')
                    ->toArray();
                $teacher->sections = $sections;
            } else {
                $teacher->sections = [];
            }
            return $teacher;
        });

        return view('livewire.teacher-table', [
            'teachers' => $teachers,
            'totalTeachers' => $totalTeachers,
            'activeTeachers' => $activeTeachers,
            'schools' => $schools,
            'sectionsManaged' => $sectionsManaged,
        ]);
    }
}