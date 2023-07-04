<div class="qr_invoice" id="qr_invoice">
	<img src="<?php echo esc_url("https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=".$invoice."&choe=UTF-8") ?>"/><br/>
	<p><?php echo $order_detail ?></p>
	<textarea readonly id="invoice_text"><?php echo esc_textarea($invoice) ?></textarea>
	<br>
	<button id='invoice_copy_button'>Copiar Lightning Invoice</button>
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
	
</script>

<style>
	div.qr_invoice {
	 text-align:center
	}
</style>