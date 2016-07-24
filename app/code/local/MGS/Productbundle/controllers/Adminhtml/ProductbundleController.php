<?php

class MGS_Productbundle_Adminhtml_ProductbundleController extends Mage_Adminhtml_Controller_action
{
	public function indexAction() {
		$this->loadLayout();
		$this->_title($this->__("Manage Bundle Products"));
		$this->renderLayout();
	}
	
	public function exportAction() {

		$products = Mage::getResourceModel('catalog/product_collection');
		
		if(count($products)){
			try{
				$csv_export = '';
				if($csv_export == ''){
					$file = 'single';
					
					$data_title = array(
						'website_ids', 'sku', 'name', 'category_ids');
 
					Mage::helper('productbundle')->createCsvfile($file,$data_title);
					$CSVFileName = Mage::getSingleton('core/session')->getCsvexport();
				}

				// Get Attributes: Department, Eyewear_Type
				$productModel = Mage::getModel('catalog/product');
				$department_attr = $productModel->getResource()->getAttribute("department");
				$eyewear_type_attr = $productModel->getResource()->getAttribute("eyewear_type");

				// Men RX gategory Id [dumb way]
				$rx_men_parent = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', 'Men')->getFirstItem()->getId();
				$c_rx_men = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToFilter('name', 'Prescription Sunglasses')
				->addAttributeToFilter('parent_id', $rx_men_parent)
				->getFirstItem()->getId();

				// Women RX gategory Id [dumb way]
				$rx_women_parent = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', 'Women')->getFirstItem()->getId();
				$c_rx_women = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToFilter('name', 'Prescription Sunglasses')
				->addAttributeToFilter('parent_id', $rx_women_parent)
				->getFirstItem()->getId();

				foreach($products as $product){
					$bundled_product = new Mage_Catalog_Model_Product();		
					$bundled_product->load($product->getId());
					
					$b_website_ids = implode(',',$bundled_product->getWebsiteIds());
					$b_attribute_set_id = $bundled_product->getData('attribute_set_id');
					
					$b_prescription_ready = $bundled_product->getData('prescription_ready');
					$b_sku = $bundled_product->getData('sku');  	
					$rx_sku = explode("_", $b_sku);
					$is_rx = ($rx_sku[0] == 'rx') ? true : false;
					
					// if item can not be a bundle 
					if (!$b_prescription_ready || $is_rx)
					{
						continue;
					}

					$b_name = $bundled_product->getData('name');
					$b_gender = $bundled_product->getData('department');
					$b_eyewear_type = $bundled_product->getData('eyewear_type');

					// rx category or none
					$c_ids = array();
					$eyewear_type_value = isset($b_eyewear_type) ? $eyewear_type_attr->getSource()->getOptionText($b_eyewear_type) : '';
					if ($eyewear_type_value == 'Sunglasses')
					{
						$department_value = isset($b_gender) ? $department_attr->getSource()->getOptionText($b_gender) : '';
						if ($department_value == 'Unisex' || $department_value == 'Men')
						{
							$c_ids[] = $c_rx_men;
						}
						if ($department_value == 'Unisex' || $department_value == 'Women')
						{
							$c_ids[] = $c_rx_women;
						}
					}
					
					$b_unique_model = $bundled_product->getData('model_parent');
					// remove ids if it is not a parent of the model
					if (!$b_unique_model) $c_ids = array();

					// set categories ids
					$b_category_ids = implode(',', $c_ids);

					$options = array();
					// frame of the glasses 
					if(count($options))
					{
						foreach ($options as $option) {
							$o_required = $option->getData('required');
							$o_position = $option->getData('position');
							$o_type = $option->getData('type');
							$o_title = $option->getData('default_title');

							$_selections = $option->getSelections(); // get all items of each option
							$selections_arr = array();

							if(count($_selections)){
								foreach ($_selections as $selection) {
									// data of product selection
									$selection_price_value = $selection->getData('selection_price_value');
									$selection_price_type = $selection->getData('selection_price_type');
									$selection_qty = $selection->getData('selection_qty');
									$selection_can_change_qty = $selection->getData('selection_can_change_qty');
									$position = $selection->getData('position');
									$is_default = $selection->getData('is_default');
								
									// data of product to import new product
									if ($option->getData('default_title') == "Frame")
									{
										$frameSKU = substr($b_sku, 3); // cut '-rx'
										$selection = Mage::getModel('catalog/product')->loadByAttribute('sku', $frameSKU);
									}
									else
									{
										$selection = Mage::getModel('catalog/product')->loadByAttribute('sku', $selection->getData('sku'));			
									}			
										
									$website_ids = implode(',',$selection->getWebsiteIds());
									$attribute_set_id = $selection->getData('attribute_set_id');
									$type_id = $selection->getData('type_id');
									$sku = $selection->getData('sku');
									$name = $selection->getData('name');
									$description = $selection->getData('description');
									$short_description = $selection->getData('short_description');
									$category_ids = implode(',',$selection->getCategoryIds());
									$has_options = $selection->getData('has_options');
									$msrp_enabled = $selection->getData('msrp_enabled');
									$msrp_display_actual_price_type = $selection->getData('msrp_display_actual_price_type');
									$price = $selection->getData('price');
									$special_price = $selection->getData('special_price');
									$msrp = $selection->getData('msrp');
									$status = $selection->getData('status');
									$tax_class_id = $selection->getData('tax_class_id');
									$weight = $selection->getData('weight');
									// $stock_item = $selection->getData('stock_item');
									$stock_item = '';
										
									$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($selection);
									$is_in_stock = $stock->getIsInStock();
									$qty = $stock->getQty();
										
									$selections_arr[] = implode('#sa#', array($website_ids, $attribute_set_id, $type_id, $sku, $name, $description, $short_description, $category_ids, $has_options, $msrp_enabled, $msrp_display_actual_price_type, $price, $special_price, $msrp, $status, $tax_class_id, $weight, $stock_item, $is_in_stock, $qty, $selection_price_value, $selection_price_type, $selection_qty, $selection_can_change_qty, $position, $is_default)); 
								}
							}
							$options_arr[] = implode('#oa#',array($o_required, $o_position, $o_type, $o_title, implode('#s#',$selections_arr)));
						}
					}
					
					//$bundle_options_selections = implode('#o#', $options_arr);
					
					$data = array($b_website_ids, $b_sku, $b_name, $b_category_ids);
					
					$fp = fopen($CSVFileName, 'a');
					fputcsv($fp, $data);
				}
				
				// auto save just file csv export
				Mage::helper('productbundle')->autoSave();
					
				Mage::getSingleton('adminhtml/session')->addSuccess('Export success');
			} catch (Exception $e) {
				var_dump($e->getMessage());
				Mage::log($e->getMessage());
				die("error: ".$e->getMessage());
			}
		}
		// $this->_redirect('adminhtml/dashboard');
	}
	
