<?php
namespace qashops\demo\Business;

/**
 * class OrderLineManager
 */
class OrderLineManager
{
    /**
     * Consigue el stock bloqueado sobre un producto
     *
     * @param integer $productId
     * @param boolean $cache
     * @param integer $cacheDuration
     *
     * @return integer
     */
    public static function getBlockedStockQuantityForProduct($productId, $cache, $cacheDuration)
    {
        if ($cache) {
            return OrderLine::getDb()->cache(function () use ($productId) {
                return OrderLine::find()
                    ->select('SUM(quantity) as quantity')
                    ->joinWith('order')
                    ->where("(order.status = '" . Order::STATUS_PENDING . "' OR order.status = '" . Order::STATUS_PROCESSING . "' OR order.status = '" . Order::STATUS_WAITING_ACCEPTANCE . "') AND order_line.product_id = $productId")
                    ->scalar();
            }, $cacheDuration);
        }

        return OrderLine::find()
            ->select('SUM(quantity) as quantity')
            ->joinWith('order')
            ->where("(order.status = '" . Order::STATUS_PENDING . "' OR order.status = '" . Order::STATUS_PROCESSING . "' OR order.status = '" . Order::STATUS_WAITING_ACCEPTANCE . "') AND order_line.product_id = $productId")
            ->scalar();
    }
}
