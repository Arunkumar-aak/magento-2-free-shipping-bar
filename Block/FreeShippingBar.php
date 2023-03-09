<?php

namespace Arun\FreeShippingBar\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class FreeShippingBar extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    protected $checkoutSession;
    protected $priceHelper;

    const XML_PATH_IS_ENABLED = 'free_shipping_bar/free_shipping_bar/enabled';
    const XML_PATH_MINIMUM_ORDER_AMOUNT = 'free_shipping_bar/free_shipping_bar/minimum_order_amount';
    const XML_PATH_BACKGROUND_COLOR = 'free_shipping_bar/free_shipping_bar/background_color';
    const XML_PATH_TEXT_COLOR = 'free_shipping_bar/free_shipping_bar/text_color';

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
    }

    // function used to get whether the fre shipping module is enabled or not
    // returns boolean either true or false
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    // function used to get minimum order amount value configured by the Admin    
    public function getMinimumAmount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MINIMUM_ORDER_AMOUNT, ScopeInterface::SCOPE_STORE);
    }

    // function used to get background color for the bar configured by the Admin    
    public function getBackgroundColor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BACKGROUND_COLOR, ScopeInterface::SCOPE_STORE);
    }

    // function used to get text color for the bar configured by the Admin    
    public function getTextColor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TEXT_COLOR, ScopeInterface::SCOPE_STORE);
    }

    // function used to get formatted price based on the currency      
    public function formatPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    // function used to get message configured from the admin 
    // Here, there are 3 messages which can be used 
    // 1. cart_is_empty_message -> this is used when the cart is empty  
    // 2. cart_value_less_than_minimum_message -> this is used when the cart's total is less than minimum order amount      
    // 3. success_message -> this is used when the user avail free shipping   
    // returns string where the details are replaced based on the config added   
    public function getConfiguredMessage($messageCode, $cartTotal, $minimumOrderAmount)
    {
        $message = $this->scopeConfig->getValue('free_shipping_bar/free_shipping_bar/' . $messageCode, ScopeInterface::SCOPE_STORE);
        $remainingAmount = max(0, $minimumOrderAmount - $cartTotal);

        if (!empty($message)) {
            if (strpos($message, '{minimum_order_amount}') !== false) {
                $message = str_replace('{minimum_order_amount}', $this->formatPrice($minimumOrderAmount), $message);
            }
            if (strpos($message, '{remaining_amount}') !== false) {
                $message = str_replace('{remaining_amount}', $this->formatPrice($remainingAmount), $message);
            }
            if (strpos($message, '{cart_total}') !== false) {
                $message = str_replace('{cart_total}', $this->formatPrice($cartTotal), $message);
            }
        }

        return $message;
    }

    // function used by the block to get the message on run time
    // returns the html string
    public function getFreeShippingMessage()
    {
        $minimumOrderAmount = $this->getMinimumAmount();
        $cartTotal = $this->checkoutSession->getQuote()->getSubtotal();

        if ($cartTotal == 0) {
            $message = $this->getConfiguredMessage('cart_is_empty_message', $cartTotal, $minimumOrderAmount);
        } elseif ($cartTotal < $minimumOrderAmount) {
            $message = $this->getConfiguredMessage('cart_value_less_than_minimum_message', $cartTotal, $minimumOrderAmount);
        } else {
            $message = $this->getConfiguredMessage('success_message', $cartTotal, $minimumOrderAmount);
        }

        $backgroundColor = $this->getBackgroundColor();
        $textColor = $this->getTextColor();
        $message = '<div class="free_shipping_bar_area" style="background-color:' . $backgroundColor . '; color:' . $textColor . ';" >' . $message . '</div>';

        return $message;
    }
}
