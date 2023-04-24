<?php
/**
 * This file contains class that represents hl_attachments table columns
 * 
 */

namespace App\Keys\EmailInfo;

/**
 * This class contains constants that represents the hl_attachments table columns
 * 
 */
final class AttachmentKeys
{
    const ID = 'id';
    const ATTACHMENT_URL = 'attachment_url';
    const BATCH_NUMBER = 'batch_number';
    const ORIGINAL_NAME = 'original_name';
    const ATTACHMENT_NAME = 'attachment_name';
    const ATTACHMENT_SIZE = 'attachment_size';
    const ATTACHMENT_TYPE = 'attachment_type';
    const EMBEDDED = "embedded";
    const CONTENT_ID = "content_id";
    const ATTACHMENT_EXTENSION = 'attachment_extension';
    const UPLOADED_DATE = 'uploaded_date';
    const UPLOADED_DATE_GMT = 'uploaded_date_gmt';
    const DELETED = 'deleted';
    const FAILED = 'failed';
}

?>