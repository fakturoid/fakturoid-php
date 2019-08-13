<?php

namespace Fakturoid;

class Client
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

        $this->requester = isset($options['requester']) ? $options['requester'] : new Requester;
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
        return $this->get("/invoices/$id/download.pdf", null, $headers);
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
        $requestParams = $this->filterOptions($params, array('paid_at', 'paid_amount', 'variable_symbol', 'bank_account_id'));
        $requestParams['event'] = $event;

        return $this->post("/invoices/$id/fire.json", $requestParams);
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
        $requestParams = $this->filterOptions($params, array('paid_on', 'paid_amount', 'variable_symbol', 'bank_account_id'));
        $requestParams['event'] = $event;

        return $this->post("/expenses/$id/fire.json", $requestParams);
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

    private function patch($path, $data)
    {
        return $this->run($path, array('method' => 'patch', 'data' => $data));
    }

    private function delete($path)
    {
        return $this->run($path, array('method' => 'delete'));
    }

    private function filterOptions($options, $allowedKeys, $caseSensitive = true)
    {
        if (!$options) {
            return;
        }

        $unknownKeys = array();

        foreach ($options as $key => $value) {
            if (!$caseSensitive) {
                $key = strtolower($key);
            }

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
            'if-none-match',    // Pairs with `ETag` response header.
            'if-modified-since' // Pairs with `Last-Modified` response header.
        );

        $headers = $this->filterOptions($headers, $allowedHeaders, false);

        if ($headers) {
            foreach ($headers as $name => $value) {
                if (strtolower($name) == 'if-modified-since' && $value instanceof DateTime) {
                    $headers[$name] = gmdate('D, d M Y H:i:s \G\M\T', $value->getTimestamp());
                    break;
                }
            }
        }

        $headers['User-Agent']   = $this->userAgent;
        $headers['Content-Type'] = 'application/json';

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
