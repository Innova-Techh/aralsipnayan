<?php
namespace App\ViewModels;

class LessonViewModel
{
    public $id, $title, $description, $category, $duration, $questions_count,
           $difficulty_level, $order, $is_active;

    protected $progressStatus;
    protected $progressPercentage;

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
        }

        $this->progressStatus = $data['progressStatus'] ?? 'available';
        $this->progressPercentage = $data['progressPercentage'] ?? 0;
    }

    public function getProgressStatus()
    {
        return $this->progressStatus;
    }

    public function getProgressPercentage()
    {
        return $this->progressPercentage;
    }
}