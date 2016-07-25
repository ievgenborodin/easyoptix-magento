<?php
  /* version 0.01 */

  $host = 'https://easy-optix.com/index.php/api/soap/?wsdl';
  $user = 'kine';
  $pass = 'bClinton1923';

  $colors_list = array(
    'blue', 'bronze', 'beige','brown','black', 'burgundy',
    'cognac', 
    'gold', 'green','gunmetal', 'gray', 'grey',
    'havana',
    'ivory', 
    'khaki',
    'lilac',
    'multi','metallic',
    'opal', 'orange',
    'purple','pink','palladium',
    'red', 'rose',
    'silver','sand',
    'tortoise', 
    'violet', 
    'white',
    'yellow'
  );

  $separator = ',';

  // set pattern
  $pattern_colors = '';
  $or = '|';
  foreach($colors_list as $color)
  {
    $pattern_colors .= ($color . $or);
  }
  // cut last 'or'
  $pattern_colors = substr($pattern_colors, 0, -1);

  $pattern = '/' . $pattern_colors . '/i';
  echo $pattern. "\n";

  // connecting
  $client = new SoapClient($host);

  try {
    $session_id = $client->login($user, $pass);
    echo "connected\n";

  } catch (SoapFault $fault) {
    echo 'Fault code:'.$fault->faultcode.'<br>';
    echo 'Fault reason:'.$fault->faultstring;
  }

  // fetch attribute set
  $product_attribute_set = $client->call($session_id, 'product_attribute_set.list');
  foreach ($product_attribute_set as $set) {
    if ($set['name'] == 'Eyewear')
      $eyewear_set = $set['set_id'];
  }
  echo "\n set=$eyewear_set \n";

  // get product list
  $products_list = $client->call($session_id, 'catalog_product.list');
  $products = array();
  $n = 0;
  foreach($products_list as &$p)
  {
    // only eyewear set
    if ($p['set'] == $eyewear_set)
    {
      $products[] = $p['product_id'];echo $p['product_id'] . " ";
      $n++;
    }
  }
  echo "Product list set [$n] \n\n";

  // iterate products, get filtering attr values (iterator), set sorting data [$sort_params]
  foreach ($products as $p)
  {
    $curr_product = $client->call($session_id, 'catalog_product.info', $p);

    $curr_short_description = $curr_product['short_description'];
    
    preg_match_all($pattern, $curr_short_description, $colors);
    // capitalize colors, check on unique, set string of colors (iterate)
    $curr_colors = '';
    $unique_arr = array();
    foreach ($colors[0] as $c)
    {
      $tmp_c = ucfirst($c); 

      // exceptions
      switch ($tmp_c) {
        case 'Rose':
          $tmp_c = 'Pink';
          break;
        case 'Violet':
        case 'Lilac':
          $tmp_c = 'Purple';
          break;
        case 'Cognac':
        case 'Bronze':
        case 'Beige':
        case 'Khaki':
          $tmp_c = 'Brown';
          break;
        case 'Metallic':
        case 'Silver':
        case 'Gunmetal':
        case 'Gray':
          $tmp_c = 'Grey';
          break;
        case 'Ivory':
        case 'Sand':
          $tmp_c = 'Yellow';
          break;
        case 'Tortoise':
        case 'Opal':
          $tmp_c = 'Blue';
          break;
        case 'Palladium':
          $tmp_c = 'White';
          break;
        case 'Burgundy':
          $tmp_c = "Red";
          break;
      }

      if (!in_array($tmp_c, $unique_arr))
      {
        $unique_arr[] = $tmp_c;
        $curr_colors .= ($tmp_c . $separator);
      }
    }
    $curr_colors = substr($curr_colors, 0, -1);

    // set $sort_params
    $client->call($session_id, 'catalog_product.update', array($p, array(
      'colors' => $curr_colors
    )));

    echo $p . "\n";
  }
  echo "\n";


?>


