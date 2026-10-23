<?php

return [
    // Minutes an applied-but-unused coupon stays reserved for the customer
    // before the coupons:release-holds job gives the use back.
    'coupon_hold_minutes' => (int) env('COUPON_HOLD_TIME', 30),
];
