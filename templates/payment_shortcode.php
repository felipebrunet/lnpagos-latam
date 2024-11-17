<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>



<div class="wrapper" id="tabs">
	<ul class="aco">
		<li><a href="#tabs-1">Bitcoin Lightning</a></li>
		<li class="alts-btc"><a href="#tabs-2">Bitcoin Onchain</a></li>
	</ul>
		
	<div id="tabs-1">
		<div class="qr_invoice" id="qr_invoice">
			<img src="<?php echo esc_url("https://quickchart.io/chart?cht=qr&chld=H%7C1&chs=300x300&chl=".$invoice) ?>"/><br/>
			<p><?php echo esc_attr($order_detail) ?></p>
			<textarea readonly id="invoice_text"><?php echo esc_textarea($invoice) ?></textarea>
			<br>
			<button id='invoice_copy_button'>Copiar Lightning Invoice</button>
		</div>
	</div>

	<div id="tabs-2">
		<p><?php echo esc_attr($order_detail) ?></p>
		<br>
		<iframe width="360" height="740" src="<?php echo esc_url("https://widget.fixedfloat.com/?from=BTC&to=BTCLN&lockSend=true&lockReceive=true&address=".$invoice."&lockAddress=true&type=float") ?>"></iframe>
	</div>


</div>

      
<script type="text/javascript">
	var $ = jQuery;
	var check_payment_url = '<?php echo esc_url($check_payment_url) ?>';
	var order_id = <?php echo esc_attr($order_id) ?>;

	// Periodically check if the invoice got paid
	setInterval(function() {
			$.post(check_payment_url, {'order_id': order_id}).done(function(data) {
				var response = $.parseJSON(data);
				console.log(response);
				if (response['paid']) {
					window.location.replace(response['redirect']);
				}
			});
		}, 5000);

	// Copy into clipboard on click
	$('#invoice_copy_button').click(function() {
		$('#invoice_text').select();
		document.execCommand('copy');
		// alert("Lightning invoice copiado");
	});

	$( function() {
		$( "#tabs" ).tabs();
	} );

	
</script>

<style>
	div.qr_invoice {
	 text-align:center
	}
	.wrapper {
            text-align: center;
	}
	.wrapper ul {
		display: inline-block;
		margin: 0;
		padding: 0;
	}

	.alts-btc {
		<?php if ($alts_btc_enabled == "no") { ?>
		display: None;
		<?php } ?>
	} 





</style>
