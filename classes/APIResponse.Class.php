<?php

class APIResponse {
    private $arr;
    private $errorCode;

    function __construct() {
        $this->arr = [];
        $this->errorCode = null;
    }

    /**
     * Sets the success message.
     *
     * @param string $messageContent The success message.
     */
    function setMessage($messageContent) {
        $this->arr['message'] = $messageContent;
    }

    /**
     * Sets the error message and the HTTP status code returned to the client (defaults to 500).
     *
     * @param string $errorMessage The error message.
     * @param int $errorCode The HTTP status code of the error.
     */
    function setError($errorMessage, $errorCode=null) {
        $this->arr['error'] = $errorMessage;
        $this->errorCode = $errorCode;
    }

    /**
     * Sets a data field to the response object.
     *
     * @param string $key
     * @param mixed $value
     */
    function setData($key, $value) {
        $this->arr[$key] = $value;
    }

    /**
     * Sets many data fields.
     * You can use this if you need to send an array instead of an object.
     *
     * @param array $arr
     */
    function setDataArray($arr) {
        foreach ($arr as $key => $value) {
            $this->setData($key, $value);
        }
    }

    /**
     * Returns the response as a JSON object.
     */
    function asJson() {
      return json_encode($this->arr);
    }

    /**
     * Sends the JSON object and the HTTP status code to the client.
     */
    function send() {
        if (array_key_exists('error', $this->arr)) {
            http_response_code($this->errorCode ?? 500);
        }

        header('Content-Type: application/json');
        print $this->asJson();
    }
}
