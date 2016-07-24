<?php

class Kine_Easyoptix_Model_Randproducts extends Mage_Core_Model_Abstract
{
	public function getRandomProducts($maxCount = 4)
	{
		$randProducts = array();
		$allProducts = array();

		$product_model = Mage::getModel('catalog/product');

		$_url = Mage::helper('core/url')->getCurrentUrl();
		preg_match("/eyeglasses/i", $_url, $eyeglasses);
		preg_match("/sunglasses/i", $_url, $sunglasses);

		// if eyewear_type set
		if (count($eyeglasses) || count($sunglasses))
		{
			// get label
			$_type = (count($eyeglasses)) ? ucfirst($eyeglasses[0]) : ucfirst($sunglasses[0]);
			// get attribute
			$eyewear_type_attr = $product_model->getResource()->getAttribute("eyewear_type");
			// get id of the label
			$_type_id = $eyewear_type_attr->getSource()->getOptionId($_type);
			// set collection with eyewear_type filter
			$productCollection = $product_model
			->getCollection()->addAttributeToSelect('*')
			->addFieldToFilter('eyewear_type', array('eq'=>$_type_id))->getItems();
		}
		else {
			$productCollection = $product_model
			->getCollection()->addAttributeToSelect('*')->getItems();
		}

		$unique_models = array();
		foreach($productCollection as $id => $data)
		{

			$sku_parts = explode('_', $data['sku']);
			if ($sku_parts[0] == 'rx')
				continue;

			$curr_model = $sku_parts[0] . $sku_parts[1];

			if (!in_array($curr_model, $unique_models))
			{
				$unique_models[] = $curr_model;
				$allProducts[] = $data;
			}
		}

		$totalProductIds = count($allProducts);
		$qty = ($maxCount <= $totalProductIds) ? $maxCount : $totalProductIds;
		
		$curr = range(0, $totalProductIds-1);
		shuffle($curr);
		
		$_count = 1;
		foreach($curr as $c){
			$randProducts[] = $allProducts[$c];
			
			if ($_count++ >= $qty)
				break;
		}
		return $randProducts;
	}
}