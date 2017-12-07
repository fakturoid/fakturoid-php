<?php

/**
 * Class FakturoidException
 */
class FakturoidException extends Exception
{
}

// ************************************************************************************************
/* Safety */

if ( ! function_exists('curl_init') ) {
    throw new FakturoidException('Fakturoid lib needs the CURL PHP extension.');
}

if ( ! function_exists('json_decode') ) {
    throw new FakturoidException('Fakturoid lib needs the JSON PHP extension.');
}
// ************************************************************************************************

/**
 * Class Fakturoid
 */
class Fakturoid
{
    /**
     * @var
     */
    private $slug;
    /**
     * @var
     */
    private $api_key;
    /**
     * @var
     */
    private $email;
    /**
     * @var
     */
    private $user_agent;

    /**
     * Fakturoid constructor.
     * @param $slug
     * @param $email
     * @param $api_key
     * @param $user_agent
     */
    public function __construct($slug, $email, $api_key, $user_agent)
    {
        $this->slug = $slug;
        $this->email = $email;
        $this->api_key = $api_key;
        $this->user_agent = $user_agent;
    }

    /* Account */

    /**
     * @param int $last_modify
     * @return array
     */
    public function get_account($last_modify = 0)
    {
        return $this->get('/account.json', $last_modify);
    }

    /* User */

    /**
     * @param $id
     * @param int $last_modify
     * @return array
     */
    public function get_user($id, $last_modify = 0)
    {
        return $this->get("/users/$id.json", $last_modify);
    }

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_users($options = null, $last_modify = 0)
    {
        return $this->get('/users.json' . $this->convert_options($options, array( 'page' )), $last_modify);
    }

