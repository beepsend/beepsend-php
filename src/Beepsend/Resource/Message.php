<?php

namespace Beepsend\Resource;

use Beepsend\Request;
use Beepsend\Helper\Message as MessageHelper;

/**
 * Beepsend message resource
 * @package Beepsend
 */
class Message
{

    /**
     * Beepsend request handler
     * @var Beepsend\Request
     */
    private $request;

    /**
     * Actions to call
     * @var array
     */
    private $actions = array(
        'sms' => '/sms/',
        'send' => '/send/',
        'batches' => '/batches/',
        'estimation' => 'costestimate/',
        'messages' => '/messages/',
        'conversations' => '/conversations/',
        'sendouts' => '/sendouts/'
    );

    /**
     * Init customer resource
     * @param Beepsend\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Send new SMS
     * @param string $to Number where we are sending message
     * @param string $from Number we are sending from or text
     * @param string $message Message we are sending
     * @param string $connection Connection id to use for sending sms
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     * @param array $options Array of additional options. More info on: http://api.beepsend.com/docs.html#send-sms
     * @return array
     */
    public function send($to, $from, $message, $connection = null, $encoding = 'UTF-8', $options = array())
    {
        $data = array(
            'from' => $from,
            'to' => $to,
            'message' => mb_convert_encoding($message, $encoding, 'UTF-8'),
            'encoding' => $encoding
        );

        /* Merge additional options if we have */
        if (!empty($options)) {
            $data = array_merge($data, $options);
        }
        $response = $this->request->execute($this->actions['send'] . $connection, 'POST', $data);
        return $response;
    }

    /**
     * Send SMS to your groups of contacts
     * @param int|array $groups Group where we are sending message, for multiple groups use array (number1, number2)
     * @param int|string $from Number we are sending from or text
     * @param string $message Message we are sending
     * @param string $connection Connection id to use for sending sms
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     * @param array $options Array of additional options. More info on: http://api.beepsend.com/docs.html#send-sms
     * @return array
     */
    public function group($groups, $from, $message, $connection = null, $encoding = 'UTF-8', $options = array())
    {
        if (!is_array($groups)) {
            $groups = array($groups);
        }
        $data = [];
        $sms = [
            'from' => $from,
            'groups' => $groups,
            'body' => mb_convert_encoding($message, $encoding, 'UTF-8'),
            'encoding' => $encoding
        ];
        if (isset($options['label'])) {
            if (is_array($options['label'])) {
                $data = array_merge($data, $options['label']);
            } else {
                $data['label'] = $options['label'];
            }
            unset($options['label']);
        }
        if (isset($options['send_time'])) {
            if (is_array($options['send_time'])) {
                $data = array_merge($data, $options['send_time']);
            } else {
                $data['send_time'] = $options['send_time'];
            }
            unset($options['send_time']);
        }
        /* Merge additional options if we have */
        if (!empty($options)) {
            $sms = array_merge($sms, $options);
        }
        $sms = [
            'sms' => $sms
        ];
        $data = array_merge($data,$sms);

        $response = $this->request->execute($this->actions['sendouts'] . $connection, 'POST', $data);
        return $response;
    }
    /**
     * Send SMS to your groups of contacts or a list of numbers
     * @param array $groups Group where we are sending message, OPTIONAL
     * @param array $to list of numbers to send to, OPTIONAL
     * @param string $from Number we are sending from or text
     * @param string $message Message we are sending
     * @param string $connection Connection id to use for sending sms
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     * @param array $options Array of additional options. More info on: http://api.beepsend.com/docs.html#sendouts
     * @return array
     */
    public function sendouts($groups = array(), $to = array(), $from, $message, $connection = null, $encoding = 'UTF-8', $options = array())
    {
        $data = [];
        $sms = [
            'from' => $from,
            'groups' => $groups,
            'to' => $to,
            'body' => mb_convert_encoding($message, $encoding, 'UTF-8'),
            'encoding' => $encoding
        ];
        if (isset($options['label'])) {
            if (is_array($options['label'])) {
                $data = array_merge($data, $options['label']);
            } else {
                $data['label'] = $options['label'];
            }
            unset($options['label']);
        }
        if (isset($options['send_time'])) {
            if (is_array($options['send_time'])) {
                $data = array_merge($data, $options['send_time']);
            } else {
                $data['send_time'] = $options['send_time'];
            }
            unset($options['send_time']);
        }
        /* Merge additional options if we have */
        if (!empty($options)) {
            $sms = array_merge($sms, $options);
        }
        $sms = [
            'sms' => $sms
        ];
        $data = array_merge($data,$sms);

        $response = $this->request->execute($this->actions['sendouts'] . $connection, 'POST', $data);
        return $response;
    }

