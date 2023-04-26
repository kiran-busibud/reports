<?php

namespace App\Data\CommunicationChannel\Sendgrid\Services;

use App\Data\Keys\EmailLogs\EmailLogsKeys;
use App\Data\Repositories\Customer\CustomerRepository;
use App\Data\Repositories\Ticket\TicketRepository;
use App\Data\Repositories\Message\MessageRepository;
use App\Data\CommunicationChannel\Sendgrid\EmailType;
use App\Data\CommunicationChannel\Sendgrid\Utilities\HeaderParserUtility;
use App\Data\Services\SendMessageService\SendMessageService;
use \Illuminate\Support\Facades\Log;
use App\Data\Services\CustomerService\CustomerService;
use App\Data\Services\TicketService;
use App\Data\Services\Brand\BrandService;
use App\Data\Keys\Attachment\AttachmentKeys;
use App\Data\Keys\Cache\CacheKeys;
use App\Data\Keys\Message\MessageMetaKeys;
use App\Data\Keys\Ticket\TicketKeys;
use App\Data\Services\Attachment\AttachmentService;
use App\Data\Repositories\Brand\BrandRepository;
use App\Data\Repositories\EmailLogs\EmailLogsRepository;
use App\Data\Services\EmailLogs\EmailLogsService;
use App\Data\Status\TicketStatus;
use App\Data\WebSocket\ChannelGenerator;
use App\Events\NewTicketCreatedFromMailEvent;
use App\Events\NewTicketMessageEvent;
use Illuminate\Support\Facades\Cache;

class ParseService
{

  private $customerRepository = null;
  private $messageService = null;
  private $ticketFinder = null;
  private $replyMailParser = null;
  private $ticketRepository = null;
  private $messageRepository = null;
  private $customerService = null;
  private $ticketService = null;
  private $ticketMailer = null;
  private $brandService = null;
  private $attachmentService = null;
  private $attachmentHandler = null;
  private $brandRepository = null;
  private $emailLogsRepository = null;
  private $emailLogsService = null;
  private $headerParserUtility = null;

  const IN_REPLY_TO = "In-Reply-To";
  const MESSAGE_ID = "message-id";
  const REFERENCES = "References";
  const REPLY_TO = "Reply-To";
  // const AUTO_TICKET_GENERATED_PREFIX = "Auto-Generated : ";
  const AUTO_TICKET_GENERATED_PREFIX = "";

  public function __construct()
  {
    $this->customerRepository = new CustomerRepository();
    $this->messageService = new SendMessageService();
    $this->ticketFinder = new TicketFinder();
    $this->replyMailParser = new ReplyMailParser();
    $this->ticketRepository = new TicketRepository();
    $this->messageRepository = new MessageRepository();
    $this->customerService = new CustomerService();
    $this->ticketService = new TicketService();
    $this->ticketMailer = new TicketMailer();
    $this->brandService = new BrandService();
    $this->attachmentService = new AttachmentService();
    $this->attachmentHandler = new AttachmentHandler();
    $this->brandRepository = new BrandRepository();
    $this->emailLogsRepository = new EmailLogsRepository();
    $this->emailLogsService = new EmailLogsService();
    $this->headerParserUtility = new HeaderParserUtility();
  }

