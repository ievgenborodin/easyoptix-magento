<?php
  /*
   *  Manually adding frame_material, lens_material for rx products from simple. 
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
  $start_id = 484;

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
    // only eyewear set
    if ($p['set'] == $eyewear_set && $p['product_id']>=$start_id)
    {
      $products[] = $p['sku'];
      $n++;
    }
  }
  echo "Product list set [$n] \n\n";
  
  echo "Copying lens mat and frame mat: \n";
  foreach ($products as $p)
  {
    $curr_product = $client->call($session_id, 'catalog_product.info', $p);

    $simple_sku = substr($curr_product['sku'], 3);
    $curr_simple = $client->call($session_id, 'catalog_product.info', $simple_sku);
    $curr_frame_mat = $curr_simple['frame_material'];
    $curr_lens_mat = $curr_simple['lens_material'];

    // set $sort_params
    $client->call($session_id, 'catalog_product.update', array($p, array(
      'frame_material' => $curr_frame_mat,
      'lens_material' => $curr_lens_mat
    )));

    echo "[$counter] $p\n";
    $counter++;
  }
  echo "\n";


?>