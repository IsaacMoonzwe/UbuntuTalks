<?php

class Curl
{

    private $url;
    protected $ch;
    private $options = [];
    private $headers = [];
    private $error_code;
    private $error_string;
    private $info;
    private $response = '';

    public function __construct($url = '')
    {
        if (!$this->is_enabled()) {
            throw new Exception('cURL Class - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
        }
        $url AND $this->create($url);
    }

    public function __call($method, $arguments)
    {
        array_unshift($arguments, $method);
        return call_user_func_array([$this, 'request'], $arguments);
    }

    public function request($method, $url, $params = [], $options = [])
    {
        if (strtoupper($method === 'GET')) {
            $this->create($url . ($params ? '?' . http_build_query($params, NULL, '&') : ''));
        } else {
            $method = strtolower($method);
            $this->create($url);
            $this->{$method}($params);
        }
        // Add in the specific options provided
        $this->options($options);
        return $this->execute();
    }

    public function post($params = [], $options = [])
    {
        if (is_array($params)) {
            $params = http_build_query($params, NULL, '&');
        }
        $this->options($options);
        $this->http_method('post');
        $this->option(CURLOPT_POST, TRUE);
        $this->option(CURLOPT_POSTFIELDS, $params);
    }

    public function put($params = [], $options = [])
    {
        if (is_array($params)) {
            $params = http_build_query($params, NULL, '&');
        }
        $this->options($options);
        $this->http_method('put');
        $this->option(CURLOPT_POSTFIELDS, $params);
        $this->option(CURLOPT_HTTPHEADER, ['X-HTTP-Method-Override: PUT']);
    }

    public function http_header($header, $content = NULL)
    {
        $this->headers[] = $content ? $header . ': ' . $content : $header;
        return $this;
    }

    public function http_method($method)
    {
        $this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        return $this;
    }

    public function ssl($verify_peer = TRUE, $verify_host = 2, $path_to_cert = NULL)
    {
        if ($verify_peer) {
            $this->option(CURLOPT_SSL_VERIFYPEER, TRUE);
            $this->option(CURLOPT_SSL_VERIFYHOST, $verify_host);
            if (isset($path_to_cert)) {
                $path_to_cert = realpath($path_to_cert);
                $this->option(CURLOPT_CAINFO, $path_to_cert);
            }
        } else {
            $this->option(CURLOPT_SSL_VERIFYPEER, FALSE);
            $this->option(CURLOPT_SSL_VERIFYHOST, $verify_host);
        }
        return $this;
    }

    public function options($options = [])
    {
        foreach ($options as $option_code => $option_value) {
            $this->option($option_code, $option_value);
        }
        curl_setopt_array($this->ch, $this->options);
        return $this;
    }

    public function option($code, $value, $prefix = 'opt')
    {
        if (is_string($code) && !is_numeric($code)) {
            $code = constant('CURL' . strtoupper($prefix) . '_' . strtoupper($code));
        }
        $this->options[$code] = $value;
        return $this;
    }

    public function create($url)
    {
        $this->url = $url;
        $this->ch = curl_init($this->url);
        return $this;
    }

    public function execute()
    {
        if (!isset($this->options[CURLOPT_TIMEOUT])) {
            $this->options[CURLOPT_TIMEOUT] = 30;
        }
        if (!isset($this->options[CURLOPT_RETURNTRANSFER])) {
            $this->options[CURLOPT_RETURNTRANSFER] = TRUE;
        }
        if (!isset($this->options[CURLOPT_FAILONERROR])) {
            $this->options[CURLOPT_FAILONERROR] = TRUE;
        }
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            if (!isset($this->options[CURLOPT_FOLLOWLOCATION])) {
                $this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
            }
        }
        if (!empty($this->headers)) {
            $this->option(CURLOPT_HTTPHEADER, $this->headers);
        }
        $this->options();
        $this->response = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        // Request failed
        if ($this->response === FALSE) {
            $errno = curl_errno($this->ch);
            $error = curl_error($this->ch);
            curl_close($this->ch);
            $this->set_defaults();
            $this->error_code = $errno;
            $this->error_string = $error;
            return FALSE;
        } else {
            curl_close($this->ch);
            $this->last_response = $this->response;
            $this->set_defaults();
            return $this->last_response;
        }
    }

    public function getErrorCode()
    {
        return $this->error_code;
    }

    public function getError()
    {
        return $this->error_string;
    }

    public function is_enabled()
    {
        return function_exists('curl_init');
    }

    public function debug()
    {
        echo "=============================================<br/>\n";
        echo "<h2>CURL Test</h2>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Response</h3>\n";
        echo "<code>" . nl2br(htmlentities($this->last_response)) . "</code><br/>\n\n";
        if ($this->error_string) {
            echo "=============================================<br/>\n";
            echo "<h3>Errors</h3>";
            echo "<strong>Code:</strong> " . $this->error_code . "<br/>\n";
            echo "<strong>Message:</strong> " . $this->error_string . "<br/>\n";
        }
        echo "=============================================<br/>\n";
        echo "<h3>Info</h3>";
        echo "<pre>";
        print_r($this->info);
        echo "</pre>";
    }

    public function debug_request()
    {
        return ['url' => $this->url];
    }

    public function set_defaults()
    {
        $this->ch = NULL;
        $this->headers = [];
        $this->options = [];
        $this->error_code = NULL;
        $this->error_string = '';
        $this->response = '';
    }

}
