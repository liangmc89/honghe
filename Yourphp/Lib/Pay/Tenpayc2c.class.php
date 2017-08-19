<?php
if(!defined("Yourphp")) exit("Access Denied");
class Tenpayc2c extends Think {
	public $config = array()  ;

    public function __construct($config=array()) {
        $this->config = $config;
		$this->config['return_url'] =  return_url('Tenpayc2c');
    }
   public function setup(){
	   
	$modules['pay_name'] = '财付通-中介担保';
    $modules['pay_code']    = 'Tenpayc2c';
    $modules['pay_desc']    = '财付通(中介担保)（www.tenpay.com） - 腾讯旗下在线支付平台，通过国家权威安全认证，支持各大银行网上支付，免支付手续费。';
    $modules['is_cod']  = '0';
    $modules['is_online']  = '1';
    $modules['author']  = 'lin';
    $modules['website'] = 'http://www.tenpay.com';
    $modules['version'] = '1.0.0';
    $modules['config']  = array(
        array('name' => 'tenpay_account',   'type' => 'text', 'value' => ''),
        array('name' => 'tenpay_key',       'type' => 'text', 'value' => ''),
        array('name' => 'tenpay_type',      'type' => 'text','value' => '1'),
    );
	return $modules;
   }
    function get_code()
    {
        $version = '2';
        $cmdno = '12';
        $encode_type = 2;
        /* 平台提供者,代理商的财付通账号 */
        $chnid = $this->config['tenpay_account'];

        /* 收款方财付通账号 */
        $seller = $this->config['tenpay_account'];

        /* 商品名称 */
        if (!empty($this->config['order_id']))
        {
            //$mch_name = get_goods_name_by_id($order['order_id']);
            $mch_name = $this->config['order_sn'];
        }
        /* 总金额 */
        $mch_price = floatval($this->config['order_amount']) * 100;

        /* 物流配送说明 */
        $transport_desc = '';
        $transport_fee = '';

        /* 交易说明 */
        $mch_desc = $this->config['order_sn'];
        $need_buyerinfo = '2' ;

        /* 交易类型：2、虚拟交易，1、实物交易 */
        $mch_type = $this->config['tenpay_type'];

        /* 获得订单的流水号，补零到10位 */
        $mch_vno = $this->config['order_sn'];

        /* 返回的路径 */
        $mch_returl = $this->config['return_url'];
        $show_url   = $this->config['return_url'];
        $attach = '';

        /* 数字签名 */
        $sign_text = "chnid=" . $chnid . "&cmdno=" . $cmdno . "&encode_type=" . $encode_type . "&mch_desc=" . $mch_desc . "&mch_name=" . $mch_name . "&mch_price=" . $mch_price ."&mch_returl=" . $mch_returl . "&mch_type=" . $mch_type . "&mch_vno=" . $mch_vno . "&need_buyerinfo=" . $need_buyerinfo ."&seller=" . $seller . "&show_url=" . $show_url . "&version=" . $version . "&key=" . $this->config['tenpay_key'];

        $sign =md5($sign_text);

        /* 交易参数 */
        $parameter = array(
            'attach'            => $attach,
            'chnid'             => $chnid,
            'cmdno'             => $cmdno,                     // 业务代码, 财付通支付支付接口填  1
            'encode_type'       => $encode_type,                //编码标准
            'mch_desc'          => $mch_desc,
            'mch_name'          => $mch_name,
            'mch_price'         => $mch_price,                  // 订单金额
            'mch_returl'        => $mch_returl,                 // 接收财付通返回结果的URL
            'mch_type'          => $mch_type,                   //交易类型
            'mch_vno'           => $mch_vno,             // 交易号(订单号)，由商户网站产生(建议顺序累加)
            'need_buyerinfo'    => $need_buyerinfo,             //是否需要在财付通填定物流信息
            'seller'            => $seller,  // 商家的财付通商户号
            'show_url'          => $show_url,
            'transport_desc'    => $transport_desc,
            'transport_fee'     => $transport_fee,
            'version'           => $version,                    //版本号 2
            'sign'              => $sign,                       // MD5签名
            'sys_id'            => '542554970'                  //ecshop C账号 不参与签名
        );

        $button  = '<br /><form style="text-align:center;" action="https://www.tenpay.com/cgi-bin/med/show_opentrans.cgi " target="_blank" style="margin:0px;padding:0px" >';

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
        $retcode        = $_GET['retcode'];
        $status         = $_GET['status'];
        $seller         = $_GET['seller'];
        $total_fee      = $_GET['total_fee'];
        $trade_price    = $_GET['trade_price'];
        $transport_fee  = $_GET['transport_fee'];
        $buyer_id       = $_GET['buyer_id'];
        $chnid          = $_GET['chnid'];
        $cft_tid        = $_GET['cft_tid'];
        $mch_vno        = $_GET['mch_vno'];
        $attach         = !empty($_GET['attach']) ? $_GET['attach'] : '';
        $version        = $_GET['version'];
        $sign           = $_GET['sign'];

        $log_id     = $mch_vno;
        //$log_id = str_replace($attach, '', $mch_vno); //取得支付的log_id

        /* 如果$retcode大于0则表示支付失败 */
        if ($retcode > 0)
        {
            //echo '操作失败';
            return false;
        }

        /* 检查数字签名是否正确 */
        $sign_text = "buyer_id=" . $buyer_id . "&cft_tid=" . $cft_tid . "&chnid=" . $chnid . "&cmdno=" . $cmd_no . "&mch_vno=" . $mch_vno . "&retcode=" . $retcode . "&seller=" .$seller . "&status=" . $status . "&total_fee=" . $total_fee . "&trade_price=" . $trade_price . "&transport_fee=" . $transport_fee . "&version=" . $version . "&key=" . $this->config['tenpay_key'];
        $sign_md5 = strtoupper(md5($sign_text));
        if ($sign_md5 != $sign)
        {
            //echo '签名错误';
            return false;
        }
        elseif ($status = 3)
        {
            /* 改变订单状态为已付款 */
            order_pay_status($sp_billno,'2');
            return true;
        }
        else
        {
            //为止error
            return false;
        }
    }
}




?>