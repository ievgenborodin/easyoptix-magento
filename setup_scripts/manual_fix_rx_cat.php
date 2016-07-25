<?php
  /*
   *  Manually fixing RX glasses categories 
   *  version: 1.0.0
  **/

  $client = new SoapClient('https://easy-optix.com/index.php/api/soap/?wsdl');

  try {
    $session_id = $client->login('kine', 'bClinton1923');
    echo "connected\n";

  } catch (SoapFault $fault) {
    echo 'Fault code:'.$fault->faultcode.'<br>';
    echo 'Fault reason:'.$fault->faultstring;
  }

  // start with id
  $start_id = 1;

  // rx cat ids
  $rx_ids = array(
    '6' => array('23'),
    '7' => array('6', '23')
  );

  // counter
  $counter = $start_id;

  // fetch attribute set
  $product_attribute_set = $client->call($session_id, 'product_attribute_set.list');
  foreach ($product_attribute_set as $set) {
    if ($set['name'] == 'Eyewear')
      $eyewear_set = $set['set_id'];
  }

  // get product list
  $products_list = $client->call($session_id, 'catalog_product.list');
  $products = array();
  $n = 0;
  foreach($products_list as $p)
  {
    // only eyewear set and rx
    if ($p['set'] == $eyewear_set && substr($p['sku'], 0, 10) == 'rx_miu-miu') //$p['product_id']>=$start_id
    {
      echo $n++ . "\n";

      $curr_p = $client->call($session_id, 'catalog_product.info', $p['sku']);
      
      $children_sku = trim($curr_p['children_sku']);
      $model_parent = $curr_p['model_parent'];

      if (!$children_sku && !$model_parent || $children_sku && $model_parent)
        $c_ids = $rx_ids[$curr_p['department']];
      else 
        $c_ids = array();

      $client->call($session_id, 'catalog_product.update', array($p['sku'], array(
        'categories' => $c_ids
      )));
    }
  }

?>