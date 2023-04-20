<?php

/**
 * This file contains the column keys for hl_attachment_meta table
 * 
 * @author Yuvaraj N <yuvaraj@zinosi.com>
 */

namespace App\Keys\EmailInfo;

final class AttachmentMetaKeys{

    const META_ID = 'meta_id';
    const ATTACHMENT_ID = 'attachment_id';
    const META_KEY = 'meta_key';
    const META_VALUE = 'meta_value';

    //if direct cdn link
    const IS_DIRECT_LINK = 'isDirectLink';
    const IS_PUBLIC_ATTACHMENT = 'isPublicAttachment';

}