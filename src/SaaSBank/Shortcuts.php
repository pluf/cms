<?php
Pluf::loadFunction('Pluf_Shortcuts_RenderToResponse');

/**
 * یک نمونه جدید از پرداخت ایجاد می‌کند
 *
 * در صورتی که پیش از این نمونه‌ای برای پرداخت ایجاد شده باشد آن را به عنوان
 * نتیجه برمی‌گرداند.
 *
 * @param SaaSBank_Receipt $object            
 * @return SaaSBank_Receipt
 */
function SaaSBank_Shortcuts_receiptFactory ($object)
{
    if ($object == null || ! isset($object))
        return new Bank_Receipt();
    return $object;
}

/**
 * یک متور پرداخت را پیدا می‌کند.
 *
 * @param unknown $type            
 * @throws SaaSBank_Exception_EngineNotFound
 * @return unknown
 */
function SaaSBank_Shortcuts_GetEnginer404 ($type)
{
    $items = SaaSBank_Service::engines();
    foreach ($items as $item) {
        if ($item->getType() === $type) {
            return $item;
        }
    }
    throw new SaaSBank_Exception_EngineNotFound();
}