<?php
/**
 * shoppingflux_Productwrapper
 * @package modules.shoppingflux
 */
class shoppingflux_Productwrapper extends productexporter_BaseProductwrapper
{
	protected function returnIfNotEmpty($value)
	{
		return f_util_StringUtils::isEmpty($value) ? '' : $value;

	}
	/**
	 * @return string
	 */
	public function _getId_parent()
	{
		$product = $this->getProduct();
		if ($product instanceof catalog_persistentdocument_productdeclination)
		{
			return $product->getDeclinedProduct()->getId();
		}
		return $product->getId();
	}

	/**
	 * @return string
	 */
	public function _getId()
	{
		return $this->getProduct()->getId();
	}

	/**
	 * @return string
	 */
	public function _getNom()
	{
		$product = $this->getProduct();
		if ($product instanceof catalog_persistentdocument_productdeclination)
		{
			return $product->getDeclinedProduct()->getNavigationLabel();
		}
		return $this->getProduct()->getNavigationLabel();
	}
	
	/**
	 * @return string
	 */
	public function _getUrl()
	{
		return $this->getProduct()->getDocumentService()->generateUrlForExporter($this->getProduct(), $this->getShop());
	}

	/**
	 * @return string
	 */
	public function _getDescription()
	{
		return $this->getProduct()->getDescriptionAsHtml();
	}

	/**
	 * @return string
	 */
	public function _getDescription_Courte()
	{
		$title = f_util_StringUtils::shortenString(f_util_HtmlUtils::htmlToText($this->getProduct()->getDescriptionAsHtml()), 255);
		return $title;
	}

	public function _getPrix()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return strval(catalog_PriceFormatter::getInstance()->round($price->getValueWithTax()));
		}
		return '';
	}

	public function _getPrix_Barre()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return strval(catalog_PriceFormatter::getInstance()->round($price->getOldValueWithTax()));
		}
		return '';
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasDiscount()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return $price->isDiscount();
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function _getFrais_De_Port()
	{
		$shipping = $this->getExport()->getShippingFilter();
		if ($shipping === null)
		{
			return '';
		}
		return strval(catalog_PriceFormatter::getInstance()->round($shipping->getValueWithTax()));
	}

	/**
	 * @return string
	 */
	public function _getDelai_Livraison()
	{
		return $this->returnIfNotEmpty($this->getExport()->getDeliveryDelay());
	}

	/**
	 * @return string
	 */
	public function _getMarque()
	{
		$result = '';
		$brand = $this->getProduct()->getBrand();
		if ($brand)
		{
			$result = $brand->getLabel();
		}
		return $result;
	}
	
	/**
	 * @return string
	 */
	public function _getRayon()
	{
		return $this->getProduct()->getShopPrimaryShelf($this->getShop())->getLabel();
	}

	/**
	 * @return string
	 */
	public function _getQuantite()
	{
		return strval($this->getProduct()->getStockableDocument()->getCurrentStockQuantity());
	}

	/**
	 * @return string
	 */
	public function _getEan()
	{
		$attributes =  $this->getProduct()->getAttributes();
		return isset($attributes['EAN']) ? $attributes['EAN'] : '';
	}

	/**
	 * @return string
	 */
	public function _getPoids()
	{
		$attributes =  $this->getProduct()->getAttributes();
		return isset($attributes['POIDS']) ? $attributes['POIDS'] : '';
	}

	/**
	 * @return string
	 */
	public function _getEcotaxe()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return strval(catalog_PriceFormatter::getInstance()->round($price->getEcoTax()));
		}
		return '';
	}


	/**
	 * @return string
	 */
	public function _getTva()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return strval(catalog_PriceFormatter::getInstance()->round(100*$price->getTax()->getRate()));
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function _getRef_Constructeur()
	{
		$attributes =  $this->getProduct()->getAttributes();
		return isset($attributes['REF-CONSTRUCTEUR']) ? $attributes['REF-CONSTRUCTEUR'] : $this->getProduct()->getCodeReference();
	}

	/**
	 * @return string
	 */
	public function _getRef_Fournisseur()
	{
		$attributes =  $this->getProduct()->getAttributes();
		return isset($attributes['REF-FOURNISSEUR']) ? $attributes['REF-FOURNISSEUR'] : $this->getProduct()->getCodeReference();
	}

	public function _getImages()
	{
		$product = $this->getProduct();
		$website = $this->getShop()->getWebsite();
		return array_map(function($doc) use ($website){
				return LinkHelper::getDocumentUrlForWebsite($doc, $website);
			}, $product->getAllVisuals($this->getShop()));
	}

	public function _getUrl_Categories()
	{
		$product = $this->getProduct();
		$website = $this->getShop()->getWebsite();
		return array_map(function($doc) use ($website){
				return LinkHelper::getDocumentUrlForWebsite($doc, $website);
			}, $product->getShelfArray());
	}

	protected $extraAttributes = array('CARA1', 'CARA2', 'CARA3', 'CARA4');

	/**
	 * @return string
	 */
	public function _getCaracteristiques()
	{
		$result = array();
		$attributes =  $this->getProduct()->getAttributes();
		foreach ($this->extraAttributes as $attributeName)
		{
			if (isset($attributes[$attributeName]))
			{
				$result[] = $attributes[$attributeName];
			}
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function _getDeclinaisons()
	{
		$result = array();
		if ($this->getProduct() instanceof catalog_persistentdocument_productdeclination)
		{
			
		}
		$attributes =  $this->getProduct()->getAttributes();
		foreach ($this->extraAttributes as $attributeName)
		{
			if (isset($attributes[$attributeName]))
			{
				$result[] = $attributes[$attributeName];
			}
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function _getAttributs()
	{
		$result = array();
		$product = $this->getProduct();
		if ($product instanceof catalog_persistentdocument_productdeclination)
		{
			$declinedProduct = $product->getDeclinedproduct();
			foreach ($declinedProduct->getAxes() as $i => $axe)
			{
				$result[str_replace(':', '_', $axe->getName())] = $product->getDocumentService()->getAxeLabel($product, $i+1);
			}
			
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function _getDiscount_From()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return $price->getUIStartpublicationdate();
		}
		return '';
	}

	public function _getDiscount_To()
	{
		$price = $this->getProduct()->getPriceForCurrentShopAndCustomer();
		if ($price instanceof catalog_persistentdocument_price)
		{
			return $price->getUIEndpublicationdate();
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function _getFil_Ariane()
	{
		return $this->getProduct()->getDocumentService()->getPathOf($this->getProduct(), " > ");
	}

	/**
	 * @return string
	 */
	public function _getManufacturer()
	{
		return '';
	}

	/**
	 * @return string
	 */
	public function _getSupplier()
	{
		return '';
	}

	/**
	 * @return string
	 */
	public function _getBrand_Url()
	{
		$result = '';
		$brand = $this->getProduct()->getBrand();
		if ($brand)
		{
			$result = LinkHelper::getDocumentUrlForWebsite($brand, $this->getShop()->getWebsite());
		}
		return $result;
	}
}