  public function parseInboundWebhook($data, $attachments)
  {

    Log::debug("ParseService : parseInboundWebhook() - Webhook got hit");

    Log::debug("Heyyyy : " . print_r($data, true));

    $this->headerParserUtility->loadHeaders($data["headers"]);

    if (!isset($data['html'])) {

      $data['html'] = "";
    }

    if (!isset($data['text'])) {

      $data['text'] = "";
    }

    if (!isset($data['subject']) || empty($data['subject'])) {

      $data['subject'] = "No Subject";
    }

    $emailLogId = $this->emailLogsRepository->create([EmailLogsKeys::DATA => json_encode($data)]);

    $toEmails = $this->emailFormatter(explode(",", $data['to']));

    $ccEmails = [];

    if (isset($data['cc'])) { 

      $ccEmails = $this->emailFormatter(explode(",", $data['cc']));
    }

    $brandEntity = $this->getBrand($toEmails, $ccEmails);

    Log::debug("ParseService : parseInboundWebhook() - Check If email is a reply to some email or not");

    if ($this->isRepliedEmail($data)) {
      //The email received is a replied mail
      Log::debug("ParseService : parseInboundWebhook() - Email is reply to some email");

      Log::debug("ParseService : parseInboundWebhook() - Check validity of the email");

      $validated = $this->validateReplyEmail($data);

      if (!$validated) {
        Log::debug("ParseService : parseInboundWebhook() - Email is not valid, so create new ticket");

        $this->createNewTicket($data, $attachments, $emailLogId, $toEmails, $ccEmails, $brandEntity);

        //email is not valid
        return;
      }

      Log::debug("ParseService : parseInboundWebhook() - Email is valid");

      //email is valid, store into database

      $repliedToMessageId = $this->headerParser(ParseService::IN_REPLY_TO, $data['headers']);

      $emailMessageId = $this->headerParser(ParseService::MESSAGE_ID, $data['headers']);

      $ticketId = $validated['ticketId'];

      $replyToEmail = $this->headerParserUtility->getHeaderValue(ParseService::REPLY_TO);

      if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
        $from = $this->extractNameAndEmail($replyToEmail)['email'];
      } else {
        $from = $this->extractNameAndEmail($data['from'])['email'];
      }


      $ticket = $this->ticketRepository->getById([$ticketId])[0];

      $emailMessageId = $this->removeAngleBrackets($emailMessageId);

      $truncatedMessage = $this->replyMailParser->parse($data['html'], $data['text'], $emailMessageId);

      $storedMessageId = $this->messageService->receiveMail(
        [
          'customerId' => $ticket->customerId,
          'message' => !empty($truncatedMessage['html']) ? $truncatedMessage['html'] : $truncatedMessage['text'], //nl2br($truncatedMessage['text']),
          'ticketId' => $ticketId,
          'emailMessageId' => $emailMessageId,
          'from' => $from,
          'to' => $brandEntity->brandEmail
        ]
      );

      // if(empty($storedMessageId)){
      //   Log::debug("ParseService : parseInboundWebhook() - Message not added to database");
      //   return;
      // }

      $uploadedAttachment = $this->attachmentService->addAttachment([AttachmentKeys::BATCH_NUMBER => null], $attachments, $data['attachment-info'] ?? null, true, !empty($truncatedMessage['html']) ? $truncatedMessage['html'] : $truncatedMessage['text']);

      Log::debug("CHECK : " . print_r($uploadedAttachment, true));

      if (!empty($uploadedAttachment)) {

        Log::debug("CHECK : Storing the batch number");

        $this->messageRepository->update($storedMessageId['id'], ['messageAttachmentBatchNumber' => $uploadedAttachment[0]['batchNumber']], true);
      }
      $this->emailLogsRepository->markProcessed($emailLogId);
      // $this->emailLogsService->processRawData($emailLogId);

      $this->ticketRepository->ticketModified([$ticketId]);

      if(isset($storedMessageId['id'])){

        //dispatching new ticket message event
        NewTicketMessageEvent::dispatch($storedMessageId['id'], ChannelGenerator::getGlobalAgentsChannel());
  
      }

    } else {

      Log::debug("ParseService : parseInboundWebhook() - Email is not an reply to other email");

      $this->createNewTicket($data, $attachments, $emailLogId, $toEmails, $ccEmails, $brandEntity);

      NewTicketCreatedFromMailEvent::dispatch($data["subject"] ?? "");

    }

    // Log::debug($storedAttachments);

