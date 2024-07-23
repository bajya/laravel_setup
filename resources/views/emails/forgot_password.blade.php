<!DOCTYPE html>
<html lang="en">
<head>
<title>Laravel</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>

</head>
<body style="background-color:#fff; font-family:arial; font-size:14px;">
<center>
	<table width="800" cellspacing="0" style="border:1px solid #efefef; border-radius:5px; overflow:hidden">
		<tr>
			<td bgcolor="#516672" style="padding:10px 20px;">
				<table style="width:100%;">
					<tr>
						<td>
							 <div style="color:#fff;display: block;margin: 0 auto;width: 100%;text-align: center;">
							 	<img src="{{ asset('images/offerlogo.gif') }}" alt="homepage" class="dark-logo" style="width: 50px;">
							 	<div>Laravel </div>
							</div>
						</td>	
						 
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="text-align:center; padding:40px 50px 10px; border-bottom:1px solid #e1e1e1;" bgcolor="#f9f9f9">

				<p style="font-weight: bold; font-size: 18px; margin-top: 60px;">Dear, {{ ucfirst($name) }},</p>
                <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">{{ ucfirst($name) }} If you've lost your password or wish to reset it,<br>use the code below to get started.</p>
                <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">Email : {{ $email }}</p>
                <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">Code : {{ $forgot_code }}</p>
			</td>
		</tr>
		{{-- <tr><td style="border-bottom:1px solid #efefef">&nbsp;</td></tr> --}}
		<tr>
			<td style="text-align:center; padding:20px 10px 20px; font-size:13px; color:#666;">Copyright @ {{ date('Y') }} Laravel. All rights reserved.</td>
		</tr>

	</table>
</center>
</body>
</html>

