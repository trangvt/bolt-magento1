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

class Bolt_Boltpay_Model_Validator extends Mage_SalesRule_Model_Validator
{
    /**
     * Method for resetting rounding delta. Rounding deltas cause percentage discounts applied to an order to often get
     * off by $0.01 rounding errors because the validator used is a singleton. So every time collectTotals is called
     * it reuses the previous rounding deltas and causes rounding problems. Since Mage_SalesRule_Model_Validator doesn't
     * provide a method for resetting these before calling collectTotals, we created one.
     */
    public function resetRoundingDeltas()
    {
       $this->_roundingDeltas = [];
    }

}