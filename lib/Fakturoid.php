<?php

class FakturoidException extends Exception {}

/* Safety */

if (!function_exists('curl_init')) {
    throw new FakturoidException('Fakturoid lib needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new FakturoidException('Fakturoid lib needs the JSON PHP extension.');
}

class Fakturoid
{
    const URL = 'https://app.fakturoid.cz/api/v2/accounts/';

    private $slug;
    private $apiKey;
    private $email;
    private $userAgent;

    private $requester; // For testing purposes

    public function __construct($slug, $email, $apiKey, $userAgent, $options = array())
    {
        $this->slug      = $slug;
        $this->email     = $email;
        $this->apiKey    = $apiKey;
        $this->userAgent = $userAgent;

        $this->requester = isset($options['requester']) ? $options['requester'] : new FakturoidRequester;
    }

    /* Account */

    public function getAccount($headers = null)
    {
        return $this->get('/account.json', null, $headers);
    }

    /* User */

    public function getUser($id, $headers = null)
    {
        return $this->get("/users/$id.json", null, $headers);
    }

    public function getUsers($params = null, $headers = null)
    {
        return $this->get('/users.json', $this->filterOptions($params, array('page')), $headers);
    }

    /* Invoice */

    public function getInvoices($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id');
        return $this->get('/invoices.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getRegularInvoices($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id');
        return $this->get('/invoices/regular.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getProformaInvoices($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id');
        return $this->get('/invoices/proforma.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getInvoice($id, $headers = null)
    {
        return $this->get("/invoices/$id.json", null, $headers);
    }

    public function getInvoicePdf($id, $headers = null)
    {
        // TODO: Use ->get?
        return $this->run("/invoices/$id/download.pdf", array('method' => 'get', 'headers' => $headers));
    }

    public function searchInvoices($params = null, $headers = null)
    {
        return $this->get('/invoices/search.json', $this->filterOptions($params, array('query', 'page')), $headers);
    }

    public function updateInvoice($id, $data)
    {
        return $this->patch("/invoices/$id.json", $data);
    }

    public function fireInvoice($id, $event, $params = array())
    {
        return $this->post("/invoices/$id/fire.json", array_merge(array('event' => $event), $params));
    }

    public function createInvoice($data)
    {
        return $this->post('/invoices.json', $data);
    }

    public function deleteInvoice($id)
    {
        return $this->delete("/invoices/$id.json");
    }

    /* Expense */

    public function getExpenses($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status');
        return $this->get('/expenses.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getExpense($id, $headers = null)
    {
        return $this->get("/expenses/$id.json", null, $headers);
    }

    public function searchExpenses($params = null, $headers = null)
    {
        return $this->get('/expenses/search.json', $this->filterOptions($params, array('query', 'page')), $headers);
    }

    public function updateExpense($id, $data)
    {
        return $this->patch("/expenses/$id.json", $data);
    }

    public function fireExpense($id, $event, $params = array())
    {
        return $this->post("/expenses/$id/fire.json", array_merge(array('event' => $event), $params));
    }

    public function createExpense($data)
    {
        return $this->post('/expenses.json', $data);
    }

    public function deleteExpense($id)
    {
        return $this->delete("/expenses/$id.json");
    }

    /* Subject */

    public function getSubjects($params = null, $headers = null)
    {
        $allowed = array('since', 'updated_since', 'page', 'custom_id');
        return $this->get('/subjects.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getSubject($id, $headers = null)
    {
        return $this->get("/subjects/$id.json", null, $headers);
    }

    public function createSubject($data)
    {
        return $this->post('/subjects.json', $data);
    }

    public function updateSubject($id, $data)
    {
        return $this->patch("/subjects/$id.json", $data);
    }

    public function deleteSubject($id)
    {
        return $this->delete("/subjects/$id.json");
    }

    public function searchSubjects($params = null, $headers = null)
    {
        return $this->get('/subjects/search.json', $this->filterOptions($params, array('query')), $headers);
    }

    /* Generator */

    public function getGenerators($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page');
        return $this->get('/generators.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getTemplateGenerators($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page');
        return $this->get('/generators/template.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getRecurringGenerators($params = null, $headers = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page');
        return $this->get('/generators/recurring.json', $this->filterOptions($params, $allowed), $headers);
    }

    public function getGenerator($id, $headers = null)
    {
        return $this->get("/generators/$id.json", null, $headers);
    }

    public function createGenerator($data)
    {
        return $this->post('/generators.json', $data);
    }

    public function updateGenerator($id, $data)
    {
        return $this->patch("/generators/$id.json", $data);
    }

    public function deleteGenerator($id)
    {
        return $this->delete("/generators/$id.json");
    }

    /* Message */

    public function createMessage($id, $data)
    {
        return $this->post("/invoices/$id/message.json", $data);
    }

    /* Event */

    public function getEvents($params = null, $headers = null)
    {
        return $this->get('/events.json', $this->filterOptions($params, array('subject_id', 'since', 'page')), $headers);
    }

    public function getPaidEvents($params = null, $headers = null)
    {
        return $this->get('/events/paid.json', $this->filterOptions($params, array('subject_id', 'since', 'page')), $headers);
    }

    /* Todo */

    public function getTodos($params = null, $headers = null)
    {
        return $this->get('/todos.json', $this->filterOptions($params, array('subject_id', 'since', 'page')), $headers);
    }

    /* Helper functions */

    private function get($path, $params = null, $headers = null)
    {
        return $this->run($path, array('method' => 'get', 'params' => $params, 'headers' => $headers));
    }

    private function post($path, $data)
    {
        return $this->run($path, array('method' => 'post', 'data' => $data));
    }

    private function put($path, $data)
    {
        return $this->run($path, array('method' => 'put', 'data' => $data));
    }

    private function patch($path, $data)
    {
        return $this->run($path, array('method' => 'patch', 'data' => $data));
    }

    private function delete($path)
    {
        return $this->run($path, array('method' => 'delete'));
    }

    private function filterOptions($options, $allowedKeys)
    {
        if (!$options) {
            return;
        }

        $unknownKeys = array();

        foreach ($options as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                unset($options[$key]);
                $unknownKeys[] = $key;
            }
        }

        if (!empty($unknownKeys)) {
            trigger_error("Unknown option keys: " . implode(', ', $unknownKeys));
        }

        return $options;
    }

    /**
     * Execute HTTP method on path with data
     */
    private function run($path, $options)
    {
        $method  = $options['method'];
        $data    = isset($options['data'])    ? $options['data']    : null;
        $params  = isset($options['params'])  ? $options['params']  : null;
        $headers = isset($options['headers']) ? $options['headers'] : array();
        $body    = !empty($data)              ? json_encode($data)  : null;

        // Arrays in constants are in PHP 5.6+
        $allowedHeaders = array(
            'If-None-Match',    // Pairs with `ETag` response header.
            'If-Modified-Since' // Pairs with `Last-Modified` response header.
        );

        $headers = $this->filterOptions($headers, $allowedHeaders);
        $headers['User-Agent']   = $this->userAgent;
        $headers['Content-Type'] = 'application/json';

        if (!empty($headers['If-Modified-Since']) && $headers['If-Modified-Since'] instanceof DateTime) {
            $headers['If-Modified-Since'] = gmdate('D, d M Y H:i:s \G\M\T', $headers['If-Modified-Since']->getTimestamp());
        }

        $response = $this->requester->run(array(
            'url'     => self::URL . $this->slug . $path,
            'method'  => $method,
            'params'  => $params,
            'body'    => $body,
            'userpwd' => "$this->email:$this->apiKey",
            'headers' => $headers
        ));

        return $response;
    }
}

// For testing purposes.
class FakturoidRequester
{
    public function run($options)
    {
        $request  = new FakturoidRequest($options);
        $response = $request->run();

        return $response;
    }
}

class FakturoidRequest
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
            throw new FakturoidException('cURL failed to initialize.');
        }

        curl_setopt($c, CURLOPT_URL, $this->getUrl());
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FAILONERROR, false); // to get error messages in response body
        curl_setopt($c, CURLOPT_USERPWD, $this->getUserpwd());
        curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($c, CURLOPT_USERAGENT, $this->getHeader('User-Agent'));
        curl_setopt($c, CURLOPT_HTTPHEADER, $this->filterHeaders());

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
            throw new FakturoidException($message, curl_errno($c));
        }

        if ($info['http_code'] >= 400) {
            throw new FakturoidException($response, $info['http_code']);
        }

        curl_close($c);

        return new FakturoidResponse($info, $response);
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
        return $this->headers[$name];
    }

    // User-Agent header is sent differently.
    private function filterHeaders()
    {
        $headers = array();

        foreach ($this->headers as $name => $value) {
            if ($name != 'User-Agent') {
                $headers[] = "$name: $value";
            }
        }

        return $headers;
    }
}

class FakturoidResponse
{
    private $statusCode;
    private $headers;
    private $body;

    public function __construct($info, $response)
    {
        $this->statusCode = $info['http_code'];
        $this->headers    = $info['headers'];

        if ($this->isJson()) {
            $this->body = json_decode($response);
        } else {
            $this->body = $response;
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        // Typically in 304 Not Modified.
        if ($this->body === '') {
            return null;
        }

        return $this->body;
    }

    private function isJson()
    {
        if (empty($this->headers['Content-Type'])) {
            return false;
        }

        $contentType = $this->headers['Content-Type'];
        return strpos($contentType, 'application/json') !== false;
    }
}