	public function importAction() {
		Mage::helper('productbundle')->saveCsvfile();
		$path = Mage::getBaseDir('var') . DS ."inportcsv" . DS;
		$csv_import = $_FILES['fileimport']['name'];
		$handle = fopen($path.$csv_import,'r');
		$row_count = 0;
		$new_product = 0;

		//draft data
		$bundle_ops = <<<_Draft_
1#oa#1#oa#select#oa#Frame#oa#1#sa#15#sa#simple#sa#5Q-87FB-3E9M#sa#Ray Ban RB3016 1145/15#sa#CLUBMASTER POLAR SPECIAL SERIES As fashionable today as when they were introduced, the Ray-Ban Clubmaster Polar Special Series sunglasses combine unmistakable retro style revamped for the future. The Ray-Ban RB3016 Clubmaster Polar Series blend an iconic design with special Polarized Sunglasses lenses and colors coming from the legendary legacy of Ray-Ban.
#sa#Model: RB3016 Clubmaster Classic.
Color: 1145/15 Polarized Dark Pink Lens.
Original Ray Ban Case and Ray Ban Cleaning Cloth included.
Gender: Unisex.

#sa#57,59,62,64,68,69,73,75,78,80,91,92,97,99,107,108#sa#0#sa#2#sa#4#sa#154.9900#sa#154.9900#sa##sa#1#sa#2#sa#6.4000#sa##sa#1#sa#999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#0#sa#0#o#1#oa#2#oa#select#oa#Lens Type#oa#1#sa#4#sa#simple#sa#singlevision-type-lens#sa#Single Vision#sa#Single vision lenses are prescribed if you need correction for one field of vision, either for distance, intermediate (computer), or items up close (near vision). Single vision has the same optical focal point or correction over the entire area of the lens.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#39.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#1#sa#0#s#1#sa#4#sa#simple#sa#bifocals-type-lens#sa#Bifocals#sa#Bifocals are eyeglasses with two distinct optical powers. Bifocals are commonly prescribed to people with presbyopia who also require a correction for myopia, hyperopia, and/or astigmatism.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#69.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#2#sa#0#s#1#sa#4#sa#simple#sa#progressive-type-lens#sa#Progressive#sa#This type of lens has no visible dividing line between the different prescription segments and enables the wearer to see objects in the distance, at close range and in the intermediate distance through a single lens. This type has become the mainstream senior lens. #sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#99.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#3#sa#0#o#1#oa#3#oa#select#oa#Lens Material#oa#1#sa#4#sa#simple#sa#plastic-material-lens#sa#Plastic (CR-39)#sa#CR-39, or allyl diglycol carbonate (ADC), is a plastic polymer commonly used in the manufacture of eyeglass lenses. The abbreviation stands for ""Columbia Resin #39"", which was the 39th formula of a thermosetting plastic developed by the Columbia Resins project in 1940.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#39.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#1#sa#0#s#1#sa#4#sa#simple#sa#polycarbonate-material-lens#sa#Thin (Polycarbonate)#sa#Polycarbonate eyeglass lenses are 10 times more impact-resistant than glass or regular plastic lenses, and they exceed the FDA's impact resistance requirements by more than 40 times.
#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#69.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#2#sa#0#s#1#sa#4#sa#simple#sa#hindex167-material-lens#sa#Thinner (h index 1.67)#sa#High index lenses with 1.67 high index are good for people with prescriptions over +/-6.00 sphere, because they are thinner and lighter. Thinner lenses have three advantages: they do not look thick, they do not magnify or minify your eyes when seen by others, and they are lighter. The suggested prescription range is +/-6.00 to +/-8.00 sphere. Thinner, lighter and more expensive than mid-high index or hard resin lenses, high index lenses are a good choice for every day use.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#99.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#3#sa#0#s#1#sa#4#sa#simple#sa#hindex174-material-lens#sa#Thinnest (h index 1.74)#sa#High index lenses with 1.74 are the thinnest lenses on the market, and are made only for people with the highest prescriptions.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#189.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#4#sa#0#s#1#sa#4#sa#simple#sa#crizal-material-lens#sa#Thinnest Crizal (h index 1.74)#sa#Crizal high index lenses with 1.74 are the thinnest lenses on the market, and are made only for people with the highest prescriptions.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#229.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#5#sa#0#o#0#oa#4#oa#select#oa#Lens Anti-Reflection#oa#1#sa#4#sa#simple#sa#generic-antireflection-lens#sa#Generic#sa#Generic quality anti-reflection layer.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#39.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#2#sa#0#s#1#sa#4#sa#simple#sa#premium-antireflection-lens#sa#Premium#sa#Premium quality of anti-reflection layer.#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#69.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#3#sa#0#o#0#oa#5#oa#select#oa#Lens Transition#oa#1#sa#4#sa#simple#sa#grey-transition-lens#sa#Grey#sa#Transition layer. Grey color#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#69.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#2#sa#0#s#1#sa#4#sa#simple#sa#brown-transition-lens#sa#Brown#sa#Transition layer. Brown color#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#69.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#3#sa#0#s#1#sa#4#sa#simple#sa#polarized-transition-lens#sa#Polarized#sa#Transition layer. Polarized color#sa#Corrections greater than +2 or less than -2 significantly decreases size of lenses. Needed corrections for more than one field of vision.#sa##sa#0#sa#2#sa#4#sa#99.0000#sa##sa##sa#1#sa#2#sa#0.0000#sa##sa#1#sa#99999999.0000#sa#0.0000#sa#0#sa#1.0000#sa#0#sa#4#sa#0"
_Draft_;
		$i = 0;
		$bundleOptions = array();
		$bundleSelections = array();
		$bundle_options = explode('#o#',$bundle_ops);
		foreach ($bundle_options as $bun_option)
		{
			$option_data = explode('#oa#', $bun_option);
			$bundleOptions[$i] = array(
				'required' => $option_data[0],
				'option_id' => '',
				'position' => $option_data[1],
				'type' => $option_data[2],
				'title' => $option_data[3],
				'delete' => '',
			);	
			$bundle_selections = explode ("#s#", $option_data[4]);
			foreach ($bundle_selections as $bun_selection)
			{
				$selection_data = explode('#sa#', $bun_selection);
				if ($i!=0){
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $selection_data[3]);
					if($product){
						$product_id = $product->getData('entity_id');
					}
				}
				else 
				{
					$product_id = '';
				}
						
				$bundleSelections[$i][] = array(
					'product_id' => $product_id,
					'selection_qty' => $selection_data[22],
					'selection_can_change_qty' => $selection_data[23],
					'position' => $selection_data[24],
					'is_default' => $selection_data[25],
					'selection_id' => '',
					'selection_price_type' => $selection_data[21],
					'selection_price_value' => $selection_data[20],
					'option_id' => '',
					'delete' => ''
				);
			}
			$i++;
		}

