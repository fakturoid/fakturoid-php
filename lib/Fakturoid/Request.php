<?php

namespace Fakturoid;

class Request
{
    private $url;
    private $method;
    private $body;
    private $userpwd;
    private $headers;

    public function __construct($options)
    {
        $this->url    = $options['url'];
        $this->method = $options['method'];

        if (!empty($options['params'])) {
            $serializedParams = http_build_query($options['params']);

            if (!empty($serializedParams)) {
                $this->url .= '?' . http_build_query($options['params']);
            }
        }

        if (array_key_exists('body', $options)) {
            $this->body = $options['body'];
        }

        $this->userpwd = $options['userpwd'];
        $this->headers = $options['headers'];
    }

    public function run()
    {
        $c = curl_init();

        if ($c === false) {
            throw new Exception('cURL failed to initialize.');
        }

        curl_setopt($c, CURLOPT_URL, $this->getUrl());
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FAILONERROR, false); // to get error messages in response body
        curl_setopt($c, CURLOPT_USERPWD, $this->getUserpwd());
        curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($c, CURLOPT_USERAGENT, $this->getHeader('User-Agent'));
        curl_setopt($c, CURLOPT_HTTPHEADER, $this->getHttpHeaders());

        $headers = array();

        // PHP 5.3+
        curl_setopt($c, CURLOPT_HEADERFUNCTION, function ($_curl, $header) use (&$headers) {
            $length = strlen($header);
            $header = explode(':', $header, 2);

            if (count($header) < 2) { // Ignore non-key-value headers
                return $length;
            }

            $name  = trim($header[0]);
            $value = trim($header[1]);
            $headers[$name] = $value;

            return $length;
        });

        if ($this->getMethod() === 'post') {
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, $this->getBody());
        } elseif ($this->getMethod() === 'put') {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($c, CURLOPT_POSTFIELDS, $this->getBody());
        } elseif ($this->getMethod() === 'patch') {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($c, CURLOPT_POSTFIELDS, $this->getBody());
        } elseif ($this->getMethod() === 'delete') {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response        = curl_exec($c);
        $info            = curl_getinfo($c);
        $info['headers'] = $headers;

        if ($response === false) {
            $message = sprintf('cURL failed with error #%d: %s', curl_errno($c), curl_error($c));
            throw new Exception($message, curl_errno($c));
        }

        if ($info['http_code'] >= 400) {
            throw new Exception($response, $info['http_code']);
        }

        curl_close($c);

        return new Response($info, $response);
    }

    // For testing purposes

    public function getUrl()
    {
        return $this->url;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getUserpwd()
    {
        return $this->userpwd;
    }

    public function getHeader($name)
    {
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($headerName) == strtolower($name)) {
                return $value;
            }
        }
    }

    // User-Agent header is sent differently.
    public function getHttpHeaders()
    {
        $headers = array(
            'X-Client-Env: PHP ' . PHP_VERSION
        );

        foreach ($this->headers as $name => $value) {
            if (strtolower($name) != 'user-agent') {
                $headers[] = "$name: $value";
            }
        }

        return $headers;
    }
}
