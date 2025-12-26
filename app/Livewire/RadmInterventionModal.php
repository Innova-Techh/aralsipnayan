<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RadmDetection;

class RadmInterventionModal extends Component
{
    public $show = false;
    public $detectionId;
    public $raiScore;
    public $timeFlag;
    public $accuracyFlag;
    public $bktFlag;
    public $avgResponseTime;
    public $accuracy;

    protected $listeners = ['showRadmIntervention' => 'showModal'];

    /**
     * Show the intervention modal with detection data
     */
    public function showModal($detectionId)
    {
        $detection = RadmDetection::find($detectionId);

        if (!$detection) {
            return;
        }

        $this->detectionId = $detectionId;
        $this->raiScore = $detection->rai_score;
        $this->timeFlag = $detection->time_flag;
        $this->accuracyFlag = $detection->accuracy_flag;
        $this->bktFlag = $detection->bkt_flag;
        $this->avgResponseTime = $detection->avg_response_time;
        $this->accuracy = $detection->accuracy * 100; // Convert to percentage
        $this->show = true;
    }

    /**
     * Close modal and acknowledge intervention
     */
    public function closeModal()
    {
        if ($this->detectionId) {
            $detection = RadmDetection::find($this->detectionId);
            if ($detection) {
                $detection->acknowledge();
            }
        }

        $this->reset(['show', 'detectionId', 'raiScore', 'timeFlag', 'accuracyFlag', 'bktFlag']);
        
        // Emit event to notify parent component
        $this->dispatch('radmInterventionAcknowledged');
    }

    /**
     * Get friendly messages based on flags
     */
    public function getMessages()
    {
        $messages = [];

        if ($this->timeFlag) {
            $messages[] = 'Your responses are being submitted very quickly';
        }

        if ($this->accuracyFlag) {
            $messages[] = 'Your accuracy is lower than expected';
        }

        if ($this->bktFlag) {
            $messages[] = 'Your performance pattern suggests you may not be fully engaged';
        }

        return $messages;
    }

    public function render()
    {
        return view('livewire.radm-intervention-modal');
    }
}
