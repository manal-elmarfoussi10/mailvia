<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAutomationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(
        protected Automation $automation,
        protected Contact $contact
    ) {
    }

    public function handle(): void
    {
        if (!$this->automation->is_active) {
            return;
        }

        try {
            $template = $this->automation->template;
            $sender = $this->automation->sender;

            $html = $this->replaceVariables($template->content_html);
            $text = $this->replaceVariables($template->content_text);

            // Send via Mail facade
            Mail::send([], [], function ($message) use ($sender, $html, $text) {
                $message->to($this->contact->email)
                    ->from($sender->from_email, $sender->from_name)
                    ->subject($this->automation->name) // Or a specific subject from settings
                    ->html($html)
                    ->plain($text);
                
                $message->getHeaders()->addTextHeader('X-Automation-Id', $this->automation->id);
                $message->getHeaders()->addTextHeader('X-Contact-Id', $this->contact->id);
            });

            Log::info("Automation '{$this->automation->name}' sent to {$this->contact->email}");

        } catch (\Exception $e) {
            Log::error("Failed to send automation email to {$this->contact->email}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function replaceVariables($content)
    {
        $vars = [
            '{{email}}' => $this->contact->email,
            '{{first_name}}' => $this->contact->first_name,
            '{{last_name}}' => $this->contact->last_name,
        ];

        return str_replace(array_keys($vars), array_values($vars), $content);
    }
}
