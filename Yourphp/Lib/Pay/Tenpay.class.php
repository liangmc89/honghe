<?php
if(!defined("Yourphp")) exit("Access Denied");
class Tenpay extends Think {
	public $config = array()  ;

    public function __construct($config=array()) {
        $this->config = $config;
		$this->config['return_url'] =  return_url('Tenpay');
    }
   public function setup(){
	   
	$modules['pay_name'] = '财付通-即时到帐';
    $modules['pay_code']    = 'Tenpay';
    $modules['pay_desc']    = '财付通(即时到帐)（www.tenpay.com） - 腾讯旗下在线支付平台，通过国家权威安全认证，支持各大银行网上支付，免支付手续费。';
    $modules['is_cod']  = '0';
    $modules['is_online']  = '1';
    $modules['author']  = 'lin';
    $modules['website'] = 'http://www.tenpay.com';
    $modules['version'] = '2.0.0';
    $modules['config']  = array(
        array('name' => 'tenpay_account',   'type' => 'text', 'value' => ''),
        array('name' => 'tenpay_key',       'type' => 'text', 'value' => ''),
        array('name' => 'magic_string',     'type' => 'text', 'value' => '')
    );
	return $modules;
   }
    /**
     * 生成支付代码
     */
    function get_code($order, $payment)
    {
        $cmd_no = '1';

        /* 获得订单的流水号，补零到10位 */
        $sp_billno = $$this->config['order_sn'];

        /* 交易日期 */
        $today = date('Ymd');

        /* 将商户号+年月日+流水号 */
        $bill_no = str_pad($$this->config['order_sn'], 10, 0, STR_PAD_LEFT);
        $transaction_id = $this->config['tenpay_account'].$today.$bill_no;

        /* 银行类型:支持纯网关和财付通 */
        $bank_type = '0';

        /* 订单描述，用订单号替代 */
        if (!empty($this->config['order_id']))
        {
            $desc = $this->config['order_sn'];

        }
        /* 编码标准 */
        if (!defined('EC_CHARSET') || EC_CHARSET == 'utf-8')
        {
            $desc = iconv('utf-8', 'gbk', $desc);
        }

        /* 返回的路径 */
        $return_url = $this->config['return_url'];

        /* 总金额 */
        $total_fee = floatval($this->config['order_amount']) * 100;

        /* 货币类型 */
        $fee_type = '1';

        /* 财付通风险防范参数 */
        $spbill_create_ip = $_SERVER['REMOTE_ADDR'];

        /* 数字签名 */
        $sign_text = "cmdno=" . $cmd_no . "&date=" . $today . "&bargainor_id=" . $this->config['tenpay_account'] .
          "&transaction_id=" . $transaction_id . "&sp_billno=" . $sp_billno .
          "&total_fee=" . $total_fee . "&fee_type=" . $fee_type . "&return_url=" . $return_url .
           "&spbill_create_ip=" . $spbill_create_ip . "&key=" . $this->config['tenpay_key'];
        $sign = strtoupper(md5($sign_text));

        /* 交易参数 */
        $parameter = array(
            'cmdno'             => $cmd_no,                     // 业务代码, 财付通支付支付接口填  1
            'date'              => $today,                      // 商户日期：如20051212
            'bank_type'         => $bank_type,                  // 银行类型:支持纯网关和财付通
            'desc'              => $desc,                       // 交易的商品名称
            'purchaser_id'      => '',                          // 用户(买方)的财付通帐户,可以为空
            'bargainor_id'      => $this->config['tenpay_account'],  // 商家的财付通商户号
            'transaction_id'    => $transaction_id,             // 交易号(订单号)，由商户网站产生(建议顺序累加)
            'sp_billno'         => $sp_billno,                  // 商户系统内部的定单号,最多10位
            'total_fee'         => $total_fee,                  // 订单金额
            'fee_type'          => $fee_type,                   // 现金支付币种
            'return_url'        => $return_url,                 // 接收财付通返回结果的URL
            'attach'            => $attach,                     // 用户自定义签名
            'sign'              => $sign,                       // MD5签名
            'spbill_create_ip'  => $spbill_create_ip,           //财付通风险防范参数
            'sys_id'            => '542554970',                 //ecshop C账号 不参与签名
            'sp_suggestuser'    => '1202822001'                 //财付通分配的商户号

        );

        $button  = '<br /><form style="text-align:center;" action="https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi" target="_blank" style="margin:0px;padding:0px" >';

        foreach ($parameter AS $key=>$val)
        {
            $button  .= "<input type='hidden' name='$key' value='$val' />";
        }

        $button  .= '<input type="submit"  value="提交" /></form><br />';

        return $button;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        /*取返回参数*/
        $cmd_no         = $_GET['cmdno'];
        $pay_result     = $_GET['pay_result'];
        $pay_info       = $_GET['pay_info'];
        $bill_date      = $_GET['date'];
        $bargainor_id   = $_GET['bargainor_id'];
        $transaction_id = $_GET['transaction_id'];
        $sp_billno      = $_GET['sp_billno'];
        $total_fee      = $_GET['total_fee'];
        $fee_type       = $_GET['fee_type'];
        $attach         = $_GET['attach'];
        $sign           = $_GET['sign'];



        /* 如果pay_result大于0则表示支付失败 */
        if ($pay_result > 0)
        {
            return false;
        }

        /* 检查支付的金额是否相符 */
        if (!check_money($log_id, $total_fee / 100))
        {
            return false;
        }

        /* 检查数字签名是否正确 */
        $sign_text  = "cmdno=" . $cmd_no . "&pay_result=" . $pay_result .
                          "&date=" . $bill_date . "&transaction_id=" . $transaction_id .
                            "&sp_billno=" . $sp_billno . "&total_fee=" . $total_fee .
                            "&fee_type=" . $fee_type .
                            "&key=" . $this->config['tenpay_key'];
        $sign_md5 = strtoupper(md5($sign_text));
        if ($sign_md5 != $sign)
        {
            return false;
        }
        else
        {
            /* 改变订单状态 */
            order_pay_status($sp_billno,'2');
            return true;
        }
    }
}




?>