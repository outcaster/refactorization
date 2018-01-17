<?php
namespace qashops\demo\Business;

/**
 * class BlockedStockManager
 */
class BlockedStockManager
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
            return BlockedStock::getDb()->cache(function () use ($productId) {
                return BlockedStock::find()
                    ->select('SUM(quantity) as quantity')
                    ->joinWith('shoppingCart')
                    ->where("blocked_stock.product_id = $productId AND blocked_stock_date > '" . date('Y-m-d H:i:s') . "' AND (shopping_cart_id IS NULL OR shopping_cart.status = '" . ShoppingCart::STATUS_PENDING . "')")
                    ->scalar();
            }, $cacheDuration);
        }

        return BlockedStock::find()
            ->select('SUM(quantity) as quantity')
            ->joinWith('shoppingCart')
            ->where("blocked_stock.product_id = $productId AND blocked_stock_to_date > '" . date('Y-m-d H:i:s') . "' AND (shopping_cart_id IS NULL OR shopping_cart.status = '" . ShoppingCart::STATUS_PENDING . "')")
            ->scalar();
    }
}
