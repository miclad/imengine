<?

DataTable::setEventHandler('users', 'onAfterUpdate', array('DBEventHandlers', 'onAfterUserUpdate'));
DataTable::setEventHandler('banners', 'onBeforeSave', array('DBEventHandlers', 'prepareBannerData'));
DataTable::setEventHandler('banners', 'onAfterUpdate', array('DBEventHandlers', 'bannerAfterSave'));
DataTable::setEventHandler('banners', 'onAfterInsert', array('DBEventHandlers', 'bannerAfterSave'));
DataTable::setEventHandler('banners', 'onAfterDelete', array('DBEventHandlers', 'bannerAfterDelete'));

DataTable::setEventHandler('order_items', 'onAfterDelete', array('DBEventHandlers', 'updateOrderSum'));
DataTable::setEventHandler('order_items', 'onAfterInsert', array('DBEventHandlers', 'orderItemAdded'));
DataTable::setEventHandler('order_items', 'onAfterInsert', array('DBEventHandlers', 'updateOrderSum'));
DataTable::setEventHandler('order_items', 'onAfterUpdate', array('DBEventHandlers', 'orderItemAdded'));
DataTable::setEventHandler('order_items', 'onAfterUpdate', array('DBEventHandlers', 'updateOrderSum'));

DataTable::setEventHandler('orders', 'onAfterUpdate', array('DBEventHandlers', 'orderSaveHistory'));
DataTable::setEventHandler('orders', 'onAfterUpdate', array('DBEventHandlers', 'updateBonus'));
DataTable::setEventHandler('orders', 'onAfterDelete', array('DBEventHandlers', 'bonusReturn'));

DataTable::setEventHandler('discount_cards', 'onAfterInsert', array('DBEventHandlers', 'discountCardAdded'));

DataTable::setEventHandler('structure', 'onBeforeSave', array('DBEventHandlers', 'makeItemUrl'));
DataTable::setEventHandler('folders', 'onBeforeSave', array('DBEventHandlers', 'makeItemUrl'));

DataTable::setEventHandler('folders', 'onAfterUpdate', array('DBEventHandlers', 'onAfterUrlChanged'));
DataTable::setEventHandler('structure', 'onAfterUpdate', array('DBEventHandlers', 'onAfterUrlChanged'));

/* components */

DataTable::setEventHandler('pages_components', 'onAfterUpdate', array('DBEventHandlers', 'onAfterComponentSave'));
DataTable::setEventHandler('pages_components', 'onAfterInsert', array('DBEventHandlers', 'onAfterComponentSave'));

DataTable::setEventHandler('pages_components', 'onAfterUpdate', array('DBEventHandlers', 'updateComponentSettings'));
DataTable::setEventHandler('pages_components', 'onAfterInsert', array('DBEventHandlers', 'updateComponentSettings'));

DataTable::setEventHandler('articles', 'onAfterUpdate', array('DBEventHandlers', 'saveItemsPropertyValues'));
DataTable::setEventHandler('articles', 'onAfterInsert', array('DBEventHandlers', 'saveItemsPropertyValues'));

DataTable::setEventHandler('catalog', 'onAfterUpdate', array('DBEventHandlers', 'saveCatalogPropertyValues'));
DataTable::setEventHandler('catalog', 'onAfterInsert', array('DBEventHandlers', 'saveCatalogPropertyValues'));
DataTable::setEventHandler('catalog', 'onAfterUpdate', array('DBEventHandlers', 'updateCatalogPrices'));
DataTable::setEventHandler('catalog', 'onAfterInsert', array('DBEventHandlers', 'updateCatalogPrices'));
DataTable::setEventHandler('catalog_prices', 'onAfterUpdate', array('DBEventHandlers', 'catalogPricesChanged'));
DataTable::setEventHandler('catalog_prices', 'onAfterInsert', array('DBEventHandlers', 'catalogPriceChanged'));


?>