		while (($data_csv = fgetcsv($handle,1000000,",")) !== FALSE) {
			if($row_count != 0){

				$bundleProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', 'rx_' . $data_csv[1]);
				
				if(!$bundleProduct){
					$new_product++;
					try{
						// get frame data
						$frame_short_info = Mage::getModel('catalog/product')->loadByAttribute('sku', $data_csv[1]);
						$frame_id = $frame_short_info->getData('entity_id');
						$bundleSelections[0][0]["product_id"] = $frame_id;
						$frame = Mage::getModel('catalog/product')->load($frame_id);
						
						$frame_children_sku = $frame->getData('children_sku');

						$categories_ids = explode(',', $data_csv[3]);

						if ($frame->getData('model_parent'))
						{
							$children_sku_arr = explode(" ", trim($frame_children_sku));
							foreach($children_sku_arr as &$s){
						    	if ($s)
						      		$s = 'rx_' . $s;
						  	}
						  	$children_sku_new = implode(" ", $children_sku_arr);
						}
						else {
							$children_sku_new = $frame_children_sku;
						}
					
						Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
						$bundleProduct = Mage::getModel('catalog/product');
						$p = array(
							'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID,
				            'sku_type' => 1,
				            'shipment_type' => 0,            
							'price_type' => 0,
							'price_view' => 1,
				            'sku' => 'rx_' . $frame->getData('sku'),
				            'name' =>  $frame->getData('name'),
				            'status' => 1,//$data_csv[9],
				            'visibility' => 2,//$data_csv[10],
				            'description' =>  $frame->getData('description'),
				            'short_description' =>  $frame->getData('short_description'),
				            'url_key' => 'rx_' .  $frame->getData('url_key'),
				            'type_id' => 'bundle',
				            'attribute_set_id' => $frame->getData('attribute_set_id'),
				            'weight_type' => 0,
				            'has_options' => 1,
				            'website_ids' => explode(',',$data_csv[0]),
				            'created_at' => strtotime('now'),
				            'category_ids' => $categories_ids,
				            'country_of_manufacture' => 'IT',
				            'manufacturer' => 28,
				         	'stock_data' => array(
				         		'use_config_manage_stock' => 1, 
								'manage_stock' => 1, 
								'is_in_stock' => 1
				         	),
				            'eyewear_type' => $frame->getData('eyewear_type'),
							'prescription_ready' => $frame->getData('prescription_ready'),
							'department' => $frame->getData('department'),
							'style' => $frame->getData('style'),
							'brand' => $frame->getData('brand'),
							'brand_model' => $frame->getData('brand_model'),
							'brand_ext' => $frame->getData('brand_ext'),
							'frame_material' => $frame->getData('frame_material'),
							'frame_color' => $frame->getData('frame_color'),
							'bridge_width' => $frame->getData('bridge_width'),
							'arm_length' => $frame->getData('arm_length'),
							'lens_width' => $frame->getData('lens_width'),
							'lens_material' => $frame->getData('lens_material'),
							'colors' => $frame->getData('colors'),
                                             'meta_title' => $frame->getData('meta_title'),
                                             'meta_keyword' => $frame->getData('meta_keyword'),
                                             'meta_description' => $frame->getData('meta_description'),
                                             'sort_params' => $frame->getData('sort_params'),
							'polarized' => $frame->getData('polarized'),
							'model_parent' => $frame->getData('model_parent'),
							'children_sku' => $children_sku_new,
							
				            'delivery_time' => '',
				            'generate_meta' => 1,
				            'tax_class_id' => 1, //19%
			    		);
						$bundleProduct->setData($p);					

						//registering a product because of Mage_Bundle_Model_Selection::_beforeSave
						Mage::register('product', $bundleProduct);
						Mage::register('current_product', $bundleProduct);
							
						//flags for saving custom options/selections
						$bundleProduct->setCanSaveCustomOptions(true);
						$bundleProduct->setCanSaveBundleSelections(true);
						$bundleProduct->setAffectBundleProductSelections(true);
						 
						//setting the bundle options and selection data
						$bundleProduct->setBundleOptionsData($bundleOptions);
						$bundleProduct->setBundleSelectionsData($bundleSelections);
						 
						$bundleProduct->save();

						Mage::unregister('product');
						Mage::unregister('current_product');
						
					} catch (Exception $e) {
						Mage::log($e->getMessage());
						echo "error: ".$e->getMessage();
					}
				}
			}
			$row_count++;
		}

		if($new_product == 0){
			Mage::getSingleton('adminhtml/session')->addSuccess("Don't have new products in file CSV");
		}
		$this->_redirect('adminhtml/catalog_product/index');
		Mage::getSingleton('adminhtml/session')->addSuccess('Import success');
	}
}