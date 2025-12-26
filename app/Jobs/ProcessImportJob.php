<?php

namespace App\Jobs;

use App\Models\Import;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function handle(): void
    {
        try {
            $import = $this->import;
            $company = $import->company;
            $mapping = $import->mapping;
            $listId = $import->contact_list_id;

            // Read the file
            $data = Excel::toArray([], $import->file_path);
            
            if (empty($data) || empty($data[0])) {
                $import->update([
                    'status' => 'failed',
                    'errors' => ['message' => 'File is empty or unreadable.'],
                ]);
                return;
            }

            $rows = $data[0]; // First sheet
            $headers = array_shift($rows); // Remove header row
            
            $import->update(['total_rows' => count($rows)]);

            $processedCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    // Map row data to contact fields
                    $contactData = [];
                    
                    foreach ($mapping as $field => $columnName) {
                        if (empty($columnName)) continue;
                        
                        $columnIndex = array_search($columnName, $headers);
                        if ($columnIndex !== false && isset($row[$columnIndex])) {
                            $contactData[$field] = $row[$columnIndex];
                        }
                    }

                    // Validate email is present
                    if (empty($contactData['email'])) {
                        $errorCount++;
                        $errors[] = "Row " . ($index + 2) . ": Missing email";
                        continue;
                    }

                    // Check for duplicates within company
                    $existing = $company->contacts()->where('email', $contactData['email'])->first();
                    
                    if ($existing) {
                        // Update existing contact
                        $existing->update([
                            'first_name' => $contactData['first_name'] ?? $existing->first_name,
                            'last_name' => $contactData['last_name'] ?? $existing->last_name,
                        ]);
                        
                        // Merge tags if provided
                        if (!empty($contactData['tags'])) {
                            $newTags = array_map('trim', explode(',', $contactData['tags']));
                            $existingTags = $existing->tags ?? [];
                            $existing->update(['tags' => array_unique(array_merge($existingTags, $newTags))]);
                        }
                        
                        // Attach to list if specified
                        if ($listId) {
                            $existing->lists()->syncWithoutDetaching([$listId]);
                        }
                    } else {
                        // Create new contact
                        $contactData['status'] = $contactData['status'] ?? 'subscribed';
                        
                        // Process tags
                        if (isset($contactData['tags']) && is_string($contactData['tags'])) {
                            $contactData['tags'] = array_map('trim', explode(',', $contactData['tags']));
                        }
                        
                        $contact = $company->contacts()->create($contactData);
                        
                        // Attach to list if specified
                        if ($listId) {
                            $contact->lists()->attach($listId);
                        }
                    }

                    $processedCount++;
                    
                    // Update progress every 50 rows
                    if ($processedCount % 50 === 0) {
                        $import->update([
                            'processed_rows' => $processedCount,
                            'error_rows' => $errorCount,
                        ]);
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            // Final update
            $import->update([
                'status' => 'completed',
                'processed_rows' => $processedCount,
                'error_rows' => $errorCount,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            $this->import->update([
                'status' => 'failed',
                'errors' => ['message' => $e->getMessage()],
            ]);
        }
    }
}
