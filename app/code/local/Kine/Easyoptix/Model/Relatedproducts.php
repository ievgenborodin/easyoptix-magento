<?php

class Kine_Easyoptix_Model_Relatedproducts extends Mage_Core_Model_Abstract
{
	protected $_brand;
	protected $_style;
	protected $_curr_item;

	public function setRelatedProducts($curr_item, $brand, $style)
	{
		$this->_curr_item = $curr_item;
		$this->_brand = $brand;
		$this->_style = isset($style) ? $style : '';
	}

	public function getRelatedProducts($maxCount = 5)
	{
		$relatedProducts = array();
		$allProducts = array();

		$product_model = Mage::getModel('catalog/product');

		// style
		if (isset($this->_style))
		{
			// set collection with style filter
			$productCollection = $product_model
			->getCollection()->addAttributeToSelect('*')
			->addFieldToFilter('name', array('neq'=>$this->_curr_item))
			->addFieldToFilter('style', array('eq'=>$this->_style))->getItems();

			// if no matches
			if (!count($productCollection)) 
			{
				// set collection with brand filter
				$productCollection = $product_model
				->getCollection()->addAttributeToSelect('*')
				->addFieldToFilter('name', array('neq'=>$this->_curr_item))
				->addFieldToFilter('brand', array('eq'=>$this->_brand))->getItems();
			}
		}
		else // set collection with brand filter [default]
		{
			$productCollection = $product_model
			->getCollection()->addAttributeToSelect('*')
			->addFieldToFilter('name', array('neq'=>$this->_curr_item))
			->addFieldToFilter('brand', array('eq'=>$this->_brand))->getItems();
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
			$relatedProducts[] = $allProducts[$c];
			
			if ($_count++ >= $qty)
				break;
		}
		return $relatedProducts;
	}
}