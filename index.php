<?php
// Just set your authorization key
$token = 'YOUR AUTHORIZATION KEY';

function verify_envato_purchase_code($token, $code_to_verify) {
	$code_to_verify = urldecode($code_to_verify);

	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code_to_verify}",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 20,

		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer {$token}",
		)
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$output = json_decode(curl_exec($ch), true);
	curl_close($ch);
	return $output;
}

function get_readable_date( $date ) {
	return date_format( date_create( $date ),"d M Y" );	
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Purchase Code Validator</title>
	<style type="text/css">
		body{
			font-size: 16px;
			line-height: 22px;
			font-family: verdana;
		}
		.container{
			width: 600px;
			margin: 80px auto;
		}
		.purchase-info{
			margin-bottom: 50px;
		}
		.error{
			color: #e00;
		}
		.info{
			color: #00e;
		}
		.success{
			color: #0d0;
		}
		strong {
			width: 180px;
			display: inline-block;
		}
		button,
		input{
			padding: 10px 15px;
			width: 300px;
			border: 1px solid #1085e4;
			border-radius: 4px;
			font-size: 15px;
			line-height: 22px;
			outline: 0;
			transition: 0.3s ease;
		}
		button{
			width: 100px;
			background: #1085e4;
			color: #fff;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="purchase-info">
			<?php
				if (isset($_POST['code']) && '' != $_POST['code']):
					$data  = verify_envato_purchase_code($token, trim($_POST['code']) );

					if ( isset($data['buyer']) && '' != $data['buyer'] ):

						$sold_at 		 = get_readable_date($data['sold_at']);
						$supported_until = get_readable_date($data['supported_until']);
						$diff_date 		 = date_diff( date_create( $sold_at ), date_create( $supported_until ));
						$support_text 	 = ($diff_date->format("%R%a") > 0) ? '<span class="success">Valid for support</span>' : '<span class="error">Invalid for supoprt</span>';
					?>
					<p class="info"><strong>Item Name:</strong> <?php echo $data['item']['name'] ?></p>
					<p class="info"><strong>Buyer:</strong> <?php echo $data['buyer'] ?></p>
					<p class="info"><strong>Amount:</strong> <?php echo $data['amount'] ?></p>
					<p class="info"><strong>Quantity:</strong> <?php echo $data['purchase_count'] ?></p>
					<p class="info"><strong>Purchased At:</strong> <?php echo $sold_at ?></p>
					<p class="info"><strong>Supported Until:</strong> <?php echo $supported_until ?></p>
					<p class="info"><strong>Supported Status:</strong> <?php echo $support_text ?></p>
					<p class="info"><strong>License Type:</strong> <?php echo $data['license'] ?></p>
				<?php else: ?>
					<p class="error">Invalid Purchased Code</p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<form action="#" method="POST">
			<p class="info">Enter the purchase code for getting the purchase info.</p>
			<input type="text" name="code" placeholder="Enter code">
			<button type="submit">Validate</button>
		</form>
	</div>
</body>
</html>