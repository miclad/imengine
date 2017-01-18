<?

echo Page::includeComponent('payment:yandex', array('return_link' => $this->getConfig('return_link'), 'order_id' => $this->getConfig('order_id'), 'paymentType' => 'PC'));