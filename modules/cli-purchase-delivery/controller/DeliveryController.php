<?php
/**
 * DeliveryController
 * @package cli-purchase-delivery
 * @version 0.0.1
 */

namespace CliPurchaseDelivery\Controller;

use Purchase\Model\Purchase;
use Purchase\Model\PurchaseDelivery;
use Cli\Library\Bash;
use LibCourier\Library\Courier;

class DeliveryController extends \Cli\Controller
{
    public function checkAction()
    {
        $id = $this->req->param->id;

        $purchase = Purchase::getOne(['id' => $id]);
        $courier_receipt = $purchase->courier_receipt;

        $delivery = PurchaseDelivery::getOne(['purchase' => $purchase->id]);

        if (!$courier_receipt) {
            return Bash::json(['error' => false]);
        }

        // 5 => Delivered
        // 7 => Returned
        if (in_array($purchase->status, [5,7])) {
            return Bash::json(['error' => false]);
        }

        $courier = json_decode($purchase->courier)->provider;
        $result = Courier::track($courier->code, $courier_receipt);

        if (!$result) {
            return Bash::json(['error' => true, 'delay' => 900]);
        }

        $set = [
            'data' => json_encode($result),
            'purchase' => $purchase->id
        ];

        if ($delivery) {
            PurchaseDelivery::set($set, ['id' => $delivery->id]);
        } else {
            PurchaseDelivery::create($set);
        }

        // check the status of the delivery
        $last_status = 0;
        foreach ($result as $res) {
            if ($last_status < $res['status']) {
                $last_status = $res['status'];
            }
        }

        if (!$last_status) {
            return Bash::json(['error' => true, 'delay' => 900]);
        }

        $purchase_status = $purchase->status;

        if ($purchase_status == 3) {
            $purchase_status = 4;
        }

        $last_status++;

        if ($last_status > $purchase_status) {
            $purchase_status = $last_status;
        }

        Purchase::set(['status' => $purchase_status], ['id' => $purchase->id]);

        if (in_array($purchase_status, [5,7])) {
            return Bash::json(['error' => false]);
        } else {
            return Bash::json(['error' => true, 'delay' => 900]);
        }
    }
}