    // $payload = [
    //   'attachments' => json_encode($storedAttachments)
    // ];
    // $this->messageRepository->update($storedMessageId['id'], $payload, true);
  }

  private function createNewTicket($data, $attachments, $emailLogId, $toEmails, $ccEmails, $brandEntity)
  {

    $replyToEmail = $this->headerParserUtility->getHeaderValue(ParseService::REPLY_TO);

    if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
      $from = $this->extractNameAndEmail($replyToEmail);
    } else {
      $from = $this->extractNameAndEmail($data['from']);
    }

    $to = $this->extractNameAndEmail($data['to']);

    $customer = $this->customerRepository->getByEmail($from['email']);
    Log::debug("ParseService : parseInboundWebhook() - Check if customer exists");

    if (!$customer) {
      Log::debug("ParseService : parseInboundWebhook() - Customer not found, creating new one");

      //create new customer
      if (empty($from['name'])) {
        $from['name'] = "Auto-Generated Name";
      }
      $customerData = $this->customerService->addNewCustomer($from);

      if ($customerData['err']) {
        return;
      }

      $customerId = $customerData['data']['id'];
      $this->customerRepository->update($customerId, ['autogenerated' => 1], true);
      $customer = $this->customerRepository->getById($customerId);
    }



    $emailMessageId = $this->headerParser(ParseService::MESSAGE_ID, $data['headers']);
    $emailMessageId = $this->removeAngleBrackets($emailMessageId);

    // dd("Hello",$emailMessageId);

    $truncatedMessage = $this->replyMailParser->parse($data['html'], $data['text'], $emailMessageId);

    $ticketTitle = ParseService::AUTO_TICKET_GENERATED_PREFIX . $data['subject'];

    Log::debug("ParseService : parseInboundWebhook() - Creating auto generated ticket");

    //create a new ticket
    $ticketId = $this->ticketService->addTicket([
      "to" => $from['email'],
      "ticket_title" => $ticketTitle,
      "from" => $brandEntity->brandEmail, //$to['email'],
      "ticket_description" => !empty($truncatedMessage['html']) ? $truncatedMessage['html'] : $truncatedMessage['text'], //nl2br($truncatedMessage['text'])
      "filteredDescription" => $truncatedMessage['text'],
      TicketKeys::TICKET_TYPE => TicketStatus::AUTO_GENERATED_TICKET_TYPE
    ], true);

    $this->ticketRepository->update($ticketId, [
      'messageId' => $emailMessageId,
      'autogenerated' => 1
    ], true);


    $to = ['email' => $brandEntity->brandEmail, 'name' => $brandEntity->brandName];

    $this->emailLogsRepository->markProcessed($emailLogId);
    // $this->emailLogsService->processRawData($emailLogId);

    // $this->ticketMailer->sendTicketMail(
    //   EmailType::TICKET_AUTO_GENERATED,
    //   $ticketId,
    //   $to,
    //   $from['email'],
    //   null,
    //   null,
    //   true
    // );

    $uploadedAttachment = $this->attachmentService->addAttachment([AttachmentKeys::BATCH_NUMBER => null], $attachments, $data['attachment-info'] ?? null, true, !empty($truncatedMessage['html']) ? $truncatedMessage['html'] : $truncatedMessage['text']);

    if (!empty($uploadedAttachment)) {

      $messageEntity = $this->messageRepository->getTicketDescription($ticketId);
      if (!empty($messageEntity)) {

        $messageEntity = $messageEntity[0];
        $status = $this->messageRepository->update($messageEntity->id, [MessageMetaKeys::META_MESSAGE_ATTACHMENT_BATCH_NUMBER => $uploadedAttachment[0]['batchNumber']], true);

        if (!$status) {
          Log::debug("ParserService - failed to update attachmentBatchnumber");
        }
      }

      $this->ticketRepository->update($ticketId, ['attachmentBatchNumber' => $uploadedAttachment[0]['batchNumber']], true);
    }
  }


  /**
   * This method selects a brand for the incoming email
   * 
   * @param array $toEmails
   * @param array $ccEmails
   * 
   * @return BrandEntity
   * 
   */
  public function getBrand($toEmails, $ccEmails)
  {

    $emailToCheck = [];

    foreach ($toEmails as $toEmail) {

      $emailToCheck[] = $toEmail['email'];
    }

    foreach ($ccEmails as $ccEmail) {

      $emailToCheck[] = $ccEmail['email'];
    }


    $brandEmails = Cache::rememberForever(CacheKeys::BRAND_CACHE, function () {

      return $this->brandRepository->getAllBrandEmails();
    });


    foreach ($emailToCheck as $email) {


      if (in_array($email, $brandEmails['emails'])) {

        return $this->brandRepository->getBrandByEmail($email);
      }
    }

    return $this->brandRepository->getDefaultBrand();
  }

  /**
   * This method formats the name email strings to array
   * 
   * @param array $nameEmailStrings
   * 
   * @return array
   * 
   */
  private function emailFormatter($nameEmailStrings)
  {

    $formatted = [];

    foreach ($nameEmailStrings as $nameEmailString) {

      $formatted[] = $this->extractNameAndEmail($nameEmailString);
    }

    return $formatted;
  }

  /**
   * This method is used to remove surrounding angle brackets
   * from a string
   * 
   * @param $string string
   * 
   * @return string
   * 
   */
  private function removeAngleBrackets($string)
  {
    return trim(str_replace(array('<', '>'), '', $string));
  }

  /**
   * This method is used to parse particular header value from the string
   * 
   * @param $key string
   * @param $string string
   * 
   * @return string
   * 
   */
  private function headerParser($key, $string)
  {
    //regex pattern to filter the string
    $pattern = "/$key: (<.*>)/i";

    $matches = [];

    //function matches the regex patterns and stores the matched values in matches array
    preg_match($pattern, $string, $matches);

    if (empty($matches)) {
      //no match was found in the string, so return false
      return false;
    }

    //found the match, so returning them
    return $matches[1];
  }

  /**
   * This method is used to check if the string has email only or it have name and email concatenated
   * like 'Maninder <maninder@zinosi.com' (both name and email)
   * or 'maninder@zinosi.com' (only the email address)
   * 
   * @param $str string
   * 
   * @return bool if only email is present return true else return false 
   * 
   */
  private function checkStringHasEmailOnly($str)
  {
    //regex pattern
    $pattern = "/<[^>]*>/i";

    if (!preg_match($pattern, $str)) {
      //string has only email, so return true
      return true;
    }

    //string has name and email concatenated so return false
    return false;
  }

  /**
   * This method is used to check the presence of In-Reply-To header in the email
   *
   * @param $data array
   * 
   * @return bool
   *
   */
  private function isRepliedEmail($data)
  {

    //get the value of IN_REPLY_TO header
    $repliedHeader = $this->headerParserUtility->getHeaderValue(ParseService::IN_REPLY_TO);

    if ($repliedHeader) {
      //string is returned, so it is a replied email, return true
      return true;
    }

    //no string returned, so it is not a replied email, return false
    return false;
  }

  /**
   * This method is used to check if the email which webhook received is already
   * processed by our system or not
   * 
   * if it is already processed, then it will return true
   * 
   * @param $data array
   * 
   * @return bool
   * 
   */
  private function checkEmailAlreadyHandled($data)
  {
    //get the message id from the email header
    $emailMessageId = $this->headerParser(ParseService::MESSAGE_ID, $data['headers']);

    //remove the angle brackets <> from the message id
    $emailMessageId = $this->removeAngleBrackets($emailMessageId);

    //check if any message or ticket with this email message id exists in our system
    $message = $this->ticketFinder->find($emailMessageId);

    if ($message) {
      //found a message or ticket with the emailmessageid in our system
      //so return true
      return true;
    }

    //no message or ticket with emailMessageId found in our system, so return false1
    return false;
  }

  /**
   * This method is used to validate the email, which our system detected as a reply
   *
   * The validatiy of email is checked on the following key points -
   * 1. The IN_REPLY_TO header value have any associated message or ticket in our system.
   * 2. The FROM email address should match with 'to' or 'cc' or 'bcc' of the parent email in our system (Unchecked this validation
   * due to some bugs, This validation is not in use for now)
   * 3. Ticket Id should be present in the Email Subject
   * 4. Ticket id in email subject must match with the ticket id of the associated ticket found using IN_REPLY_TO header value in our system
   *  
   * @param $data array
   * 
   * @return array
   * 
   */
  private function validateReplyEmail($data)
  {

    Log::debug("ParseService : validateReplyEmail() - Inside validateReplyEmail method");

    //get the value of IN_REPLY_TO header
    $parentMessageId = $this->headerParser(ParseService::IN_REPLY_TO, $data['headers']);

    // Remove the angle brackets <> from the string returned
    $parentMessageId = $this->removeAngleBrackets($parentMessageId);

    //check for any message or ticket with the parentMessageId
    $ticketId = $this->ticketFinder->find($parentMessageId);

    Log::debug("ParseService : validateReplyEmail() - Check if email message have any parent in our system");

    if (!$ticketId) {

      //found no ticket or message with the particular parentMessageId, but, this might be due to an email which
      //is not delivered to our system, so check the references header array for any parent in our system

      Log::debug("ParseService : validateReplyEmail() - Email IN-REPLY-TO header not matched with any parent");
      Log::debug("ParseService : validateReplyEmail() - Checking REFERENCES to find any parent");

      //get the references value from the header
      $references = $this->headerParser(ParseService::REFERENCES, $data['headers']);

      //convert the references string to array
      $references = explode(" ", trim($references));

      //iterate over all the refernces to find any match with our system
      //iterating from end in order to get the latest message or ticket for the reference
      for ($i = count($references) - 1; $i >= 0; $i--) {

        $reference = $references[$i];

        //remove the angle brackets <> from the reference
        $reference = $this->removeAngleBrackets($reference);

        //check for ticket or message with the reference
        $ticketId = $this->ticketFinder->find($reference);

        if ($ticketId) {
          //reference matched with message or ticket in our system
          //assign the refernce as the parentMessageId
          $parentMessageId = $reference;
          //found the match, so no need to check the further iterations, so break
          Log::debug("ParseService : validateReplyEmail() - Found a parent in our system");
          break;
        }
      }

      if (!$ticketId) {
        //no reference matched with any message or ticket
        Log::debug("ParseService : validateReplyEmail() - No parent found, so return");
        //Our first validation check has failed, so return false to indicate failed validation
        return false;
      }
    }

    Log::debug("ParseService : validateReplyEmail() - Parent email found for received email");

    $replyToEmail = $this->headerParserUtility->getHeaderValue(ParseService::REPLY_TO);

    if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
      $from = $this->extractNameAndEmail($replyToEmail)['email'];
    } else {
      $from = $this->extractNameAndEmail($data['from'])['email'];
    }


    //check if $from email match with to or ccs or bccs

    // $toCheckEmails = $this->getAllEmailsToCheck($parentMessageId);
    // // $toCheckEmails = ['somerandom@email.com'];

    // Log::debug("ParseService : validateReplyEmail() - toCheckEmails : ".json_encode($toCheckEmails));

    // Log::debug("ParseService : validateReplyEmail() - Check if email from address is in our system");

    // if(!in_array($from, $toCheckEmails)){
    //   Log::debug("ParseService : validateReplyEmail() - From email address is not in our system, so return");
    //   return false;
    // }

    // Log::debug("ParseService : validateReplyEmail() - From email found in our system");

    $ticket = $this->ticketRepository->getById([$ticketId])[0];


    //extract ticketID from subject
    Log::debug("ParseService : validateReplyEmail() - Ticket is not autogenerated, so checking the email subject");

    //get the ticket id from the subject
    $extractedTicketId = $this->extractTicketId($data['subject']);

    if (!$extractedTicketId) {
      //there is not ticketid found in the subject of the email
      Log::debug("ParseService : validateReplyEmail() - There is no ticketId in subject of email, so return");
      //our third validation check has failed, so return false to indicate failed ticket validation
      return false;
    }

    Log::debug("ParseService : validateReplyEmail() - Ticket id found in the subject of email");

    if ($extractedTicketId != $ticketId) {
      //ticketid found in ticket subject did not matched with ticketid from our system
      Log::debug("ParseService : validateReplyEmail() - TicketId in subject does not matched with the ticket id of parent message, so return");
      //validation has failed, so return false
      return false;
    }

    Log::debug("ParseService : validateReplyEmail() - Email is legit");

    //email is legit because it have passed all the validation checks
    return ['success' => true, 'ticketId' => $ticketId];
  }

  /**
   * This method used to extract the ticket id from the email subject
   * 
   * @param $subject string
   * 
   * @return mixed
   * 
   */
  private function extractTicketId($subject)
  {
    $matches = [];

    //store the regex matches in matches array
    preg_match("/Request # \d*/", $subject, $matches);

    if ($matches) {
      //match found, so return the match
      $matches = explode('#', $matches[0]);
      return $matches[1];
    } else {
      //not found anything, so return false
      return false;
    }
  }

  /**
   * This method removes all the special characters from the string
   * 
   * @param string $str
   * 
   * @return string 
   * 
   */
  function removeSpecialChar($str)
  {
    $res = preg_replace('/[\!\@\#\$\%\^\&\*\(\)\-\_\+\=\;\{\}\[\]\:\'\<\>\,\.\?\/\|\\\""]+/', '', $str);
    return $res;
  }

  /**
   * This method is used to split name and email from this kind of string 'Maninder <maninder@zinosi.com>'
   * 
   * @param $string string
   * 
   * @return array ['name' => '--name--', 'email' => '--email--']
   * 
   */
  public function extractNameAndEmail($string)
  {
    $name = "";
    $email = $string;

    //check if string has only email
    if (!$this->checkStringHasEmailOnly($string)) {
      //string have name and email so split them
      $splitted = preg_split('/\s*<([^>]*)>/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

      if (count($splitted) == 1) {
        $name = "";
        $email = $splitted[0];
      } else {
        $name = $splitted[0];
        $email = $splitted[1];
      }

    }

    Log::debug("ParseService : CHECKING - name - $name --- email - $email");

    if (!empty($name)) {
      $name = $this->removeSpecialChar(explode("@", $name)[0]);
    }

    // $name = "";

    return [
      "name" => $name,
      "email" => strtolower($email)
    ];
  }
}