<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/**
 * Class contains cancel order
 * *
 */
namespace Apptha\Airhotels\Controller\Mytrip;
use Magento\Framework\Controller\ResultFactory;

class Reject extends \Magento\Framework\App\Action\Action
{

    /**
     * cancel the order
     * *
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $hostOrderModel = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($orderId, 'order_item_id');
        $hostOrderModel->setCancelRequestStatus(4);
        $hostOrderModel->save();
        $this->sendOrderCancelRejectEmail($hostOrderModel);
        $this->messageManager->addSuccess(__('Booking Rejected successfully.'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
    /**
     * Handling cancel reject email
     * Apptha\Airhotels\Model\Hostorder $hostOrder
     *
     * return void
     **/
    public function sendOrderCancelRejectEmail($hostOrder){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // loading host details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        // loading customer details
        $customer = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getCustomerId());
        $customerName=$customer->getName();
        $customerEmail=$customer->getEmail();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminEmail = $admin->getAdminEmail();
        $templateId = 'airhotels_customer_order_cancel_reject';
        /* Sender Detail */
        $senderInfo = [
            'name' => $hostName,
            'email' => $recipient
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $customerName,
            'email' => $customerEmail
        ];
        /* Template variables Detail */
        $emailTempVariables = (array(
            'cname' => $customerName,
            'order_id' => $hostOrder->getOrderId()
        ));
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId,$adminEmail );
    }
}
