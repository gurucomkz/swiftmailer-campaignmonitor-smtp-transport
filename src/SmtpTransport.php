<?php


namespace Gurucomkz\campaignmonitor;

use CS_REST_Transactional_ClassicEmail;
use SilverStripe\Core\Config\Config;
use Swift_Transport;
use Swift_Mime_Message;
use Swift_Events_EventListener;
use Swift_TransportException;
use SilverStripe\Control\Email\Email;


class SmtpTransport implements Swift_Transport
{

    private $_clientID = null;
    private $_apiKey = null;

    /** Connection status */
    protected $_started = true;

    /**
     * Test if this Transport mechanism has started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->_started;
    }

    /**
     * Start this Transport mechanism.
     */
    public function start()
    {
    }

    /**
     * Stop this Transport mechanism.
     */
    public function stop()
    {
    }

    public function setApiKey($value)
    {
        $this->_apiKey = $value;
    }

    public function getApiKey()
    {
        return $this->_apiKey;
    }

    public function setClientID($value)
    {
        $this->_clientID = $value;
    }

    public function getClientID()
    {
        return $this->_clientID;
    }


    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Swift_Mime_Message $message
     * @param string[]  $failedRecipients An array of failures by-reference
     *
     * @throws Swift_TransportException
     * @return int sent messages count
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if (null === $message->getHeaders()->get('To')) {
            throw new Swift_TransportException(
                'Cannot send message without a recipient'
            );
        }

        $auth = [
            'api_key' => $this->getApiKey(),
        ];
        $client_id = $this->getClientID();

        $mgClient = new CS_REST_Transactional_ClassicEmail($auth, $client_id);
        $result = $mgClient->send( $this->getPostData($message), null, 'No' );
        $success = $result->http_status_code > 200 && $result->http_status_code < 299;

        return !!$success;
    }

    /**
     * Register a plugin in the Transport.
     *
     * @param \Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
    }

    /**
     * Looks at the message headers to find post data.
     *
     * @param Swift_Mime_Message $message
     *
     * @return array $postData
     */
    const HEADER_MAP = [
        'subject' => 'Subject',
        'from' => 'From',
        'to' => 'To',
        'cc' => 'CC',
        'bcc' => 'BCC',
        'reply-to' => 'ReplyTo',
    ];

    protected function getPostData(Swift_Mime_Message $message)
    {
        // get "form", "to" etc..
        $postData = [
            'Html' => $message->getBody(),
        ];
        $messageHeaders = $message->getHeaders();

        foreach (self::HEADER_MAP as $swiftHeaderName => $csHeaderName) {
            /** @var \Swift_Mime_Headers_MailboxHeader $value */
            if (null !== $value = $messageHeaders->get($swiftHeaderName)) {
                $postData[$csHeaderName] = $value->getFieldBody();
                $messageHeaders->removeAll($csHeaderName);
            }
        }

        $send_all_emails_to = Config::forClass(Email::class)->get('send_all_emails_to');
        if($send_all_emails_to && !empty($send_all_emails_to)) {
            $postData['To'] = $send_all_emails_to;
            if(isset($postData['BCC'])) unset($postData['BCC']);
            if(isset($postData['CC'])) unset($postData['CC']);
        }

        return $postData;
    }

}
