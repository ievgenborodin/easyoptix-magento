<?php
  /*
   *  Collects data creating one source attribute for filtering 
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

  // list of attributes needed for filtering
  $filter_attributes = array(
    'eyewear_type',
    'prescription_ready',
    'department',
    'style',
    'brand',
    'brand_model',
    'brand_ext',
    'frame_material',
    'lens_material',
    'colors',
    'polarized',
    'lens_width'
  );

  // separator
  $separator = '*';

  // start with id
  $start_id = 353;

  // counter
  $counter = $start_id;

  // fetch attribute set
  $product_attribute_set = $client->call($session_id, 'product_attribute_set.list');
  foreach ($product_attribute_set as $set) {
    if ($set['name'] == 'Eyewear')
      $eyewear_set = $set['set_id'];
  }

  // transform array 
  function key_arr_to_key_val($arr)
  {
    if (!count($arr))
      return $arr;
    
    $new_arr = array();
    foreach ($arr as $k => $v) 
    {
        // exceptions
        $val = $v['label'];
        switch ($val)
        {
          case 'Unisex':
            $val = 'Men,Women';
            break;
          case 'Yes':
            $val = '1';
            break;
          case 'No':
            $val = '0';
            break;
        }

        $new_arr[$v['value']] = $val;
    }
    return $new_arr;
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

  // get attributes options (iterator)
  echo "Setting attributes options: \n";
  $attr_list = array();
  foreach($filter_attributes as $a)
  {
    $options = $client->call($session_id, 'catalog_product_attribute.options', $a);
    $attr_list[$a] = key_arr_to_key_val($options);
    echo "$a set \n";
  }
  echo "\n";

  
  // iterate products, get filtering attr values (iterator), set sorting data [$sort_params]
  echo "Setting filtering data: \n";
  foreach ($products as $p)
  {
    $curr_product = $client->call($session_id, 'catalog_product.info', $p);

    $curr_sort_params = '';
    foreach($attr_list as $attr_code => $attr_options)
    {
      $v = trim($curr_product[$attr_code]);
      if (isset($v) && $v!=''){ 
        $k = (count($attr_options)) ? $attr_options[$v] : $v;
        $curr_sort_params .= $k . $separator;
      }
      else 
      {  
        $curr_sort_params .= 'r' . $separator;
      }
    }
    $curr_sort_params = substr($curr_sort_params, 0, -1);

    // set $sort_params
    $client->call($session_id, 'catalog_product.update', array($p, array(
      'sort_params' => $curr_sort_params
    )));

    echo "[$counter] $p\n";
    $counter++;
  }
  echo "\n";


?>