<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta property="og:title" content="<?php echo $title_for_layout;?>" />
		<title><?php echo $title_for_layout;?></title>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="margin: 0; -webkit-text-size-adjust: none; background-color: #EEEEEE; padding: 0; width: 100% !important;" bgcolor="#EEEEEE">
		<style type="text/css">
			.preheaderContent div a:visited { color: #336699 !important; font-weight: normal !important; text-decoration: underline !important; }
			.headerContent a:visited { color: #336699 !important; font-weight: normal !important; text-decoration: underline !important; }
			.bodyContent div a:visited { color: #336699 !important; font-weight: normal !important; text-decoration: underline !important; }
			.footerContent div a:visited { color: #fff !important; font-weight: normal !important; text-decoration: underline !important; }
		</style>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="margin: 0; background-color: #EEEEEE; padding: 20px 0; height: 100% !important; width: 100% !important;" bgcolor="#EEEEEE">
				<tr>
					<td align="center" valign="top" style="border-collapse: collapse;">
						<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #FFFFFF; border: 1px solid #e1e1e1;" bgcolor="#FFFFFF">
							<tr>
								<td align="center" valign="top" style="border-collapse: collapse;">
									<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #32393f; border-bottom-width: 0;" bgcolor="#32393f">
										<tr>
											<!--<td width="100%" style="font-weight: bold; font-size: 34px; border-collapse: collapse; padding: 0 10px 0 20px; line-height: 100%; font-family: Arial; color: #202020; vertical-align: middle; text-align: left; display:none" valign="middle" align="left">
												<div>
													<h3 style="margin: 0; font-weight: 300; font-size: 26px; line-height: 100%; font-family: Helvetica,sans-serif; color: #FFFFFF; display: block; text-align: left;" align="left">
														<?php
														if (isset($emailTitle)) { 
															echo $emailTitle; 
														}
														?>
													</h3>
												</div>
											</td>-->
											<td style="font-weight: bold; font-size: 34px; border-collapse: collapse; padding: 15px 20px 15px 20px; line-height: 100%; font-family: Arial; color: #ffffff; vertical-align: middle; text-align: left;" valign="middle" align="left">
												<span>
													<?php
													echo $this->Eramba->getLogo(DEFAULT_LOGO_WHITE_URL, true, array(
														'style' => 'max-width: 180px; line-height: 100%; outline: none; border: 0; text-decoration: none; height: auto;'
													));
													?>
												</span>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top" style="border-collapse: collapse;">
									<table border="0" cellpadding="0" cellspacing="0" width="600">
										<tr>
											<td valign="top" style="border-collapse: collapse; background-color: #FFFFFF;" bgcolor="#FFFFFF">
												<table border="0" cellpadding="20" cellspacing="0" width="100%">
													<tr>
														<td valign="top" style="border-collapse: collapse;">
															<?php echo $this->fetch('content');?>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<br />
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>