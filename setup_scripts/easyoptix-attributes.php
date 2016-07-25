<?php

  $client = new SoapClient('https://easy-optix.com/index.php/api/soap/?wsdl');

  try {
    $session_id = $client->login('kine', 'bClinton1923');
    echo "connected";

  // Create new attribute
  function create_attr_props($attr_name, $attr_code, $p_list, $p_view, $attr_type = "select")
  {
    $attributeProps = array(
      "attribute_code" => $attr_code,
      "scope" => "global",
      "frontend_input" => $attr_type,
      "is_unique" => '0',
      "is_required" => '0',
      "is_configurable" => '0',
      "is_searchable" => '0',
      "is_visible_in_advanced_search" => '0',
      "used_in_product_listing" => $p_list,
      "is_visible_on_front" => $p_view,
      "additional_fields" => array(
          "is_filterable" => '0',
          "is_filterable_in_search" => '0',
          "position" => '1',
          "used_for_sort_by" => '0'
      ),
      "frontend_label" => array(
          array(
              "store_id" => '0',
              "label" => $attr_name
          )
      )
    );

    global $client;
    global $session_id;

    $client->call($session_id, "product_attribute.create", array(
        $attributeProps));

    return $attr_code;
  };

  // Add Options
  function add_option($attr, $option_val)
  {
    $optionToAdd = array(
      "label" => array(
          array(
              "store_id" => '0',
              "value" => $option_val
          )
      ),
      "order" => '0',
      "is_default" => '0'
    );

    global $client;
    global $session_id;

    $client->call($session_id, "product_attribute.addOption", array($attr, $optionToAdd));
  }


  /***************************************************************/

  /* ---- eyewear type ----- */
  $newAttr = create_attr_props("Eyewear Type", "eyewear_type", '0', '0');
  add_option($newAttr, "Sunglasses");
  add_option($newAttr, "Eyeglasses");
  
  /* ------ prescription ready ------ */
  $newAttr = create_attr_props("Prescription Ready", "prescription_ready", '0', '0', "boolean");

  /* ------ gender / department ------- */
  $newAttr = create_attr_props("Department", "department",'0','0');
  add_option($newAttr, "Men");
  add_option($newAttr, "Women");
  add_option($newAttr, "Unisex");

  /* ------ style ------- */
  $newAttr = create_attr_props("Style", "style", '0', '0');
  add_option($newAttr, "Pilot");
  add_option($newAttr, "Wayfarer");
  add_option($newAttr, "Cat-Eye");
  add_option($newAttr, "Round");
  add_option($newAttr, "Clubmaster");
  add_option($newAttr, "Sport");
  add_option($newAttr, "Rectangular");

  /* ------ brand ------- */
  $newAttr = create_attr_props("Brand", "brand", '0', '0');
  add_option($newAttr, "Dior");
  add_option($newAttr, "Miu Miu");
  add_option($newAttr, "Maui Jim");
  add_option($newAttr, "Ray-Ban");
  add_option($newAttr, "Givenchy");
    
  create_attr_props("Model", "brand_model", '0', '0', "text");
  create_attr_props("Model Extension", "brand_ext", '1', '1', "text");

  /* ------ frame material ------- */
  $newAttr = create_attr_props("Frame Material", "frame_material", '0', '0');
  add_option($newAttr, "Plastic");
  add_option($newAttr, "Metal");
  add_option($newAttr, "Titanium");
  add_option($newAttr, "Composite");
  
  /* ------ bridge width ------- */
  $newAttr = create_attr_props("Bridge Width", "bridge_width", '0', '0', "text");
  /* ------ arm length ------- */
  $newAttr = create_attr_props("Arm Length", "arm_length", '0', '0', "text");
  /* ------ lens width ------- */
  $newAttr = create_attr_props("Lens Width", "lens_width", '0', '0', "text");

  /* ------ lens material ------- */
  $newAttr = create_attr_props("Lens Material", "lens_material", '0', '0');
  add_option($newAttr, "Plastic");
  add_option($newAttr, "Glass");
  add_option($newAttr, "Gradient");
  add_option($newAttr, "Mirrored");
  add_option($newAttr, "Composite");
  
  create_attr_props("Polarized", "polarized", '0', '0', "boolean");
  create_attr_props("Model Parent", "model_parent", '0', '0', "boolean");
  create_attr_props("Children Sku", "children_sku", '0', '0', "textarea");
  create_attr_props("Sort", "sort_params", '1', '0', "textarea");
  /* ---- colors ----- */
  $newAttr = create_attr_props("Colors", "colors", '0', '0', "text");
  
  /***************************************************************/
    echo "\n done \n";
  } catch (SoapFault $fault) {
    echo 'Fault code:'.$fault->faultcode.'<br>';
    echo 'Fault reason:'.$fault->faultstring;
  }

?>