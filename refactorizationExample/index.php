<?php
use qashops\demo\Business\OrderLineManager;
use qashops\demo\Business\BlockedStockManager;

/**
 * class Product
 */
class Product
{
    /**
     * Método demo
     *
     * @param integer $productId           productId
     * @param integer $quantityAvailable   quantityAvailable
     * @param boolean $cache               cache
     * @param integer $cacheDuration       cacheDuration
     * @param object  $securityStockConfig securityStockConfig
     *
     * @return integer
     */
    public static function stock(
        $productId,
        $quantityAvailable,
        $cache = false,
        $cacheDuration = 60,
        $securityStockConfig = null
    ) {
        //Consigue las cantidades
        $ordersQuantity       = OrderLineManager::getBlockedStockQuantityForProduct(
            $productId,
            $cache,
            $cacheDuration
        );
        $blockedStockQuantity = BlockedStockManager::getBlockedStockQuantityForProduct(
            $productId,
            $cache,
            $cacheDuration
        );

        return $this->computeAvailableStock(
            $ordersQuantity,
            $blockedStockQuantity,
            $quantityAvailable,
            $securityStockConfig
        );
    }

    /**
     * Cálcula la cantidad disponible
     *
     * @param integer $ordersQuantity       ordersQuantity
     * @param integer $blockedStockQuantity blockedStockQuantity
     * @param integer $quantityAvailable    quantityAvailable
     * @param object  $securityStockConfig  securityStockConfig
     *
     * @return integer
     */
    private function computeAvailableStock(
        $ordersQuantity,
        $blockedStockQuantity,
        $quantityAvailable,
        $securityStockConfig
    ) {
        // Calculamos las unidades disponibles si los stocks bloqueados devuelven algo
        if ($ordersQuantity != null || $blockedStockQuantity != null)) {
            if ($quantityAvailable >= 0) {
                $quantity = $quantityAvailable - @$ordersQuantity - @$blockedStockQuantity;
                if (!empty($securityStockConfig)) {
                    $quantity = ShopChannel::applySecurityStockConfig(
                        $quantity,
                        @$securityStockConfig->mode,
                        @$securityStockConfig->quantity
                    );
                }
                return $quantity > 0 ? $quantity : 0;
            }

            //Nota: de acuerdo al código original, este return devuelve un quantityAvailable negativo? me suena raro
            return $quantityAvailable;
        }

        // en otro caso, lógica por defecto
        if ($quantityAvailable >= 0) {
            if (!empty($securityStockConfig)) {
                $quantityAvailable = ShopChannel::applySecurityStockConfig(
                    $quantityAvailable,
                    @$securityStockConfig->mode,
                    @$securityStockConfig->quantity
                );
            }

            $quantityAvailable = $quantityAvailable > 0 ? $quantityAvailable : 0;
        }

        return $quantityAvailable;
    }
}