    /* Invoice */

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_invoices($options = null, $last_modify = 0)
    {
        return $this->get("/invoices.json" . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id' )), $last_modify);
    }

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_regular_invoices($options = null, $last_modify = 0)
    {
        return $this->get('/invoices/regular.json' . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id' )), $last_modify);
    }

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_proforma_invoices($options = null, $last_modify = 0)
    {
        return $this->get('/invoices/proforma.json' . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page', 'status', 'custom_id' )), $last_modify);
    }

    /**
     * @param $id
     * @param int $last_modify
     * @return array
     */
    public function get_invoice($id, $last_modify = 0)
    {
        return $this->get("/invoices/$id.json", $last_modify);
    }

    /**
     * @param $id
     * @param int $last_modify
     * @return array
     * @throws FakturoidException
     */
    public function get_invoice_pdf($id, $last_modify = 0)
    {
        return $this->run("/invoices/$id/download.pdf", 'get', null, false, $last_modify);
    }

    /**
     * @param $options
     * @return array
     */
    public function search_invoices($options = null)
    {
        return $this->get("/invoices/search.json" . $this->convert_options($options, array( 'query', 'page' )));
    }

    /**
     * @param $id
     * @param $data
     * @return array
     */
    public function update_invoice($id, $data)
    {
        return $this->patch("/invoices/$id.json", $data);
    }

    /**
     * @param $id
     * @param $event
     * @param array $options
     * @return array
     */
    public function fire_invoice($id, $event, $options = array())
    {
        return $this->post("/invoices/$id/fire.json", array_merge(array( 'event' => $event ), $options));
    }

    /**
     * @param $data
     * @return array
     */
    public function create_invoice($data)
    {
        return $this->post('/invoices.json', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete_invoice($id)
    {
        return $this->delete("/invoices/$id.json");
    }

    /* Expense */

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_expenses($options = null, $last_modify = 0)
    {
        return $this->get("/expenses.json" . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page', 'status' )), $last_modify);
    }

    /**
     * @param $id
     * @param int $last_modify
     * @return array
     */
    public function get_expense($id, $last_modify = 0)
    {
        return $this->get("/expenses/$id.json", $last_modify);
    }

    /**
     * @param $options
     * @return array
     */
    public function search_expenses($options = null)
    {
        return $this->get("/expenses/search.json" . $this->convert_options($options, array( 'query', 'page' )));
    }

    /**
     * @param $id
     * @param $data
     * @return array
     */
    public function update_expense($id, $data)
    {
        return $this->patch("/expenses/$id.json", $data);
    }

    /**
     * @param $id
     * @param $event
     * @param array $options
     * @return array
     */
    public function fire_expense($id, $event, $options = array())
    {
        return $this->post("/expenses/$id/fire.json", array_merge(array( 'event' => $event ), $options));
    }

    /**
     * @param $data
     * @return array
     */
    public function create_expense($data)
    {
        return $this->post('/expenses.json', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete_expense($id)
    {
        return $this->delete("/expenses/$id.json");
    }

    /* Subject */

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_subjects($options = null, $last_modify = 0)
    {
        return $this->get('/subjects.json' . $this->convert_options($options, array( 'since', 'updated_since', 'page', 'custom_id' )), $last_modify);
    }

    /**
     * @param $id
     * @param int $last_modify
     * @return array
     */
    public function get_subject($id, $last_modify = 0)
    {
        return $this->get("/subjects/$id.json", $last_modify);
    }

    /**
     * @param $data
     * @return array
     */
    public function create_subject($data)
    {
        return $this->post('/subjects.json', $data);
    }

    /**
     * @param $id
     * @param $data
     * @return array
     */
    public function update_subject($id, $data)
    {
        return $this->patch("/subjects/$id.json", $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete_subject($id)
    {
        return $this->delete("/subjects/$id.json");
    }

    /**
     * @param $options
     * @return array
     */
    public function search_subjects($options = null)
    {
        return $this->get('/subjects/search.json' . $this->convert_options($options, array( 'query' )));
    }

    /* Generator */

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_generators($options = null, $last_modify = 0)
    {
        return $this->get('/generators.json' . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page' )), $last_modify);
    }

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_template_generators($options = null, $last_modify = 0)
    {
        return $this->get('/generators/template.json' . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page' )), $last_modify);
    }

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_recurring_generators($options = null, $last_modify = 0)
    {
        return $this->get('/generators/recurring.json' . $this->convert_options($options, array( 'subject_id', 'since', 'updated_since', 'page' )), $last_modify);
    }

    /**
     * @param $id
     * @param int $last_modify
     * @return array
     */
    public function get_generator($id, $last_modify = 0)
    {
        return $this->get("/generators/$id.json", $last_modify);
    }

    /**
     * @param $data
     * @return array
     */
    public function create_generator($data)
    {
        return $this->post('/generators.json', $data);
    }

    /**
     * @param $id
     * @param $data
     * @return array
     */
    public function update_generator($id, $data)
    {
        return $this->patch("/generators/$id.json", $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete_generator($id)
    {
        return $this->delete("/generators/$id.json");
    }

    /* Message */

    /**
     * @param $id
     * @param $data
     * @return array
     */
    public function create_message($id, $data)
    {
        return $this->post("/invoices/$id/message.json", $data);
    }

    /* Event */

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_events($options = null, $last_modify = 0)
    {
        return $this->get('/events.json' . $this->convert_options($options, array( 'subject_id', 'since', 'page' )), $last_modify);
    }

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_paid_events($options = null, $last_modify = 0)
    {
        return $this->get('/events/paid.json' . $this->convert_options($options, array( 'subject_id', 'since', 'page' )), $last_modify);
    }

    /* Todo */

    /**
     * @param $options
     * @param int $last_modify
     * @return array
     */
    public function get_todos($options = null, $last_modify = 0)
    {
        return $this->get('/todos.json' . $this->convert_options($options, array( 'subject_id', 'since', 'page' )), $last_modify);
    }

    /* Helper functions */

    /**
     * @param $path
     * @param int $last_modify
     * @return array
     * @throws FakturoidException
     */
    private function get($path, $last_modify = 0)
    {
        return $this->run($path, 'get', null, true, $last_modify);
    }

    /**
     * @param $path
     * @param $data
     * @return array
     * @throws FakturoidException
     */
    private function post($path, $data)
    {
        return $this->run($path, 'post', $data);
    }

    /**
     * @param $path
     * @param $data
     * @return array
     * @throws FakturoidException
     */
    private function put($path, $data)
    {
        return $this->run($path, 'put', $data);
    }

    /**
     * @param $path
     * @param $data
     * @return array
     * @throws FakturoidException
     */
    private function patch($path, $data)
    {
        return $this->run($path, 'patch', $data);
    }

    /**
     * @param $path
     * @return array
     * @throws FakturoidException
     */
    private function delete($path)
    {
        return $this->run($path, 'delete');
    }

    /* Query building */

    /**
     * @param $options
     * @param $allowed
     * @return string
     */
    private function convert_options($options, $allowed)
    {
        $safe_options = array();

        foreach ($allowed as $key) {
            if ( isset($options[$key]) ) {
                $safe_options[$key] = $options[$key];
            } else {
                $safe_options[$key] = null;
            }
        }

        if ( ! empty($safe_options) ) {
            return "?" . http_build_query($safe_options);
        }
    }


    /**
     * Execute HTTP method on path with data
     *
     * @param string $path
     * @param string $method
     * @param mixed $data
     * @param bool $json_decode_return
     * @param int $last_modify
     * @return array
     * @throws FakturoidException
     */
    private function run($path, $method, $data = null, $json_decode_return = true, $last_modify = 0)
    {
        $c = curl_init();

        if ( $c === false ) {
            throw new FakturoidException('cURL failed to initialize.');
        }

        $headers = array( 'Content-Type: application/json' );

        if ( $last_modify > 0 ) {
            $headers[] = 'If-Modified-Since: ' . gmdate('D, d M Y H:i:s \G\M\T', $last_modify);
        }

        curl_setopt($c, CURLOPT_URL, "https://app.fakturoid.cz/api/v2/accounts/$this->slug$path");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FAILONERROR, false); // to get error messages in response body
        curl_setopt($c, CURLOPT_USERPWD, "$this->email:$this->api_key");
        curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($c, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c, CURLOPT_FILETIME, true);

        if ( $method == 'post' ) {
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ( $method == 'put' ) {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ( $method == 'patch' ) {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ( $method == 'delete' ) {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        $response = curl_exec($c);
        $info = curl_getinfo($c);

        if ( $response === false ) {
            throw new FakturoidException(sprintf("cURL failed with error #%d: %s", curl_errno($c), curl_error($c)), curl_errno($c));
        }

        if ( $info['http_code'] >= 400 ) {
            throw new FakturoidException($response, $info['http_code']);
        }

        curl_close($c);

        return array(
            'info' => $info,
            'response' => $json_decode_return ? json_decode($response) : $response,
        );
    }
}
