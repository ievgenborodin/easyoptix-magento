<?php
  
  /*
      version: 0.03 07/21
  */


  // ================
  // vars
  // ================

  // web service access
  $host = 'http://localhost/public_html/index.php';//'https://easy-optix.com/index.php';
  $user = 'kine';
  $pass = '3473309500';//'bClinton1923';
  
  $host .= '/api/soap/?wsdl';

  // database file
  $csv_file = 'db_csv/testing.csv';//'rb-eye-left.csv';

  // first label name (to avoid using header row)
  $header_first_name = 'SKU';

  // columns
  $csv_sku = 0;
  $csv_brand = 3;
  $csv_model = 4;
  $csv_ext = 5;
  $csv_description = 6;
  $csv_bullet_1 = 9;
  $csv_bullet_2 = 10;
  $csv_bullet_3 = 11;
  $csv_bullet_4 = 12;
  $csv_bullet_5 = 13;
  $csv_price = 8;
  $csv_eyewear_type = 7;
  $csv_prescription_ready = 29;
  $csv_gender = 27;
  $csv_style = 22;
  $csv_frame_material = 20;
  $csv_lens_material = 21;
  $csv_polarized = 23;
  $csv_bridge_width = 25;
  $csv_arm_length = 26;
  $csv_lens_width = 24;

  // no time limit
  set_time_limit(0);

  // ================
  // soap connection
  // ================
  try {
    $client = new SoapClient($host);
  	$session_id = $client->login($user, $pass);
  	echo "Soap client connected!\n";

  } catch (SoapFault $fault) {
    echo 'Fault code:'.$fault->faultcode.'<br>';
    echo 'Fault reason:'.$fault->faultstring;
  }

  // ==================
  // useful functions
  // ==================

  function gettrim ($val)
  {
    return isset($val) ? trim($val) : '';
  }

  function lowtrim ($val)
  {
    return isset($val) ? strtolower( trim( $val ) ) : '';
  }

  function uptrim ($val)
  {
    return isset($val) ? strtoupper( trim( $val ) ) : '';
  }

  // ================================
  // selectable attribute options ids
  // ================================
    echo "Getting selectable attribute value ids \n";
    function getOptionsIds($attr_code)
    {
      global $client;
      global $session_id;
      $res = array();

      $attr = $client->call($session_id, 'product_attribute.info', $attr_code);
      $attr_options = $attr["options"];
      if ($attr_options) 
      {
        foreach($attr_options as $opt)
        {
          $label = strtolower($opt["label"]);
          $res[$label] = $opt["value"]; 
        }
      }
      return $res;
    }

    $eyewear_type_arr = getOptionsIds("eyewear_type");
    $style_arr = getOptionsIds("style");
    $brand_arr = getOptionsIds("brand");
    $department_arr = getOptionsIds("department");
    $frame_mat_arr = getOptionsIds("frame_material");
    $lens_mat_arr = getOptionsIds("lens_material");

    // ================================
    // getting categories ids
    // ================================
    //$easy_optix_id = '2';

    $c_men = '3';
    $c_men_type = array(
      'eyeglasses' => '4',
      'sunglasses' => '5',
      'rx' => '6'
    );
    $c_men_brand = '15';
    $c_men_brand_arr = array(
      'ray-ban' =>  '16', 
      'ray ban' =>  '16', 
      'dior'    =>  '17',
      'maui jim'=>  '18',
      'givenchy'=>  '19'
    );
    $c_men_style = '8';
    $c_men_style_arr = array(
      'pilot'     => '9',
      'aviator' => '9',
      'clubmaster'  => '11',
      'round'     => '12',
      'wayfarer'    => '13',
      'rectangular' => '14',
      'square'    => '14',
      'sport'    => '10'
    );
    
    $c_women = '20';
    $c_women_type = array(
      'eyeglasses' => '21',
      'sunglasses' => '22',
      'rx' => '23'
    );
    $c_women_brand = '33';
    $c_women_brand_arr = array(
      'ray-ban' =>  '34',  
      'ray ban' =>  '34',    
      'dior'    =>  '35',
      'maui jim'=>  '36',
      'miu miu' =>  '37',
      'givenchy'=>  '38'
    );
    $c_women_style = '25';
    $c_women_style_arr = array(
      'pilot'     => '26',
      'aviator' => '26',
      'sport'     => '27',
      'clubmaster'  => '28',
      'round'     => '29',
      'wayfarer'    => '30',
      'rectangular' => '31',
      'square'    => '31',
      'cat-eye'     => '32'      
    );  

    $c_brand = '39';
    $c_brand_arr = array(
      'ray-ban' =>  '40', 
      'ray ban' =>  '40', 
      'miu miu' =>  '41',
      'dior'    =>  '42',
      'maui jim'=>  '43',
      'givenchy'=>  '44'
    );
    $c_style = '45';
    $c_style_arr = array(
      'pilot'    => '46',
      'aviator' => '46',
      'sport'    => '47',
      'clubmaster' => '48',
      'round'     => '49',
      'wayfarer'   => '50',
      'rectangular' => '51',
      'square'    => '51',
      'cat-eye'     => '52'
    );

    // ================================
    // colors catch
    // ================================
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

  $colors_separator = ',';

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

    // ================================
    // opening csv file
    // ================================
    echo "Opening csv file: $csv_file\n";

    ini_set('auto_detect_line_endings',TRUE);
    $fp = fopen($csv_file, 'r');

    // fetch attribute set
    $product_attribute_set = $client->call($session_id, 'product_attribute_set.list');
    foreach ($product_attribute_set as $set) {
      if ($set['name'] == 'Eyewear')
        $my_attribute_set_id = $set['set_id'];
    }

    if (empty($my_attribute_set_id))
      die('This list is empty');

    // init diff models arr
    $diff_model_arr = array(); 

    // get existing list of sku based on attribute set and starting id
    $products_list = $client->call($session_id, 'catalog_product.list');
    $sku_arr = array();
    $sku_count = 0; $unique_count = 0; $count_c = 0; echo count($products_list) . "\n";
    foreach($products_list as $pr)
    {
      $_split = explode('_',$pr['sku']);
      // only eyewear set
      if ($pr['set'] == $my_attribute_set_id && $_split[0]!='rx')
      {
        if (!in_array($pr['sku'], $sku_arr)){
          $sku_arr[] = $pr['sku'];
          $sku_count++;
        }
        
        $unique_model = 'u' . $_split[0] . $_split[1];
        if (!isset($diff_model_arr[$unique_model])){
          $diff_model_arr[$unique_model] = $pr['sku'];
          $unique_count++; echo $count_c++ . ' ';
        }
      }
    }
    echo "\n Unique skus [$sku_count] \n Unique models [$unique_count] \n";

    $iterator = 0;
    while ( ($data = fgetcsv($fp) ) !== FALSE )  //while (($data = fgetcsv($fp, 1000, ',')) !== false)
    {
      // not to use header line
      if ($data[0] != $header_first_name) 
      {
        $new_parent_product = false;
        echo "[$iterator] ";
        $iterator++;

        // data from csv
        $brand = lowtrim( $data[$csv_brand] );
        $brand_model = uptrim( $data[$csv_model] );
        $ext = lowtrim( $data[$csv_ext] );
        $description = gettrim( $data[$csv_description] );
        $short_description = isset($data[$csv_bullet_1]) ? $data[$csv_bullet_1] . "\n" : '';
        $short_description .= isset($data[$csv_bullet_2]) ? $data[$csv_bullet_2] . "\n"  : '';
        $short_description .= isset($data[$csv_bullet_3]) ? $data[$csv_bullet_3] . "\n"  : '';
        $short_description .= isset($data[$csv_bullet_4]) ? $data[$csv_bullet_4] . "\n"  : '';
        $short_description .= isset($data[$csv_bullet_5]) ? $data[$csv_bullet_5] : '';
        $price = isset($data[$csv_price]) ? trim( $data[$csv_price] ) : '0';
        $eyewear_type = lowtrim( $data[$csv_eyewear_type] ); 
        $prescription_ready = isset( $data[$csv_prescription_ready] ) ? '1' : '0';
        $department = lowtrim( $data[$csv_gender] );
        $style = lowtrim( $data[$csv_style] );
        $frame_type = lowtrim( $data[$csv_frame_material] );  
        $lens_type = lowtrim( $data[$csv_lens_material] );
        $polarized = isset($data[$csv_polarized]) ? ((trim( $data[$csv_polarized] )) == 'polarized') ? '1' : '0' : '';
        $lens_width = gettrim( $data[$csv_lens_width] );
        $bridge_width = gettrim( $data[$csv_bridge_width] );
        $arm_length = gettrim( $data[$csv_arm_length] );
        
        // defaults 
        $visibility = '3';
        $model_parent = '0';
        
        //  set colors 
        preg_match_all($pattern, $short_description, $colors);
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
            $curr_colors .= ($tmp_c . $colors_separator);
          }
        }
        $curr_colors = substr($curr_colors, 0, -1);

        // small modifications
        $brand_model = substr($brand_model, 1);
        $ext = substr($ext, 1);
        if ($brand=='ray ban') $brand = 'ray-ban';
        
          // style errors 
        switch ($style)
        {
          case 'aviator':
            $style = 'pilot';
            break;
          case 'cateye':
          case 'butterfly':
            $style = 'cat-eye';
            break;
          case 'oval':
            $style = 'round';
            break;
          case 'square':
          case 'semi-rimless':
            $style = 'rectangular';
            break;
        }

        switch ($department) 
        {
          case 'unisex-adult':
            $department = 'unisex';
            break;
          case 'womens':
            $department = 'women';
            break;
          case 'mens':
            $department = 'men';
            break;
        }
        if ($style == 'aviator') $style = 'pilot';     
        $eyewear_type = ($eyewear_type == 'sunglasses') ? 'sunglasses' : 'eyeglasses';
        $name = ucwords(ucwords($brand, '-')) . ' ' . $brand_model;
        if ($brand == 'christian dior' || $brand == 'dior homme') $brand = 'dior';
        $tmp_brand = implode("-", explode(" ", $brand));
        $url_key = strtolower( $brand_model . '-' . $ext );
        $sku = strtolower( $tmp_brand . '_' . $brand_model . '_' . $ext );

        // ids attributes
        $eyewear_type_id = isset($eyewear_type_arr[$eyewear_type]) ? $eyewear_type_arr[$eyewear_type] : '';
        $style_id = isset($style_arr[$style]) ? $style_arr[$style] : '';
        $department_id = (isset($department_arr[$department])) ? $department_arr[$department] : '';
        $brand_id = isset($brand_arr[$brand]) ? $brand_arr[$brand] : '';
        $frame_type_id = isset($frame_mat_arr[$frame_type]) ? $frame_mat_arr[$frame_type] : '';        
        $lens_type_id = isset($lens_mat_arr[$lens_type]) ? $lens_mat_arr[$lens_type] : '';
        
        // test on unique model (parent / child)
        $unique_model = 'u' . strtolower($tmp_brand) . strtolower($brand_model);
        if (!isset($diff_model_arr[$unique_model]))
        {
          // if unique adds to array
          $diff_model_arr[$unique_model] = $sku;
          $new_parent_product = true;
          $visibility = '4';
        }
        else 
        {
          // sku check (lens width)
          if (in_array($sku, $sku_arr))
          {
            // get previous item + update lens_width
            $previous_item = $client->call($session_id, 'catalog_product.info', $sku);
            $previous_lens_width = ($previous_item['lens_width'] != '') ? $previous_item['lens_width'] . ',' : '';

            $lens_width_arr = explode(',', $previous_lens_width);
            if (!in_array($lens_width, $lens_width_arr)){
              // update lens_width attribute
              $client->call($session_id, 'catalog_product.update', array($sku, array(
                'lens_width' => $previous_lens_width . $lens_width,
              )));  

              echo "Added lens size variation: $name. \n";
            }
            // skip this csv row 
            continue;
          }
          else 
          {
            $sku_arr[] = $sku;
          }

          // if not requests children sku array and adds additional child
          $tempSku = $diff_model_arr[$unique_model];
          $newSku = strtolower( $tmp_brand . '_' . $brand_model );
          $tempItem = $client->call($session_id, 'catalog_product.info', $tempSku);
          $old_children_sku = $tempItem['children_sku'];
          $updating_data = array();
          if ($tempSku != $newSku) 
          {
            echo "shit happens: $tempSku, $newSku \n";
           $old_children_sku = $newSku; 
           $updating_data['sku'] = $newSku;
           $updating_data['model_parent'] = '1';
           $updating_data['url_key'] = strtolower( $brand_model );
           $diff_model_arr[$unique_model] = $newSku;
          }

          // check if this sku in array
          $updating_data['children_sku'] = (!in_array($sku, explode(' ', $old_children_sku))) ? $old_children_sku . ' ' . $sku : $old_children_sku;
          
          // update values
          $client->call($session_id, 'catalog_product.update', array($tempSku, $updating_data));
          $model_parent = '1';
        }

        // categories
        $c_ids = array();

        if ($new_parent_product){
          array_push($c_ids, $c_brand, $c_style);   

          if (isset($c_style_arr[$style]))
          { 
            $c_ids[] = $c_style_arr[$style];
          }

          if (isset($c_brand_arr[$brand]))
          {
            $c_ids[] = $c_brand_arr[$brand];
          }

          if ($department == 'men' || $department == 'unisex'){
            
            array_push($c_ids, $c_men, $c_men_style, $c_men_brand);

            if (isset($c_men_style_arr[$style])) 
            {
              $c_ids[] = $c_men_style_arr[$style];
            }
            
            if (isset($c_men_brand_arr[$brand]))
            {
              $c_ids[] = $c_men_brand_arr[$brand];
            }
            
            if (isset($c_men_type[$eyewear_type]))
            {
              $c_ids[] = $c_men_type[$eyewear_type];
            }

          }
          if ($department == 'women' || $department == 'unisex'){
            
            array_push($c_ids, $c_women, $c_women_style, $c_women_brand); 
            
            if (isset($c_women_style_arr[$style])) 
            {
              $c_ids[] = $c_women_style_arr[$style];
            }
            
            if (isset($c_women_brand_arr[$brand]))
            {
              $c_ids[] = $c_women_brand_arr[$brand];
            }
            
            if (isset($c_women_type[$eyewear_type]))
            {
              $c_ids[] = $c_women_type[$eyewear_type];
            }

          }
          
        }
        // ================================
        // creating product
        // ================================
        $rx_meta = ($prescription_ready) ? ' Rx Available Prescription Sunglasses' : '';
        $shape_meta = ($style) ? $style . ' shape ' : '';
        $meta_keys = $name . ' ' . $department . ' Fashion ' . $eyewear_type . $rx_meta . ' ' . $shape_meta . ' ' . $frame_type . ' frame free shipping gift';
        $sort_style = ($style == '') ? 'r' : ucfirst($style);
        $sort_frame = ($frame_type == '') ? 'r' : ucfirst($frame_type);
        $sort_lens = ($lens_type == '') ? 'r' : ucfirst($lens_type);
        $sord_department = ($department == 'unisex') ? 'Men,Women' : ucfirst($department);
        $sort_colors = ($curr_colors == '') ? 'r' : $curr_colors;
        $sort_params = ucfirst($eyewear_type) .'*'. $prescription_ready .'*'. $sord_department .'*'. $sort_style .'*'. ucwords(ucwords($brand, '-')) .'*'. $brand_model .'*'. $ext .'*'. $sort_frame .'*'. $sort_lens .'*'. $sort_colors .'*'. $polarized .'*'.$lens_width;
        $new_product_id = $client->call($session_id, 'catalog_product.create', 
          array(
            'simple', 
            $my_attribute_set_id, 
            $sku, 
            array(
              "name" => $name,
              "url_key" => $url_key, 
              "categories" => $c_ids,
              "description" => $description, 
              "short_description" => $short_description, 
              "weight" => '6.4000', 
              "status" => '1', 
              "visibility" => $visibility, 
              "price" => $price, 
              "tax_class_id" => '2', 
              "stock_availabitily" => '1', 
              "eyewear_type" => $eyewear_type_id, 
              "prescription_ready" => $prescription_ready, 
              "style" => $style_id, 
              "brand" => $brand_id, 
              "brand_model" => $brand_model,
              "brand_ext" => $ext,
              "department" => $department_id,
              "model_parent" => $model_parent,
              "children_sku" => '',
              "frame_material" => $frame_type_id, 
              "lens_material" => $lens_type_id, 
              "polarized" => $polarized, 
              "bridge_width" => $bridge_width, 
              "arm_length" => $arm_length, 
              "lens_width" => $lens_width, 
              'meta_title' => $name,
              'meta_keyword' => $meta_keys,
              'meta_description' => $short_description,
              'colors' => $curr_colors,
              'sort_params' => $sort_params,
              "stock_data" => array(
                'qty' => '999',
                'is_in_stock' => 1
              )
            )
          )
        );

        echo "$name added\n";
      }
    }

    ini_set('auto_detect_line_endings',FALSE);
    fclose($fp);
  
  	echo "\ndone\n";

?>
