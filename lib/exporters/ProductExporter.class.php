<?php
/**
 * shoppingflux_ProductExporter
 * @package modules.shoppingflux
 */
class shoppingflux_ProductExporter extends productexporter_BaseXmlExporter
{
	private $exported = array();
	/**
	 * @var shoppingflux_ProductExporter
	 */
	private static $instance;

	/**
	 * @return shoppingflux_ProductExporter
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
	/**
	 * @param productexporter_persistentdocument_export $export
	 */
	protected function getFileHeader($export)
	{
		return '<?xml version="1.0" encoding="UTF-8"?><produits>';
	}
	
	/**
	 * @param productexporter_persistentdocument_export $export
	 */
	protected function getFileFooter($export)
	{
		return "</produits>";
	}
	
	/**
	 * @param integer $productId
	 * @param productexporter_persistentdocument_export $export
	 * @param integer $index
	 * @return string
	 */
	public function exportProductId($productId, $export, $index)
	{
		$product = DocumentHelper::getDocumentInstance($productId);
		$masterProductId = $productId;
		if ($product instanceof catalog_persistentdocument_productdeclination)
		{
			$declinations = $product->getDeclinedProduct()->getPublishedDeclinationsInShop($export->getShop());
			if (count($declinations))
			{
				$masterProductId = $declinations[0]->getId();
			}
		}
		if (isset($this->exported[$masterProductId]))
		{
			return '';
		}

		catalog_ShopService::getInstance()->setCurrentShop($export->getShop());
		$doc = new DOMDocument('1.0', 'UTF-8');
		$root = $doc->createElement('produit');
		$doc->appendChild($root);
		$wrapper = new shoppingflux_Productwrapper($masterProductId, $export);
		$this->addDomNode($doc, $root, 'id_parent', $wrapper->_getId_parent());
		$this->addDomNode($doc, $root, 'nom', $wrapper->_getNom());
		$this->addDomNode($doc, $root, 'url', $wrapper->_getUrl());
		$this->addDomNode($doc, $root, 'description', $wrapper->_getDescription());
		$this->addDomNode($doc, $root, 'description-courte', $wrapper->_getDescription_Courte());
		$this->addDomNode($doc, $root, 'prix', $wrapper->_getPrix());
		if ($wrapper->hasDiscount())
		{
			$this->addDomNode($doc, $root, 'prix-barre', $wrapper->_getPrix_Barre());
			$this->addDomNode($doc, $root, 'discount-from', $wrapper->_getDiscount_From());
			$this->addDomNode($doc, $root, 'discount-to', $wrapper->_getDiscount_To());

		}
		$this->addDomNode($doc, $root, 'frais-de-port', $wrapper->_getFrais_De_Port());
		$this->addDomNode($doc, $root, 'delai-livraison', $wrapper->_getDelai_Livraison());
		$this->addDomNode($doc, $root, 'marque', $wrapper->_getMarque());
		$this->addDomNode($doc, $root, 'brand-url', $wrapper->_getBrand_Url());
		$this->addDomNode($doc, $root, 'rayon', $wrapper->_getRayon());
		$this->addDomNode($doc, $root, 'quantite', $wrapper->_getQuantite());
		$this->addDomNode($doc, $root, 'ean', $wrapper->_getEan());
		$this->addDomNode($doc, $root, 'poids', $wrapper->_getPoids());
		$this->addDomNode($doc, $root, 'ecotaxe', $wrapper->_getEcotaxe());
		$this->addDomNode($doc, $root, 'tva', $wrapper->_getTva());
		$this->addDomNode($doc, $root, 'ref-constructeur', $wrapper->_getRef_Constructeur());
		$this->addDomNode($doc, $root, 'ref-fournisseur', $wrapper->_getRef_Fournisseur());
		$images = $doc->createElement('images');
		$root->appendChild($images);
		foreach ($wrapper->_getImages() as $url)
		{
			$this->addDomNode($doc, $images, 'image', $url);
		}

		$urlCategories = $doc->createElement('url-categories');
		$root->appendChild($urlCategories);
		foreach ($wrapper->_getUrl_Categories() as $url)
		{
			$this->addDomNode($doc, $urlCategories, 'url', $url);
		}

		$caracteristiques = $doc->createElement('caracteristiques');
		$root->appendChild($caracteristiques);
		foreach ($wrapper->_getCaracteristiques() as $index => $cara)
		{
			$this->addDomNode($doc, $caracteristiques, 'Cara' . ($index+1), $cara);
		}

		$declinaisons = $doc->createElement('declinaisons');
		$root->appendChild($declinaisons);
		if ($product instanceof catalog_persistentdocument_productdeclination)
		{
			$declinations = $product->getDeclinedProduct()->getPublishedDeclinationsInShop($export->getShop());
			foreach($declinations as $declination)
			{
				$declinaison = $doc->createElement('declinaison');
				$declinaisons->appendChild($declinaison);
				$wrapperDeclination = new shoppingflux_Productwrapper($declination->getId(), $export);
				$this->addDomNode($doc, $declinaison, 'id_enfant', $wrapperDeclination->_getId());
				$this->addDomNode($doc, $declinaison, 'prix', $wrapperDeclination->_getPrix());
				if ($wrapperDeclination->hasDiscount())
				{
					$this->addDomNode($doc, $declinaison, 'prix-barre', $wrapperDeclination->_getPrix_Barre());
				}
				$this->addDomNode($doc, $declinaison, 'quantite', $wrapperDeclination->_getQuantite());
				$this->addDomNode($doc, $declinaison, 'ean', $wrapperDeclination->_getEan());
				$this->addDomNode($doc, $declinaison, 'frais-de-port', $wrapperDeclination->_getFrais_De_Port());
				$images = $doc->createElement('images');
				$declinaison->appendChild($images);
				foreach ($wrapperDeclination->_getImages() as $url)
				{
					$this->addDomNode($doc, $images, 'image', $url);
				}
				$attributs = $doc->createElement('attributs');
				$declinaison->appendChild($attributs);
				foreach ($wrapperDeclination->_getAttributs() as $name => $value)
				{
					$this->addDomNode($doc, $attributs, $name, $value);
				}

			}
		}
		$this->addDomNode($doc, $root, 'fil-ariane', $wrapper->_getFil_Ariane());
		$this->addDomNode($doc, $root, 'manufacturer', $wrapper->_getManufacturer());
		$this->addDomNode($doc, $root, 'supplier', $wrapper->_getSupplier());
		$this->exported[$masterProductId] = true;
		return $doc->saveXML($doc->documentElement);
	}
}