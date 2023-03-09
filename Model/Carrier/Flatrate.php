<?php

namespace Arun\FreeShippingBar\Model\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Flatrate
{

    const XML_PATH_FREE_SHIPPING_SUBTOTAL = "free_shipping_bar/free_shipping_bar/minimum_order_amount";


    protected $_checkoutSession;
    protected $_storeManager;
    protected $_scopeConfig;

    public function __construct(
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
    }

    // This function is used in-order to set to disable the Flat rate shipping method 
    // when the Free shipping is Available
    public function afterCollectRates(\Magento\OfflineShipping\Model\Carrier\Flatrate $flatRate, $result)
    {
        $scopeId = $this->_storeManager->getStore()->getId();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        // Get Minimum order amount value from system configuration of the custom module
        $freeShippingSubTotal = $this->_scopeConfig->getValue(self::XML_PATH_FREE_SHIPPING_SUBTOTAL, $storeScope, $scopeId);
        $baseSubTotal = $this->_checkoutSession->getQuote()->getBaseSubtotal();

        if (!empty($baseSubTotal) && !empty($freeShippingSubTotal)) {

            if ($baseSubTotal >= $freeShippingSubTotal) {
                return false;
            }
        }

        return $result;
    }
}
