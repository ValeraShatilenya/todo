<?php

namespace App\Jobs;

use App\Http\Controllers\PDFController;
use App\Http\Controllers\TaskInterface;
use App\Mail\SendPDF;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SendPDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user = null;
    public $controller = null;
    public $parameters = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, TaskInterface $controller, array $parameters = [])
    {
        $this->user = $user;
        $this->controller = $controller;
        $this->parameters = $parameters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pdf = PDFController::generateTasksPDF($this->controller->getNotCompletedData(...$this->parameters), $this->controller->getCompletedData(...$this->parameters), $this->user->id);

        $path = 'pdf/' . Str::uuid() . '.pdf';

        Storage::put($path, $pdf->output());

        Mail::to($this->user)->send(new SendPDF($path));

        Storage::delete($path);
    }
}
