<?php
class Kine_Easyoptix_Model_Bannerwall extends Mage_Core_Model_Abstract
{
	protected $banners_data = array(
		'eyeglasses' => array(
			'left-top' => array(
				array(
					'title' => 'Shop Ray-Ban Men',
					'href' 	=> 'men/brands/ray-ban.html'
				),
				array(
					'title' => 'Shop Ray-Ban Women',
					'href' 	=> 'women/brands/ray-ban.html'
				)
			),
			'left-bottom-1' => array(
				array(
					'title' => 'Shop Clubmaster Glasses',
					'href' 	=> 'styles/clubmaster.html'
				)
			),
			'left-bottom-2' => array(
				array(
					'title' => 'Shop Dior Men',
					'href' 	=> 'men/brands/dior.html'
				),
				array(
					'title' => 'Shop Dior Women',
					'href' 	=> 'women/brands/dior.html'
				)
			),
			'right' => array(
				array(
					'title' => 'Shop Round Glasses',
					'href' 	=> 'styles/round.html'
				)
			)
		),
		'sunglasses' => array(
			'left-top' => array(
				array(
					'title' => 'Shop Maui Jim Men',
					'href' 	=> 'men/brands/maui-jim.html'
				),
				array(
					'title' => 'Shop Maui Jim Women',
					'href' 	=> 'women/brands/maui-jim.html'
				)
			),
			'left-bottom-1' => array(
				array(
					'title' => 'Shop Dior Home Composit',
					'href' 	=> 'brands/dior/composit-010.html'
				)
			),
			'left-bottom-2' => array(
				array(
					'title' => 'Shop Maui Jim Men',
					'href' 	=> 'men/brands/maui-jim.html'
				),
				array(
					'title' => 'Shop Maui Jim Women',
					'href' 	=> 'women/brands/maui-jim.html'
				)
			),
			'right' => array(
				array(
					'title' => 'Shop Miu Miu 12QS',
					'href' 	=> 'brands/miu-miu/12qs.html'
				)
			)
		)
	);

	protected $currType = '';

	protected function setEyewearType()
	{
		$_url = Mage::helper('core/url')->getCurrentUrl();
		preg_match("/eyeglasses/i", $_url, $eyeglasses);
		preg_match("/sunglasses/i", $_url, $sunglasses);

		if (count($eyeglasses) || count($sunglasses))
		{
			$_type = (count($eyeglasses)) ? $eyeglasses[0] : $sunglasses[0];
			$this->currType = $_type;
		} 
	}

	public function getEyewearType()
	{
		return $this->currType;
	}

	public function getBannersData()
	{
		// set Eyewear Type
		$this->setEyewearType();

		// set eyewear data
		$_type = $this->getEyewearType();
		if($_type != '')
		{
			return $this->banners_data[$_type];
		}
	
		// default
		return $this->banners_data['eyeglasses'];
	}
}