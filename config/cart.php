<?php

return [
    // Minutes a cart lives after the first item is added (customer can extend).
    'expiry_minutes' => (int) env('CART_TIME', 30),

    // Minutes an applied-but-unused coupon stays reserved for the customer.
    'coupon_hold_minutes' => (int) env('COUPON_HOLD_TIME', 30),
];
