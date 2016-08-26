<?php

class Mage_Page_Block_Template_Navigation extends Mage_Core_Block_Template
{

	protected $cats = array();

	protected $special_cats = array(
		'Men' => array(
			'name' => 'Dior Homme COMPOSIT',
			'url' => 'brands/dior/composit-010.html',
			'image' => 'images/media/banners/minibanner-men.jpg'
		),
		'Women' => array(
			'name' => 'Ray-Ban RB4222',
			'url'  => 'women/sunglasses/rb4222-61684v.html',
			'image' => 'images/media/banners/minibanner-women.jpg'
		)
	);

	protected $cms_pages = array(
		'0' => array(
			'name' => 'Eyeglasses',
			'url' => 'eyeglasses',
			'image' => ''
		),
		'1' => array(
			'name' => 'Sunglasses',
			'url' => 'sunglasses',
			'image' => ''
		)
	);


	protected function _construct()
	{
		$_helper = Mage::helper('catalog/category');
		$_categories = $_helper->getStoreCategories();
		if (count($_categories) > 0)
		{
			$this->cats = $this->getSubCats($this->cats, $_categories);
		}
	}

	protected function getSubCats($cats, $_categories)
	{
		$indx = 0;
		foreach($_categories as $_category)
		{
		    $_category = Mage::getModel('catalog/category')->load($_category->getId());

		    $cats[$indx] = array(
			   	'name' 	=> 	$_category->getName(),
			    'id'	=>	$_category->getId(),
			    'url'	=> 	$_category->getUrl()
			);

		    $_subcategories = $_category->getChildrenCategories();
		    if (count($_subcategories) > 0)
		    {
		    	$cats[$indx]['children'] = $this->getSubCats($cats[$indx]['children'], $_subcategories);    
		    }
		    $indx++;
		}
		return $cats;
	}

}