    /**
     * Send multiple messages to one or more receivers
     * @param \Beepsend\Helper\Message $messages
     * @param string $connection Connection id to use for sending sms
     * @return array
     */
    public function multiple(MessageHelper $messages, $connection = null)
    {
        $response = $this->request->execute($this->actions['send'] . $connection, 'POST', $messages->get());
        return $response;
    }

    /**
     * Send new binary SMS
     * @param string $to Number where we are sending message, for multiple recepiants use array (number1, number2)
     * @param string $from Number we are sending from or text
     * @param string $message Message we are sending
     * @param string $connection Connection id to use for sending sms
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     * @param array $options Array of additional options. More info on: http://api.beepsend.com/docs.html#send-sms-binary
     * @return array
     */
    public function binary($to, $from, $message, $connection = null, $options = array())
    {
        $data = array(
            'from' => $from,
            'to' => $to,
            'message' => $message,
            'message_type' => 'binary'
        );

        /* Merge additional options if we have */
        if (!empty($options)) {
            $data = array_merge($data, $options);
        }

        $response = $this->request->execute($this->actions['send'] . $connection, 'POST', $data);
        return $response;
    }

    /**
     * Get message details of sent messages through Beepsend
     * @param int $smsId Id of message
     */
    public function lookup($smsId)
    {
        $response = $this->request->execute($this->actions['sms'] . $smsId, 'GET');
        return $response;
    }

    /**
     * Get messages details of sent messages through Beepsend
     * @param array $options Array of options to fetch messages. More info on: http://api.beepsend.com/docs.html#sms-lookup-multiple
     */
    public function multipleLookup($options = array())
    {
        $response = $this->request->execute($this->actions['sms'], 'GET', $options);
        return $response;
    }

    /**
     * Get previous batches
     * @return array
     */
    public function batches()
    {
        $response = $this->request->execute($this->actions['sendouts'], 'GET');
        return $response;
    }
    /**
     * Get previous sendouts
     * @return array
     */
    public function getSendouts()
    {
        $response = $this->request->execute($this->actions['sendouts'], 'GET');
        return $response;
    }

    /**
     * This call will give a paginated overview of messages in a batch, complete with sent and recieved message body.
     * @param int $batchId
     */
    public function twoWayBatch($batchId, $count = 200, $offset = 0)
    {
        $data = array(
            'count' => $count,
            'offset' => $offset
        );

        $response = $this->request->execute($this->actions['batches'] . $batchId . $this->actions['messages'], 'GET', $data);
        return $response;
    }

    /**
     * Estimate cost
     * @param int|array $to Msisdn or array of msisdns
     * @param string $message Message
     * @param string $connection Connection id
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     * @return array
     */
    public function estimateCost($to, $message, $connection = null, $encoding = 'UTF-8')
    {
        $data = array(
            'to' => $to,
            'message' => $message,
            'encoding' => $encoding
        );

        $response = $this->request->execute($this->actions['sms'] . $this->actions['estimation'] . $connection, 'POST', $data);
        return $response;
    }

    /**
     * Estimate cost for sendting to group
     * @param id|array $groups Single or multple groups
     * @param string $message Message
     * @param string $connection Connection id
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     */
    public function estimateCostGroup($groups, $message, $connection = null, $encoding = 'UTF-8')
    {
        $data = array(
            'groups' => $groups,
            'message' => $message,
            'encoding' => $encoding
        );

        $response = $this->request->execute($this->actions['sms'] . $this->actions['estimation'] . $connection, 'POST', $data);
        return $response;
    }

    /**
     * List your user conversations
     * @return array
     */
    public function conversations()
    {
        $response = $this->request->execute($this->actions['conversations'], 'GET');
        return $response;
    }

    /**
     * List all messages sent back and forth in to a single contact/number.
     * @param string $id
     * @return array
     */
    public function fullConversation($id, $options = array())
    {
        $response = $this->request->execute($this->actions['conversations'] . $id, 'GET', $options);
        return $response;
    }

}
