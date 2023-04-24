<?php

namespace App\Http\Controllers\EmailInfo;

use App\Repositories\EmailInfo\IAttachmentRepository;
use App\Services\EmailInfo\AttachmentService;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log;

class AttachmentController
{
    protected $attachmentRepository;
    function __construct(IAttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    function postAttachmentInfo(Request $request)
    {
        $requestBody = $request->all();

        
    }
}