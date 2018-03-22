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

    public function get_account()
    {
        return $this->get('/account.json');
    }

    /* User */

    public function get_user($id)
    {
        return $this->get("/users/$id.json");
    }

    public function get_users($options = null)
    {
        return $this->get('/users.json', $this->filterOptions($options, array('page')));
    }

    /* Invoice */

    public function get_invoices($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id');
        return $this->get('/invoices.json', $this->filterOptions($options, $allowed));
    }

    public function get_regular_invoices($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id');
        return $this->get('/invoices/regular.json', $this->filterOptions($options, $allowed));
    }

    public function get_proforma_invoices($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id');
        return $this->get('/invoices/proforma.json', $this->filterOptions($options, $allowed));
    }

    public function get_invoice($id)
    {
        return $this->get("/invoices/$id.json");
    }

    public function get_invoice_pdf($id)
    {
        return $this->run("/invoices/$id/download.pdf", array('method' => 'get', 'jsonDecodeReturn' => false));
    }

    public function search_invoices($options = null)
    {
        return $this->get('/invoices/search.json', $this->filterOptions($options, array('query', 'page')));
    }

    public function update_invoice($id, $data)
    {
        return $this->patch("/invoices/$id.json", $data);
    }

    public function fire_invoice($id, $event, $options = array())
    {
        return $this->post("/invoices/$id/fire.json", array_merge(array('event' => $event), $options));
    }

    public function create_invoice($data)
    {
        return $this->post('/invoices.json', $data);
    }

    public function delete_invoice($id)
    {
        return $this->delete("/invoices/$id.json");
    }

    /* Expense */

    public function get_expenses($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page', 'status');
        return $this->get('/expenses.json', $this->filterOptions($options, $allowed));
    }

    public function get_expense($id)
    {
        return $this->get("/expenses/$id.json");
    }

    public function search_expenses($options = null)
    {
        return $this->get('/expenses/search.json', $this->filterOptions($options, array('query', 'page')));
    }

    public function update_expense($id, $data)
    {
        return $this->patch("/expenses/$id.json", $data);
    }

    public function fire_expense($id, $event, $options = array())
    {
        return $this->post("/expenses/$id/fire.json", array_merge(array('event' => $event), $options));
    }

    public function create_expense($data)
    {
        return $this->post('/expenses.json', $data);
    }

    public function delete_expense($id)
    {
        return $this->delete("/expenses/$id.json");
    }

    /* Subject */

    public function get_subjects($options = null)
    {
        $allowed = array('since', 'updated_since', 'page', 'custom_id');
        return $this->get('/subjects.json', $this->filterOptions($options, $allowed));
    }

    public function get_subject($id)
    {
        return $this->get("/subjects/$id.json");
    }

    public function create_subject($data)
    {
        return $this->post('/subjects.json', $data);
    }

    public function update_subject($id, $data)
    {
        return $this->patch("/subjects/$id.json", $data);
    }

    public function delete_subject($id)
    {
        return $this->delete("/subjects/$id.json");
    }

    public function search_subjects($options = null)
    {
        return $this->get('/subjects/search.json', $this->filterOptions($options, array('query')));
    }

    /* Generator */

    public function get_generators($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page');
        return $this->get('/generators.json', $this->filterOptions($options, $allowed));
    }

    public function get_template_generators($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page');
        return $this->get('/generators/template.json', $this->filterOptions($options, $allowed));
    }

    public function get_recurring_generators($options = null)
    {
        $allowed = array('subject_id', 'since', 'updated_since', 'page');
        return $this->get('/generators/recurring.json', $this->filterOptions($options, $allowed));
    }

    public function get_generator($id)
    {
        return $this->get("/generators/$id.json");
    }

    public function create_generator($data)
    {
        return $this->post('/generators.json', $data);
    }

    public function update_generator($id, $data)
    {
        return $this->patch("/generators/$id.json", $data);
    }

    public function delete_generator($id)
    {
        return $this->delete("/generators/$id.json");
    }

    /* Message */

    public function create_message($id, $data)
    {
        return $this->post("/invoices/$id/message.json", $data);
    }

    /* Event */

    public function get_events($options = null)
    {
        return $this->get('/events.json', $this->filterOptions($options, array('subject_id', 'since', 'page')));
    }

    public function get_paid_events($options = null)
    {
        return $this->get('/events/paid.json', $this->filterOptions($options, array('subject_id', 'since', 'page')));
    }

    /* Todo */

    public function get_todos($options = null)
    {
        return $this->get('/todos.json', $this->filterOptions($options, array('subject_id', 'since', 'page')));
    }

    /* Helper functions */

    private function get($path, $params = null)
    {
        return $this->run($path, array('method' => 'get', 'params' => $params));
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

    private function filterOptions($options, $allowedOptions)
    {
        $safeOptions = array();

        foreach ($allowedOptions as $key) {
            if (isset($options[$key])) {
                $safeOptions[$key] = $options[$key];
            } else {
                $safeOptions[$key] = null;
            }
        }

        return $safeOptions;
    }

    /**
     * Execute HTTP method on path with data
     */
    private function run($path, $options)
    {
        $method           = $options['method'];
        $data             = isset($options['data'])             ? $options['data']             : null;
        $params           = isset($options['params'])           ? $options['params']           : null;
        $jsonDecodeReturn = isset($options['jsonDecodeReturn']) ? $options['jsonDecodeReturn'] : true;

        $response = $this->requester->run(array(
            'url'     => self::URL . $this->slug . $path,
            'method'  => $method,
            'params'  => $params,
            'body'    => $data,
            'userpwd' => "$this->email:$this->apiKey",
            'headers' => array(
                'User-Agent' => $this->userAgent
            )
        ));

        if ($jsonDecodeReturn) {
            $response = json_decode($response);
        }

        return $response;
    }
}

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
            $this->body = json_encode($options['body']);
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
        curl_setopt($c, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

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

        $response = curl_exec($c);
        $info     = curl_getinfo($c);

        if ($response === false) {
            $message = sprintf('cURL failed with error #%d: %s', curl_errno($c), curl_error($c));
            throw new FakturoidException($message, curl_errno($c));
        }

        if ($info['http_code'] >= 400) {
            throw new FakturoidException($response, $info['http_code']);
        }

        curl_close($c);

        return $response;
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
}
