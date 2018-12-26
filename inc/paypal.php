<?php
class wp_paypal_gateway
{
    /**
     * PayPal API Version
     * @string
     */
    public $version = '200.00';

    /**
     * PayPal account username
     * @string
     */
    public $user = 'hunter.asian-facilitator_api1.gmail.com';
    //live account
    // public $user = 'vukhanhthien197_api1.gmail.com';

    /**
     * PayPal account password
     * @string
     */
    public $password = 'FCL2AE59FTRYB3KW';
    //live account
    // public $password = 'DZL67DXZYAK36L99';

    /**
     * PayPal account signature
     * @string
     */
    public $signature = 'A5MJnBbYdMIxBtRnzGT2Oz3Q6PRfA-Frn5oEmhuj8P1Slc.sdg.W21TQ';
    //live account
    // public $signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AD7OxESzSCXeiZzVPojVGqCL0Ugw';

    public function setVarApi($var1, $var2, $var3)
    {
        if ($var1 != '' && $var2 != '' && $var3 != '') {
            $this->user = $var1;
            $this->password = $var2;
            $this->signature = $var3;
        }
    }

    /**
     * Period of time (in seconds) after which the connection ends
     * @integer
     */
    public $time_out = 60;

    /**
     * Requires SSL Verification
     * @boolean
     */
    public $ssl_verify;

    /**
     * PayPal API Server
     * @string
     */
    private $server;

    /**
     * PayPal API Redirect URL
     * @string
     */
    private $redirect_url;

    /**
     * Real world PayPal API Server
     * @string
     */
    private $real_server = 'https://api-3t.paypal.com/nvp';

    /**
     * Read world PayPal redirect URL
     * @string
     */
    private $real_redirect_url = 'https://www.paypal.com/cgi-bin/webscr';

    /**
     * Sandbox PayPal Server
     * @string
     */
    private $sandbox_server = 'https://api-3t.sandbox.paypal.com/nvp';

    /**
     * Sandbox PayPal redirect URL
     * @string
     */
    private $sandbox_redirect_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

    /**
     * Array representing the supported short-terms
     * @array
     */
    private $short_term = array(
        'amount' => 'PAYMENTREQUEST_0_AMT',
        'currency_code' => 'PAYMENTREQUEST_0_CURRENCYCODE',
        'return_url' => 'RETURNURL',
        'cancel_url' => 'CANCELURL',
        'payment_action' => 'PAYMENTREQUEST_0_PAYMENTACTION',
        'token' => 'TOKEN',
        'payer_id' => 'PAYERID'
    );

    /**
     * When something goes wrong, the debug_info variable will be set
     * with a string, array, or object explaining the problem
     * @mixed
     */
    public $debug_info;

    /**
     * Saves the full response once a request succeed
     * @mixed
     */
    public $full_response = false;

    /**
     * Creates a new PayPal gateway object
     * @param boolean $sandbox Set to true if you want to enable the Sandbox mode
     */
    public function __construct($sandbox = false)
    {
        // Set the Server and Redirect URL
        $sandbox_enable = fw_get_db_settings_option('paypal_enable_sandbox');
        if (isset($sandbox_enable) && $sandbox_enable == 'on') {
            $this->server = $this->sandbox_server;
            $this->redirect_url = $this->sandbox_redirect_url;
        }else {
            $this->server = $this->real_server;
            $this->redirect_url = $this->real_redirect_url;
        }

        /*if ($sandbox) {
            $this->server = $this->sandbox_server;
            $this->redirect_url = $this->sandbox_redirect_url;
        } else {
            $this->server = $this->real_server;
            $this->redirect_url = $this->real_redirect_url;
        }*/

        // Set the SSL Verification
        $this->ssl_verify = apply_filters('https_local_ssl_verify', false);
    }

    /**
     * Executes a setExpressCheckout command
     * @param array $param
     * @return boolean
     */
    public function setExpressCheckout($param)
    {
        return $this->requestExpressCheckout('SetExpressCheckout', $param);
    }

    /**
     * Executes a getExpressCheckout command
     * @param array $param
     * @return boolean
     */
    public function getExpressCheckout($param)
    {
        return $this->requestExpressCheckout('GetExpressCheckoutDetails', $param);
    }

