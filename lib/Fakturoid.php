<?php

class FakturoidException extends Exception { }

/* Safety */

if (!function_exists('curl_init')) {
  throw new FakturoidException('Fakturoid lib needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new FakturoidException('Fakturoid lib needs the JSON PHP extension.');
}

class Fakturoid {
  private $subdomain;
  private $api_key;
  private $email;
  private $user_agent;
    
  public function __construct($subdomain, $email, $api_key, $user_agent) {
    $this->subdomain  = $subdomain;
    $this->email      = $email;    
    $this->api_key    = $api_key;
    $this->user_agent = $user_agent;
  }
  
  /* Account */
  
  public function get_account() {
    return $this->get('/account.json');
  }
  
  /* User */
  
  public function get_user($id) {
    return $this->get("/users/$id.json");
  }
  
  public function get_users($options = NULL) {
    return $this->get('/users.json'. $this->convert_options($options, array('page')));
  }
  
  /* Invoice */
  
  public function get_invoices($options = NULL) {
    return $this->get("/invoices.json" . $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page', 'status')));
  }
  
  public function get_regular_invoices($options = NULL) {
    return $this->get('/invoices/regular.json'. $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page', 'status')));
  }
  
  public function get_proforma_invoices($options = NULL) {
    return $this->get('/invoices/proforma.json'. $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page', 'status')));
  }
  
  public function get_invoice($id) {
    return $this->get("/invoices/$id.json");
  }
  
  public function get_invoice_pdf($id){
  	return $this->run("/invoices/$id/download.pdf", 'get', NULL, false);
  }
  
  public function search_invoices($options = NULL) {
    return $this->get("/invoices/search.json" . $this->convert_options($options, array('query', 'page')));
  }
  
  public function update_invoice($id, $data) {
    return $this->patch("/invoices/$id.json", $data);
  }
  
  public function fire_invoice($id, $event, $options = array()) {
    return $this->post("/invoices/$id/fire.json", array_merge(array('event' => $event), $options));
  }

  public function create_invoice($data) {
    return $this->post('/invoices.json', $data);
  }
  
  public function delete_invoice($id) {
    return $this->delete("/invoices/$id.json");
  }
    
  /* Expense */
  
  public function get_expenses($options = NULL) {
    return $this->get("/expenses.json" . $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page', 'status')));
  }
  
  public function get_expense($id) {
    return $this->get("/expenses/$id.json");
  }
  
  public function search_expenses($options = NULL) {
    return $this->get("/expenses/search.json" . $this->convert_options($options, array('query', 'page')));
  }
  
  public function update_expense($id, $data) {
    return $this->patch("/expenses/$id.json", $data);
  }
  
  public function fire_expense($id, $event, $options = array()) {
    return $this->post("/expenses/$id/fire.json", array_merge(array('event' => $event), $options));
  }

  public function create_expense($data) {
    return $this->post('/expenses.json', $data);
  }
  
  public function delete_expense($id) {
    return $this->delete("/expenses/$id.json");
  }
  
  /* Subject */
  
  public function get_subjects($options = NULL) {
    return $this->get('/subjects.json' . $this->convert_options($options, array('since', 'updated_since', 'page', 'custom_id')));
  }
  
  public function get_subject($id) {
    return $this->get("/subjects/$id.json");
  }
  
  public function create_subject($data) {
    return $this->post('/subjects.json', $data);
  }
  
  public function update_subject($id, $data) {
    return $this->patch("/subjects/$id.json", $data);
  }
  
  public function delete_subject($id) {
    return $this->delete("/subjects/$id.json");
  }
 
  public function search_subjects($options = NULL) {
    return $this->get('/subjects/search.json' . $this->convert_options($options, array('query')));
  }
  
  /* Generator */
  
  public function get_generators($options = NULL) {
    return $this->get('/generators.json' . $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page')));
  }
  
  public function get_template_generators($options = NULL) {
    return $this->get('/generators/template.json' . $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page')));
  }
  
  public function get_recurring_generators($options = NULL) {
    return $this->get('/generators/recurring.json' . $this->convert_options($options, array('subject_id', 'since', 'updated_since', 'page')));
  }
  
  public function get_generator($id) {
    return $this->get("/generators/$id.json");
  }

  public function create_generator($data) {
    return $this->post('/generators.json', $data);
  }
  
  public function update_generator($id, $data) {
    return $this->patch("/generators/$id.json", $data);
  }
  
  public function delete_generator($id) {
    return $this->delete("/generators/$id.json");
  }
  
  /* Message */
  
  public function create_message($id, $data) {
    return $this->post("/invoices/$id/message.json", $data);
  }

  /* Event */
  
  public function get_events($options = NULL) {
    return $this->get('/events.json' . $this->convert_options($options, array('subject_id', 'since', 'page')));
  }
  
  public function get_paid_events($options = NULL) {
    return $this->get('/events/paid.json' . $this->convert_options($options, array('subject_id', 'since', 'page')));
  }
  
  /* Todo */
  
  public function get_todos($options = NULL) {
    return $this->get('/todos.json' . $this->convert_options($options, array('subject_id', 'since', 'page')));
  }  
  
  /* Helper functions */
  
  private function get($path) {
    return $this->run($path, 'get');
  }
  
  private function post($path, $data) {
    return $this->run($path, 'post', $data);
  }
  
  private function put($path, $data) {    
    return $this->run($path, 'put', $data);
  }
  
  private function patch($path, $data) {    
    return $this->run($path, 'patch', $data);
  }
  
  private function delete($path) {    
    return $this->run($path, 'delete');
  }
  
  /* Query building */
  
  private function convert_options($options, $allowed) {
    $safe_options = array();
    foreach ($allowed as $key) {
      if (isset($options[$key])) {
        $safe_options[$key] = $options[$key];
      } else {
        $safe_options[$key] = NULL;
      }
    }
    if (!empty($safe_options)) {
      return "?" . http_build_query($safe_options);
    }
  }
  
  /** 
   * Execute HTTP method on path with data
   */
  private function run($path, $method, $data = NULL, $json_decode_return = true) {    
    $c = curl_init();
    
    if ($c === FALSE) {
      throw new FakturoidException('cURL failed to initialize.');
    }
    
    curl_setopt($c, CURLOPT_URL, "https://app.fakturoid.cz/api/v2/accounts/$this->subdomain$path");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($c, CURLOPT_FAILONERROR, FALSE); // to get error messages in response body
    curl_setopt($c, CURLOPT_USERPWD, "$this->email:$this->api_key");
    curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($c, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      
    if ($method == 'post') {
      curl_setopt($c, CURLOPT_POST, TRUE);
      curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
    }
    if ($method == 'put') {
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
    }
    if ($method == 'patch') {
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, "PATCH");
      curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
    }
    if ($method == 'delete') {
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, "DELETE");
    }    
    
    $response = curl_exec($c);
    $info = curl_getinfo($c);
    
    if ($response === FALSE) {
      throw new FakturoidException(sprintf("cURL failed with error #%d: %s", curl_errno($c), curl_error($c)), curl_errno($c));
    }
     
    if ($info['http_code'] >= 400) {
      throw new FakturoidException($response, $info['http_code']); 
    }
    curl_close($c);
    return $json_decode_return ? json_decode($response) : $response;
  }

}
?>