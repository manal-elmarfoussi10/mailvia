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

            $contentHtml = $template->content_html ?? $template->content_text ?? '';
            $contentText = $template->content_text ?? strip_tags($contentHtml);
            
            $html = $this->replaceVariables($contentHtml);
            $text = $this->replaceVariables($contentText);

            // Send via Mail facade (configured via ENV-only logic)
            Mail::send([], [], function ($message) use ($sender, $html, $text) {
                 // Use correct sender fields
                $fromEmail = $sender->email ?? config('mail.from.address');
                $fromName = $sender->name ?? config('mail.from.name');

                $message->to($this->contact->email)
                    ->from($fromEmail, $fromName)
                    ->subject($this->automation->name) 
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
