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
  $start_id = 1;

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
    $children_list = [];
    $unique_list = [];
    $new_children_sku = '';

    // only eyewear set and ray-ban
    if ($p['product_id']>$start_id && $p['set'] == $eyewear_set && substr($p['sku'], 0, 7) == 'ray-ban') //$p['product_id']>=$start_id
    {
      // get info of current item
      $curr_p = $client->call($session_id, 'catalog_product.info', $p['sku']);
      echo '[' . $n++ . '] ';

      // if any children_sku iterate through, set array of unique skus
      $lens_width = $curr_p['lens_width'];
      if (count($lens_width))
      {
        $children_list = explode(',', $lens_width);
        if (count($children_list) > 1)
        {
          echo $p['sku'];
        }
      }
      
      echo "\n";
    }
  }

?>