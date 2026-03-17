<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendorId;
    public $type;
    public $data;
    public $customSubject;
    public $customContent;

    /**
     * Create a new message instance.
     *
     * @param int $vendorId
     * @param string $type
     * @param array $data
     * @param string|null $customSubject
     * @param string|null $customContent
     */
    public function __construct($vendorId, $type, $data, $customSubject = null, $customContent = null)
    {
        $this->vendorId = $vendorId;
        $this->type = $type;
        $this->data = $data;
        $this->customSubject = $customSubject;
        $this->customContent = $customContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \Illuminate\Support\Facades\Log::info("ShopMail Debug: Searching for template Type={$this->type}, VendorID={$this->vendorId}");

        $template = EmailTemplate::where('user_id', $this->vendorId)
            ->where('type', $this->type)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            \Illuminate\Support\Facades\Log::warning("ShopMail Warning: Template NOT FOUND for Type={$this->type}, VendorID={$this->vendorId}");
        }

        $subject = $this->customSubject ?: ($template ? $template->subject : str_replace('_', ' ', ucfirst($this->type)));
        $content = $this->customContent ?: ($template ? $template->content : 'Template for ' . $this->type . ' not found or inactive.');

        // Parse content and subject
        foreach ($this->data as $key => $value) {
            $valueStr = is_string($value) || is_numeric($value) ? (string)$value : '';
            $content = str_replace("{{ $key }}", $valueStr, $content);
            $subject = str_replace("{{ $key }}", $valueStr, $subject);
        }

        return $this->subject($subject)
            ->html($content);
    }
}
