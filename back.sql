2017/07/17
用户微信充值一次，消费多次后退款引

INSERT INTO `ucenter`.`pool_balance` (`id`, `uid`, `order_id`, `amount`, `desc`, `before_amount`, `after_amount`, `remark`, `created_at`) VALUES ('105', '108593', '201707161252551636', '-3999.00', '提现', '3999.00', '0.00', '', '1500180775');
INSERT INTO `ucenter`.`pool_balance` (`id`, `uid`, `order_id`, `amount`, `desc`, `before_amount`, `after_amount`, `remark`, `created_at`) VALUES ('121', '108593', '201707171240021795', '-3999.00', '提现', '0.00', '-3999.00', '', '1500266402');
INSERT INTO `ucenter`.`pool_freeze` (`id`, `uid`, `order_id`, `amount`, `desc`, `before_amount`, `after_amount`, `remark`, `created_at`) VALUES ('93', '108593', '201707161252551636', '3999.00', '提现', '3999.00', '7998.00', '', '1500180775');
INSERT INTO `ucenter`.`pool_freeze` (`id`, `uid`, `order_id`, `amount`, `desc`, `before_amount`, `after_amount`, `remark`, `created_at`) VALUES ('106', '108593', '201707171240021795', '3999.00', '提现', '3999.00', '7998.00', '', '1500266402');


用户贷款入账，操作成了线下充值
INSERT INTO `ucenter`.`order` (`id`, `uid`, `platform_order_id`, `order_id`, `order_type`, `order_subtype`, `amount`, `status`, `desc`, `notice_status`, `notice_platform_param`, `created_at`, `updated_at`, `remark`, `platform`, `quick_pay`, `receipt_amount`, `counter_fee`, `discount_amount`) VALUES ('974', '98586', '', '201707151527451766', '1', 'line_down', '75565.00', '2', '用户：132xxxx5120充值', '1', '{\"orderPayType\":3}', '1500103665', '1500172324', '{\"accountName\":\"兴业银行信用卡中心\",\"bankName\":\"兴业银行\",\"payType\":3,\"referenceImg\":[\"http://img.che.com/che/170715/bd780d8c264f4f14b2cc98d69dcdc239.jpg\"],\"referenceNumber\":\"G0882200052845C\",\"transferDate\":\"2017-07-13\"}', '1', '0', '75565.00', '0.00', '0.00');
INSERT INTO `ucenter`.`pool_balance` (`id`, `uid`, `order_id`, `amount`, `desc`, `before_amount`, `after_amount`, `remark`, `created_at`) VALUES ('81', '98586', '201707151527451766', '75565.00', '充值', '25730.66', '101295.66', 'uid：98586；该笔入账应该是贷款入账，而非线下充值；', '1500103798');
