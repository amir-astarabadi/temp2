<?php

namespace App\Notifications;

use App\Services\Dataset\DatasetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DatasetStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private ?DatasetService $datasetService = null;

    public function __construct(private int $datasetId)
    {
        $this->datasetService = new DatasetService();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }
    
    public function toMail(object $notifiable): ?MailMessage
    {
        $dataset = $this->datasetService->findBy($this->datasetId);
        if(is_null($dataset)){
            return null;
        }

        return (new MailMessage)
            ->view('dataset.update_status', [
                'dataset_name' => $dataset->name, 
                'project_name' => $dataset->project->name,
                'status' => $dataset->status,
            ]);
    }
}
