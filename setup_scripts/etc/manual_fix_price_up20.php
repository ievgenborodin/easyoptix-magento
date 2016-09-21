<?php
  /*
   *  Manually adding frame_material, lens_material for rx products from simple. 
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

  // counter
  $counter = $start_id;


  // fetch attribute set
  $product_attribute_set = $client->call($session_id, 'product_attribute_set.list');
  foreach ($product_attribute_set as $set) {
    if ($set['name'] == 'Eyewear')
      $eyewear_set = $set['set_id'];
  }

$brand_ids = $client->call(
    $session_id,
    "product_attribute.options",
    array(
         'brand'
    )
);

// iterate to get miu miu and ray-ban
foreach($brand_ids as $brand)
{
  if ($brand['label'] == 'Ray-Ban')
    $ray_ban = $brand['value'];
  else if ($brand['label'] == 'Miu Miu')
    $miu_miu = $brand['value'];
}

  // get product list
  $products_list = $client->call($session_id, 'catalog_product.list');
  $products = array();
  $n = 0;
  foreach($products_list as $p)
  {
    // only eyewear set
    if ($p['set'] == $eyewear_set)
    {
      $products[] = $p['sku'];
      $n++;
    }
  }
  echo "Product list set [$n] \n\n";
  

  foreach ($products as $p)
  {
    $curr_product = $client->call($session_id, 'catalog_product.info', $p);

    $brand = $curr_product['brand'];
    if ($brand == $ray_ban || $brand == $miu_miu && $curr_product['price'])
    {
      $old_price = $curr_product['price']; echo 'old: ' . $old_price;
      $price = round($old_price + (0.2 * $old_price)); echo ", new: " . $price . "\n";
      
      $client->call($session_id, 'catalog_product.update', array($p, array(
        'price' => $price
      )));
    }

    echo "[$counter] $p\n";
    $counter++;
  }
  echo "\n";


?>