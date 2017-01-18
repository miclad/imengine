<?

echo Page::includeComponent('payment:yandex', array('order_id' => $this->getConfig('order_id'), 'paymentType' => 'WM'));