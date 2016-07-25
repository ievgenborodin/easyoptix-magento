<?php
  /* =========
    version 0.0.2
    need to add title, change names
  ==========*/

  $client = new SoapClient('https://easy-optix.com/index.php/api/soap/?wsdl');

  try {
  	$session_id = $client->login('kine', 'bClinton1923');
  	echo "connected";

  	/*
		Brands
  	*/
	$brands_params = array(
  		'name' => 'Brands',
  		'url_key' => 'brands',
  		'image' => 'allbrands.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$brands_dior_params = array(
  		'name' => 'Dior',
  		'url_key' => 'dior',
  		'image' => 'dior.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$brands_mauijim_params = array(
  		'name' => 'Maui Jim',
  		'url_key' => 'maui-jim',
  		'image' => 'maui_jim.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$brands_rayban_params = array(
  		'name' => 'Ray-Ban',
  		'url_key' => 'ray-ban',
  		'image' => 'ray_ban.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$brands_givenchy_params = array(
  		'name' => 'Givenchy',
  		'url_key' => 'givenchy',
  		'image' => 'givenchy.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$brands_miumiu_params = array(
  		'name' => 'Miu Miu',
  		'url_key' => 'miu-miu',
  		'image' => 'miu_miu.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);


  	/*
		Styles
  	*/
	$styles_params = array(
  		'name' => 'Styles',
  		'url_key' => 'styles',
  		'image' => 'allstyles.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$styles_pilot_params = array(
  		'name' => 'Pilot',
  		'url_key' => 'pilot',
  		'image' => 'pilot.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
    $styles_sport_params = array(
      'name' => 'Sport',
      'url_key' => 'sport',
      'image' => 'sport.jpg',
      'include_in_menu' => '1',
      'available_sort_by' => '1',
      'default_sort_by' => '1',
      'is_active' => '1'
    );
  	$styles_clubmaster_params = array(
  		'name' => 'Clubmaster',
  		'url_key' => 'clubmaster',
  		'image' => 'clubmaster.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$styles_round_params = array(
  		'name' => 'Round',
  		'url_key' => 'round',
  		'image' => 'round.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$styles_wayfarer_params = array(
  		'name' => 'Wayfarer',
  		'url_key' => 'wayfarer',
  		'image' => 'wayfarer.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$styles_rectangular_params = array(
  		'name' => 'Rectangular',
  		'url_key' => 'rectangular',
  		'image' => 'rectangular.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$styles_cateye_params = array(
  		'name' => 'Cat-Eye',
  		'url_key' => 'cat-eye',
  		'image' => 'cat_eye.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);


  	/**
	 *	Sale
  	 */
  	$sale_params = array(
  		'name' => 'Sale',
  		'url_key' => 'sale',
  		'image' => 'allsale.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);  	
  	$sale_men_params = array(
  		'name' => 'Men',
  		'url_key' => 'men',
  		'image' => 'mens_sale.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$sale_women_params = array(
  		'name' => 'Women',
  		'url_key' => 'women',
  		'image' => 'womens_sale.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);

    $men_sale_params = array(
      'name' => 'Sale',
      'url_key' => 'sale',
      'image' => 'mens_sale.jpg',
      'include_in_menu' => '1',
      'available_sort_by' => '1',
      'default_sort_by' => '1',
      'is_active' => '1'
    );
    $women_sale_params = array(
      'name' => 'Sale',
      'url_key' => 'sale',
      'image' => 'womens_sale.jpg',
      'include_in_menu' => '1',
      'available_sort_by' => '1',
      'default_sort_by' => '1',
      'is_active' => '1'
    );

  	/**
  	 * Eyeglasses
  	 */
  	$eyeglasses_men_params = array(
  		'name' => 'Eyeglasses',
  		'url_key' => 'eyeglasses',
  		'image' => 'eyeglasses_men.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$eyeglasses_women_params = array(
  		'name' => 'Eyeglasses',
  		'url_key' => 'eyeglasses',
  		'image' => 'eyeglasses_men.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);

  	/**
  	 * Sunglasses
  	 */
  	$sunglasses_men_params = array(
  		'name' => 'Sunglasses',
  		'url_key' => 'sunglasses',
  		'image' => 'sunglasses_men.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$sunglasses_women_params = array(
  		'name' => 'Sunglasses',
  		'url_key' => 'sunglasses',
  		'image' => 'sunglasses_men.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);

  	/**
  	 * Rx Sunglasses
  	 */
  	$rxsunglasses_men_params = array(
  		'name' => 'Prescription Sunglasses',
  		'url_key' => 'prescription-sunglasses',
  		'image' => 'rxsunglasses_men.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$rxsunglasses_women_params = array(
  		'name' => 'Prescription Sunglasses',
  		'url_key' => 'prescription-sunglasses',
  		'image' => 'rxsunglasses_women.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);

  	/**
  	 * Gendar
  	 */
  	$men_params = array(
  		'name' => 'Men',
  		'url_key' => 'men',
  		'image' => 'men.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);
  	$women_params = array(
  		'name' => 'Women',
  		'url_key' => 'women',
  		'image' => 'womens.jpg',
  		'include_in_menu' => '1',
  		'available_sort_by' => '1',
  		'default_sort_by' => '1',
  		'is_active' => '1'
  	);

  	echo "<p>Predata. done</p>";

  	/* Men sections */

  	$men_id = $client->call($session_id, 'catalog_category.create', array('2', $men_params));
  	$client->call($session_id, 'catalog_category.create', array($men_id, $eyeglasses_men_params));
  	$client->call($session_id, 'catalog_category.create', array($men_id, $sunglasses_men_params));
  	$client->call($session_id, 'catalog_category.create', array($men_id, $rxsunglasses_men_params));

  	$client->call($session_id, 'catalog_category.create', array($men_id, $men_sale_params));

  	$men_styles_id = $client->call($session_id, 'catalog_category.create', array($men_id, $styles_params));
  	$client->call($session_id, 'catalog_category.create', array($men_styles_id, $styles_pilot_params));
    $client->call($session_id, 'catalog_category.create', array($men_styles_id, $styles_sport_params));
  	$client->call($session_id, 'catalog_category.create', array($men_styles_id, $styles_clubmaster_params));
  	$client->call($session_id, 'catalog_category.create', array($men_styles_id, $styles_round_params));
  	$client->call($session_id, 'catalog_category.create', array($men_styles_id, $styles_wayfarer_params));
  	$client->call($session_id, 'catalog_category.create', array($men_styles_id, $styles_rectangular_params));

  	$men_brands_id = $client->call($session_id, 'catalog_category.create', array($men_id, $brands_params));
  	$client->call($session_id, 'catalog_category.create', array($men_brands_id, $brands_rayban_params));
  	$client->call($session_id, 'catalog_category.create', array($men_brands_id, $brands_dior_params));
  	$client->call($session_id, 'catalog_category.create', array($men_brands_id, $brands_mauijim_params));
  	$client->call($session_id, 'catalog_category.create', array($men_brands_id, $brands_givenchy_params));
  	echo "<p>Men sections. done</p>";

  	/* Women sections */

  	$women_id = $client->call($session_id, 'catalog_category.create', array('2', $women_params));
  	$client->call($session_id, 'catalog_category.create', array($women_id, $eyeglasses_women_params));
  	$client->call($session_id, 'catalog_category.create', array($women_id, $sunglasses_women_params));
  	$client->call($session_id, 'catalog_category.create', array($women_id, $rxsunglasses_women_params));

  	$client->call($session_id, 'catalog_category.create', array($women_id, $women_sale_params));

  	$women_styles_id = $client->call($session_id, 'catalog_category.create', array($women_id, $styles_params));
  	$client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_pilot_params));
    $client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_sport_params));
  	$client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_clubmaster_params));
  	$client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_round_params));
  	$client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_wayfarer_params));
  	$client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_rectangular_params));
  	$client->call($session_id, 'catalog_category.create', array($women_styles_id, $styles_cateye_params));

  	$women_brands_id = $client->call($session_id, 'catalog_category.create', array($women_id, $brands_params));
  	$client->call($session_id, 'catalog_category.create', array($women_brands_id, $brands_rayban_params));
  	$client->call($session_id, 'catalog_category.create', array($women_brands_id, $brands_dior_params));
  	$client->call($session_id, 'catalog_category.create', array($women_brands_id, $brands_mauijim_params));
  	$client->call($session_id, 'catalog_category.create', array($women_brands_id, $brands_miumiu_params));
  	$client->call($session_id, 'catalog_category.create', array($women_brands_id, $brands_givenchy_params));
  	echo "<p>Women sections. done</p>";


  	/* Brands sections */

  	$brands_id = $client->call($session_id, 'catalog_category.create', array('2', $brands_params));
  	$client->call($session_id, 'catalog_category.create', array($brands_id, $brands_rayban_params));
  	$client->call($session_id, 'catalog_category.create', array($brands_id, $brands_miumiu_params));
  	$client->call($session_id, 'catalog_category.create', array($brands_id, $brands_dior_params));
  	$client->call($session_id, 'catalog_category.create', array($brands_id, $brands_mauijim_params));
  	$client->call($session_id, 'catalog_category.create', array($brands_id, $brands_givenchy_params));
  	echo "<p>Brands sections. done</p>";


  	/* Styles sections */

  	$styles_id = $client->call($session_id, 'catalog_category.create', array('2', $styles_params));
  	$client->call($session_id, 'catalog_category.create', array($styles_id, $styles_pilot_params));
    $client->call($session_id, 'catalog_category.create', array($styles_id, $styles_sport_params));
  	$client->call($session_id, 'catalog_category.create', array($styles_id, $styles_clubmaster_params));
  	$client->call($session_id, 'catalog_category.create', array($styles_id, $styles_round_params));
  	$client->call($session_id, 'catalog_category.create', array($styles_id, $styles_wayfarer_params));
  	$client->call($session_id, 'catalog_category.create', array($styles_id, $styles_rectangular_params));
  	$client->call($session_id, 'catalog_category.create', array($styles_id, $styles_cateye_params));
  	echo "<p>Styles sections. done</p>";


	/* Sale sections */

  	$sale_id = $client->call($session_id, 'catalog_category.create', array('2', $sale_params));
  	$client->call($session_id, 'catalog_category.create', array($sale_id, $sale_men_params));
  	$client->call($session_id, 'catalog_category.create', array($sale_id, $sale_women_params));
  	echo "<p>Sale sections. done</p>";

    $client->endSession($session_id);
  	echo "<br><p>done</p>";
  } catch (SoapFault $fault) {
  	echo 'Fault code:'.$fault->faultcode.'<br>';
  	echo 'Fault reason:'.$fault->faultstring;
  }