    /**
     * Executes a doExpressCheckout command
     * @param array $param
     * @return boolean
     */
    public function doExpressCheckout($param)
    {
        return $this->requestExpressCheckout('DoExpressCheckoutPayment', $param);
    }
    
    /**
     * Executes a CreateRecurringPaymentsProfile command
     * @param array $param
     * @return boolean
     */
    public function CreateRecurringPaymentsProfile($param)
    {
        return $this->requestExpressCheckout('CreateRecurringPaymentsProfile', $param);
    }
    
    /**
     * Executes a GetRecurringPaymentsProfileDetails command
     * @param array $param
     * @return boolean
     */
    public function GetRecurringPaymentsProfileDetails($param)
    {
        return $this->requestExpressCheckout('GetRecurringPaymentsProfileDetails', $param);
    }
    
    /**
     * Executes a ManageRecurringPaymentsProfileStatus command
     * @param array $param
     * @return boolean
     */
    public function ManageRecurringPaymentsProfileStatus($param)
    {
        return $this->requestExpressCheckout('ManageRecurringPaymentsProfileStatus', $param);
    }
    
    /**
     * Executes a UpdateRecurringPaymentsProfile command
     * @param array $param
     * @return boolean
     */
    public function UpdateRecurringPaymentsProfile($param)
    {
        return $this->requestExpressCheckout('UpdateRecurringPaymentsProfile', $param);
    }
    
    /**
     * Executes a UpdateRecurringPaymentsProfile command
     * @param array $param
     * @return boolean
     */
    public function RefundTransaction($param)
    {
        return $this->requestExpressCheckout('RefundTransaction', $param);
    }

    /**
     * @param string $type
     * @param array $param
     * @return boolean Specifies if the request is successful and the response property
     *                 is filled
     */
    private function requestExpressCheckout($type, $param)
    {
        // Construct the request array
        $param = $this->replace_short_terms($param);
        $request = $this->build_request($type, $param);

        // Makes the HTTP request
        $response = wp_remote_post($this->server, $request);

        // HTTP Request fails
        if (is_wp_error($response)) {
            $this->debug_info = $response;
            return false;
        }

        // Status code returned other than 200
        if ($response['response']['code'] != 200) {
            $this->debug_info = 'Response code different than 200';
            return false;
        }

        // Saves the full response
        $this->full_response = $response;

        // Request succeeded
        return true;
    }

    /**
     * Replace the Parameters short terms
     * @param array $param The given parameters array
     * @return array $param
     */
    private function replace_short_terms($param)
    {
        foreach ($this->short_term as $short_term => $long_term)
        {
            if (array_key_exists($short_term, $param)) {
                $param[$long_term] = $param[$short_term];
                unset($param[$short_term]);
            }
        }
        return $param;
    }

    /**
     * Builds the request array from the object, param and type parameters
     * @param string $type
     * @param array $param
     * @return array $body
     */
    private function build_request($type, $param)
    {
        // Request Body
        $body = $param;
        $body['METHOD'] = $type;
        $body['VERSION'] = $this->version;
        $body['USER'] = $this->user;
        $body['PWD'] = $this->password;
        $body['SIGNATURE'] = $this->signature;

        // Request Array
        $request = array(
            'method' => 'POST',
            'body' => $body,
            'timeout' => $this->time_out,
            'sslverify' => $this->ssl_verify
        );

        return $request;
    }

    /**
     * Returns the PayPal Body response
     * @return array $reponse
     */
    public function getResponse()
    {
        if ($this->full_response) {
            parse_str(urldecode($this->full_response['body']), $output);
            return $output;
        }
        return false;
    }

    /**
     * Returns the redirect URL
     * @return string $url
     */
    public function getRedirectURL()
    {
        $output = $this->getResponse();
        if ($output['ACK'] === 'Success') {
            $query_data = array(
                'cmd' => '_express-checkout',
                'token' => $output['TOKEN']
            );
            $url = $this->redirect_url . '?' . http_build_query($query_data);
            return $url;
        }
        return false;
    }

    /**
     * Returns the response Token
     * @return string $token
     */
    public function getToken()
    {
        $output = $this->getResponse();
        if ($output['ACK'] === 'Success') {
            return $output['TOKEN'];
        }
        return false;
    }
}