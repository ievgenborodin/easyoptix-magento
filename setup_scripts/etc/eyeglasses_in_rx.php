<?php
  /*
   *  Manually fixing RX glasses categories 
   *  version: 1.0.0
  **/

  $host = 'https://easy-optix.com/index.php';
  $user = 'kine';
  $pass = 'bClinton1923';
  
  $host .= '/api/soap/?wsdl';

  $client = new SoapClient($host);

  try {
    $session_id = $client->login($user, $pass);
    echo "connected\n";

  } catch (SoapFault $fault) {
    echo 'Fault code:'.$fault->faultcode.'<br>';
    echo 'Fault reason:'.$fault->faultstring;
  }

  // start with id
  $start_id = 471;

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
    if ($p['product_id']>$start_id && $p['set'] == $eyewear_set && substr($p['sku'], 0, 10) == 'rx_ray-ban') //$p['product_id']>=$start_id
    {
      $curr_p = $client->call($session_id, 'catalog_product.info', $p['sku']);
      echo '[' . $n++ . '] ';

      $eyewear_type = $curr_p['eyewear_type'];
      $curr_cats = count($curr_p['categories']);

      if ($eyewear_type==4 && $curr_cats)
      {
        $client->call($session_id, 'catalog_product.update', array($p['sku'], array(
          'categories' => array()
        )));
        echo $p['sku'];
      }
      echo "\n";
    }
  }

?>