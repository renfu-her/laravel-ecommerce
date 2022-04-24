<form name='newebpay' id="data_set" method='post' action='{{ $URL}}'>
    <input type='hidden' id='MerchantID' name='MerchantID' value='{{ $MerchantID }}' />
    <input type='hidden' id='TradeInfo' name='TradeInfo' value='{{ $TradeInfo }}'>
    <input type='hidden' id='TradeSha' name='TradeSha' value='{{ $TradeSha }}'>
    <input type='hidden' id='Version' name='Version' value='{{ $Version }}'>
    <script type="text/javascript">document.getElementById("data_set").submit();</script>
</form>
