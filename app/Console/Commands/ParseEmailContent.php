<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZBateson\MailMimeParser\MailMimeParser;
use App\Traits\HtmlTextConverterTrait;

class ParseEmailContent extends Command
{
    use HtmlTextConverterTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse raw email content and extract plain text body';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting email parsing process...');
        
        // Get unprocessed emails (where raw_text is empty)
        $emails = DB::table('successful_emails')
            ->whereRaw('TRIM(raw_text) = ""')
            ->orWhereNull('raw_text')
            ->get();
            
        $this->info("Found {$emails->count()} emails to process");
        
        $parser = new MailMimeParser();
        $count = 0;
        
        foreach ($emails as $email) {
            try {
                // Parse the email
                $message = $parser->parse($email->email, false);
                
                // Get plain text content
                $plainText = $message->getTextContent();
                
                // If no plain text content is found, try to extract from HTML
                if (empty($plainText) && $message->getHtmlContent()) {
                    $plainText = $this->convertHtmlToText($message->getHtmlContent());
                }
                
                // Clean the text (remove non-printable characters except line breaks)
                $cleanText = $this->cleanText($plainText);
                
                // Update the database
                DB::table('successful_emails')
                    ->where('id', $email->id)
                    ->update(['raw_text' => $cleanText]);
                
                $count++;
                
                if ($count % 100 === 0) {
                    $this->info("Processed {$count} emails so far");
                }
            } catch (\Exception $e) {
                $this->error("Error processing email ID {$email->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Email parsing completed. Processed {$count} emails.");
    }
}
