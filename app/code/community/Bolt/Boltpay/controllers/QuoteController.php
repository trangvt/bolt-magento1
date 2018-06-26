<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the Bolt extension
 * to a newer versions in the future. If you wish to customize this extension
 * for your needs please refer to http://www.magento.com for more information.
 *
 * @category   Bolt
 * @package    Bolt_Boltpay
 * @copyright  Copyright (c) 2018 Bolt Financial, Inc (http://www.bolt.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Bolt_Boltpay_QuoteController
 *
 * Validate immutable quote with session quote in Magento system
 */
class Bolt_Boltpay_QuoteController extends Mage_Core_Controller_Front_Action
{

    /**
     * @return mixed
     * @throws Varien_Exception
     */
    public function validateAction()
    {
        $requestJson = file_get_contents('php://input');
        $requestData = json_decode($requestJson);

        $immutableQuoteId = $requestData->immutable_quote_id;
        /** @var Mage_Sales_Model_Quote $immutableQuote */
        $immutableQuote = Mage::getModel('sales/quote')->load($immutableQuoteId);

        /** @var Mage_Sales_Model_Quote $sessionQuote */
        $sessionQuote = Mage::getSingleton('checkout/session')->getQuote();

        $responseData = array(
            'result' => 'success'
        );

        // Validate cart total
        if ($sessionQuote->getGrandTotal() != $immutableQuote->getGrandTotal()) {
            $responseData['result'] = 'failure';
            $responseData['message'] = Mage::helper('boltpay')->__('Cart total does not match');
        }

        // Validate cart items (skus and qtys)
        if(!$this->validateCartItems($immutableQuote, $sessionQuote)){
            $responseData['result'] = 'failure';
            $responseData['message'] = Mage::helper('boltpay')->__('Cart items do not match');
        };

        $response = Mage::helper('core')->jsonEncode($responseData);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($response);
    }

    /**
     * Validate cart items of session quote and immutable quote
     * @param $immutableQuote
     * @param $sessionQuote
     * @return mixed
     */
    protected function validateCartItems($immutableQuote, $sessionQuote)
    {
        $immutableItems = array();
        foreach ($immutableQuote->getAllItems() as $immutableItem) {
            $immutableItems[$immutableItem->getId] = $immutableItem->getQty();
        }
        $sessionItems = array();
        foreach ($sessionQuote->getAllItems() as $sessionItem) {
            $sessionItems[$sessionItem->getId] = $sessionItem->getQty();
        }

        if (array_diff_assoc($immutableItems, $sessionItems) || array_diff_assoc($sessionItems, $immutableItems)){
            return false;
        }

        return true;
    }
}
