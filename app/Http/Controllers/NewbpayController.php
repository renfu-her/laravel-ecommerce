<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Product;
use App\OrderProduct;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;

class NewbpayController extends Controller
{
    // 正式
//    protected $MerchantID = 'MS335031431';
//    protected $HashKey = 'oBMI6GkCEnICMBKkWV0A21nIvfLwjYYA';
//    protected $HashIV = 'CTY0wZMQdPReih1P';
//    protected $url = 'https://core.newebpay.com/MPG/mpg_gateway';
    // 測試
    protected $MerchantID = 'MS335031431';
    protected $HashKey = 'oBMI6GkCEnICMBKkWV0A21nIvfLwjYYA';
    protected $HashIV = 'CTY0wZMQdPReih1P';
    protected $url = 'https://ccore.newebpay.com/MPG/mpg_gateway';
    protected $ver = '1.6';
    protected $ReturnURL = "";
    protected $NotifyURL_atm = "";
    protected $NotifyURL_webatm = "";
    protected $NotifyURL_credit = "";
    protected $ClientBackURL = "";

    public function __construct()
    {
        $this->ReturnURL       = "https://" . $_SERVER['SERVER_NAME'] . "/thankyou"; 			//支付完成 返回商店網址
        $this->NotifyURL_atm   = "https://" . $_SERVER['SERVER_NAME'] . "/newebpay/atm_notify"; 		//支付通知網址
        $this->NotifyURL_webatm   = "https://" . $_SERVER['SERVER_NAME'] . "/newebpay/webatm_notify";
        $this->NotifyURL_credit = "https://" . $_SERVER['SERVER_NAME'] . "/newebpay/credit_notify"; 	//支付通知網址
        $this->ClientBackURL   = "https://" . $_SERVER['SERVER_NAME'] . "/methods/newebpay";

    }

    // 刷卡、ATM
    public function index(Request $request)
    {

        $input = $request->input();

        $date_now = date('Y-m-d H:i:s');
        $date_now = strtotime($date_now);


        if(!empty($input['charge_type'][0])){
            $charge_type = $input['charge_type'][0];
        } else {
            $charge_type = $input['charge_type'][1];
        }

        $payment = [
            'price' => (int)$input['price'],
            'ItemDesc' => '電子商品',
            'charge_type' => $charge_type,
            'email' => Auth::user()->email,
        ];
        
        $trade_info_arr = [
            'MerchantID' => $this->MerchantID,
            'RespondType' => 'JSON',
            'TimeStamp' => $date_now,
            'Version' => $this->ver,
            'MerchantOrderNo' => $date_now,
            'Amt' => $payment['price'],
            'ItemDesc' => $payment['ItemDesc'],
            $payment['charge_type'] => 1,
            'Email' => $payment['email'],
            'LoginType' => 0,
            'ReturnURL' => $this->ReturnURL
        ];

        if($payment['charge_type'] == 'VACC'){
            $trade_info_arr['NotifyURL'] = $this->NotifyURL_atm;
        } elseif($payment['charge_type'] == 'WEBATM'){
            $trade_info_arr['NotifyURL'] = $this->NotifyURL_webatm;
        } else {
            $trade_info_arr['NotifyURL'] = $this->NotifyURL_credit;
        }

        $charge_type_name = $payment['charge_type'];

        $TradeInfo = $this->create_mpg_aes_encrypt($trade_info_arr, $this->HashKey, $this->HashIV);
        $TradeSha = strtoupper(hash("sha256",$this->SHA256($this->HashKey, $TradeInfo , $this->HashIV)));

        
        $MerchantID = $this->MerchantID;
        $TradeInfo	= $TradeInfo;
        $TradeSha = $TradeSha;
        $RespondType = "JSON";
        $Version = $this->ver;
        $MerchantOrderNo = $date_now;
        $Amt = $payment['price'];
        $ItemDesc = $payment['ItemDesc'];
        $Email = $payment['email'];
        $charge_type_name = 1;
        $URL = $this->url;

        // 回寫 orders

        $order = $this->addToOrdersTables($request, null);

//        Mail::send(new OrderPlaced($order));
        // decrease the quantities of all the products in the cart
        $this->decreaseQuantities();

        Cart::instance('default')->destroy();
        session()->forget('coupon');

        return view('newbpay',
            compact('MerchantID', 'TradeInfo', 'TradeSha', 'RespondType',
                'Version', 'MerchantOrderNo', 'Amt', 'ItemDesc', 'Email', 'charge_type_name', 'URL'));

    }


    // 加密
    protected function create_mpg_aes_encrypt ($parameter = "" , $key = "", $iv = "") {
        $return_str = '';
        if (!empty($parameter)) {
            //將參數經過 URL ENCODED QUERY STRING
            $return_str = http_build_query($parameter);
        }
        return trim(bin2hex(openssl_encrypt($this->addpadding($return_str), 'aes-256-cbc',
            $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)));
    }


    protected function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    // HashKey AES 解密方法
    protected function create_aes_decrypt($parameter = "", $key = "", $iv = "") {
        return $this->strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv));
    }

    protected function strippadding($string) {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    // HashIV SHA256 加密方法
    public function SHA256($key="", $tradeinfo="", $iv=""){
        $HashIV_Key = "HashKey=".$key."&".$tradeinfo."&HashIV=".$iv;
        return $HashIV_Key;
    }

    protected function addToOrdersTables($request, $error)
    {
        // Insert into orders table
        $order = Order::create([
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'billing_email' => $request->email,
            'billing_name' => $request->name,
            'billing_address' => $request->address,
            'billing_city' => $request->city,
            'billing_province' => $request->province,
            'billing_postalcode' => $request->postalcode,
            'billing_phone' => $request->phone,
            'billing_name_on_card' => $request->name_on_card,
            'billing_discount' => getNumbers()->get('discount'),
            'billing_discount_code' => getNumbers()->get('code'),
            'billing_subtotal' => getNumbers()->get('newSubtotal'),
            'payment_gateway' => 'newbpay',
            'billing_tax' => getNumbers()->get('newTax'),
            'billing_total' => getNumbers()->get('newTotal'),
            'error' => $error,
        ]);

        // Insert into order_product table
        foreach (Cart::content() as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item->model->id,
                'quantity' => $item->qty,
            ]);
        }

        return $order;
    }

    protected function decreaseQuantities()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);

            $product->update(['quantity' => $product->quantity - $item->qty]);
        }
    }
}
