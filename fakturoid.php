<?php

/* Safety */

if (!function_exists('curl_init')) {
  throw new Exception('Fakturoid lib needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Fakturoid lib needs the JSON PHP extension.');
}

class Fakturoid {
  private $subdomain;
  private $api_key;
  private $user_agent;
    
  public function __construct($subdomain, $api_key, $user_agent) {
    $this->subdomain  = $subdomain;
    $this->api_key    = $api_key;
    $this->user_agent = $user_agent;
  }
  
  /* Account */
  
  public function get_account() {
    return $this->get('/account.json');
  }
  
  /* Invoice */
  
  public function get_invoices($options = NULL) {
    return $this->get("/invoices.json" . $this->convert_options($options, array('subject_id', 'since', 'page')));
  }
  
  public function get_regular_invoices($options = NULL) {
    return $this->get('/invoices/regular.json'. $this->convert_options($options, array('subject_id', 'since', 'page')));
  }
  
  public function get_proforma_invoices($options = NULL) {
    return $this->get('/invoices/proforma.json'. $this->convert_options($options, array('subject_id', 'since', 'page')));
  }
  
  public function get_invoice($id) {
    return $this->get("/invoices/$id.json");
  }
  
  public function update_invoice($id, $data) {
    return $this->put("/invoices/$id.json", $data);
  }
  
  public function fire_invoice($id, $event, $options = NULL) {
    return $this->post("/invoices/$id/fire.json", array_merge(array('event' => $event), $options));
  }

  public function create_invoice($data) {
    return $this->post('/invoices.json', $data);
  }
  
  public function delete_invoice($id) {
    return $this->delete("/invoices/$id.json");
  }
  
  /* Subject */
  
  public function get_subjects($options = NULL) {
    return $this->get('/subjects.json' . $this->convert_options($options, array('since')));
  }
  
  public function get_subject($id) {
    return $this->get("/subjects/$id.json");
  }
  
  public function create_subject($data) {
    return $this->post('/subjects.json', $data);
  }
  
  public function update_subject($id, $data) {
    return $this->put("/subjects/$id.json", $data);
  }
  
  public function delete_subject($id) {
    return $this->delete("/subjects/$id.json");
  }
  
  /* Generator */
  
  public function get_generators($options) {
    return $this->get('/generators.json' . $this->convert_options($options, array('subject_id', 'since')));
  }
  
  public function get_generator($id) {
    return $this->get("/generators/$id.json");
  }

  public function create_generator($data) {
    return $this->post('/generators.json', $data);
  }
  
  public function update_generator($id, $data) {
    return $this->put("/generators/$id.json", $data);
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
    return $this->get('/events.json') . $this->convert_options($options, array('subject_id', 'since', 'page'));
  }
  
  public function get_paid_events($options = NULL) {
    return $this->get('/events/paid.json') . $this->convert_options($options, array('subject_id', 'since', 'page'));
  }
  
  /* Todo */
  
  public function get_todos($options = NULL) {
    return $this->get('/todos.json') . $this->convert_options($options, array('subject_id', 'since', 'page'));
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
  
  private function delete($path) {    
    return $this->run($path, 'delete');
  }
  
  /* Query building */
  
  private function convert_options($options, $allowed) {
    $safe_options = array();
    foreach ($allowed as $key) {
      $safe_options[$key] = $options[$key];
    }
    if (!empty($safe_options)) {
      return "?" . http_build_query($safe_options);
    }
  }
  
  /** 
   * Execute HTTP method on path with data
   */
  private function run($path, $method, $data = NULL) {    
    $c = curl_init();
    
    curl_setopt($c, CURLOPT_URL, "https://$this->subdomain.fakturoid.cz/api/v1$path");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($c, CURLOPT_FAILONERROR, FALSE); // to get error messages in response body
    curl_setopt($c, CURLOPT_USERPWD, "$this->subdomain:$this->api_key");
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
    if ($method == 'delete') {
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, "DELETE");
    }    
    
    $response = curl_exec($c);
    $info = curl_getinfo($c);
    
    if ($info['http_code'] >= 400) {
      throw new Exception($response, $info['http_code']); 
    }	
    curl_close($c);
    return json_decode($response);
  }

}
?>