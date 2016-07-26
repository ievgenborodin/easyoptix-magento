<?php
  
  /*
      populate children images
  */

  // web service access
  $host = 'http://localhost/public_html/index.php';//'https://easy-optix.com/index.php';
  $user = 'kine';
  $pass = '3473309500';//'bClinton1923';
  
  $host .= '/api/soap/?wsdl';

  // image path & extension
  $images_folder = 'db_photos';
  $image_type = '.jpg';  

  // starting ID
  $start_id = 868; 

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
 
  function addImage($item_id, $item_label, $image_type, $image_name, $image_file_name, $position = '0')
  {
          // image data
          $child = (substr($item_label, 0, 3) == 'ext') ? true : false;
          $exclude = ($child || $image_type == 'thumbnail' || $image_type == 'small_image') ? 1 : 0;
          $data = array(
            'file' => array(
              'content' => base64_encode(file_get_contents($image_name)),
              'mime' => 'image/jpeg',
              'name' => $image_file_name
            ),
            'label' => $item_label,
            'types' =>  array($image_type),
            'exclude' => $exclude,
            'position' => $position
          );

          // call create image
          global $client;
          global $session_id;
          $results = $client->call( $session_id, 'catalog_product_attribute_media.create', array($item_id, $data) );
  }

   $eyewear_types = getOptionsIds('eyewear_type');

   // get all products
   $products = $client->call($session_id, 'catalog_product.list');

   // itarate through products
   $index = 0;
   foreach ($products as $p) 
   {
      // getting info from current product
      $curr_product_id = $p['product_id'];
      echo "$index. [id: $curr_product_id] \n";
      $index++;

      // skip unnecessary items
      if ($curr_product_id < $start_id)
      {
        continue;
      }

      $curr_product = $client->call($session_id, 'catalog_product.info', $curr_product_id);

      // get eyewear type of the parent
      $eyewear_type = array_search($curr_product['eyewear_type'], $eyewear_types);

      // ================================
      // adding images
      // ================================

        $_sku = explode("_", $p['sku']);

        // rx check
        $rx = false;
        if ($_sku[0]=='rx') 
        {
          array_splice($_sku, 0, 1);
          $rx = true;
        }

        $_brand = $_sku[0]; 
        $_serie = $_sku[1];
        $_ext = (isset($_sku[2])) ? $_sku[2] : $curr_product['brand_ext'];

        // image path
        $img_path = $images_folder . '/' . $_brand . '/' . $eyewear_type . '/' . $_serie;
        $image_file_name = $_brand[0] . $_serie . '_' . $_ext;
        $images_num = 0;
        $name = ($rx==true) ? "rx $_brand $_serie $_ext" : "$_brand $_serie $_ext";

        for ($i=6; $i>0; $i--)
        {
          $file = $img_path . '/' . $_ext  . '_' . $i . $image_type;
          if (file_exists($file))
          {
            addImage($curr_product_id, $name, 'image', $file, $image_file_name . '_' . $i, $i);
            $images_num++; 
          };
        };

        $file = $img_path . '/' . 'small_image/' . $_ext . $image_type;
        if (file_exists($file))
        {
          addImage($curr_product_id, $name, 'small_image', $file, 's' . $image_file_name);
          echo "\t$name. Small image added \n";
        }
        else 
        {
          echo "Small_image img for $name not found\n $file \n";
        }
        
        $file = $img_path . '/' . 'thumbnail/' . $_ext . $image_type;
        if (file_exists($file))
        {
          addImage($curr_product_id, $name, 'thumbnail', $file, 't' . $image_file_name);
          echo "\t$name. Thumbnail added \n";
        }
        else 
        {
          echo "Thumbnail img for $name not found\n $file \n";
        }
        
        echo "\t$name. Images count: $images_num \n";


      // check if current product has children models
      if (!isset($curr_product['children_sku']))
      {
         echo "\tno children\n";
         continue;
      }

      $curr_children = strtolower( trim($curr_product['children_sku'] ));
      if (count($curr_children))
      {  
         // get list of children sku
         $children_sku = explode(" ", $curr_children);
         foreach($children_sku as $c)
         {
            // break sku
            $child_sku = explode("_", $c);

            // rx check
            $rx = false;
            if ($child_sku[0]=='rx') 
            {
              array_splice($child_sku, 0, 1);
              $rx = true;
            }

            $_brand = $child_sku[0]; 
            $_serie = $child_sku[1];
            $_ext = (isset($child_sku[2])) ? $child_sku[2] : $curr_product['brand_ext'];

            // image path
            $img_path = $images_folder . '/' . $_brand . '/' . $eyewear_type . '/' . $_serie . '/small_image';
            $image_file_name = 's' . $_brand[0] . $_serie . '_' . $_ext;

            // file name 
            $file = $img_path . '/' . $_ext . $image_type;

            // if file exist add it to parent
            if (file_exists($file))
            {
               addImage($curr_product_id, "ext_$_ext", '', $file, $image_file_name, 999);
               $out_name = ($rx==true) ? "rx $_brand $_serie" : "$_brand $_serie";
               echo "\t$out_name: child model $_ext added.\n";
            }
            else 
            {
               echo "\tfile: $file not found.\n";
            }
         }
      }
   }

  	echo "\ndone\n";
?>
