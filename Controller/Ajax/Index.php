<?php

namespace Arun\FreeShippingBar\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

// This Controller is used for to update the Free Shipping bar using AJAX 
// when the 'Add to Cart' happens and also 'mini cart' gets update
class Index extends Action
{
    protected $resultJsonFactory;
    protected $block;

    const BLOCK_PATH = 'Arun\FreeShippingBar\Block\FreeShippingBar';

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $response = array(
            'success' => false,
            'message' => ''
        );

        try {
            $blockName = self::BLOCK_PATH;
            $block = $this->_view->getLayout()->createBlock($blockName);
            $html = $block->getFreeShippingMessage();

            $response['success'] = true;
            $response['html'] = $html;
            $response['message'] = 'Block updated successfully';
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
