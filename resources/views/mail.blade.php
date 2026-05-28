<!doctype html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Mail</title>
    <style media="all" type="text/css">
@media all {
  .btn-primary table td:hover {
    background-color: #ec0867 !important;
  }

  .btn-primary a:hover {
    background-color: #ec0867 !important;
    border-color: #ec0867 !important;
  }
}
@media only screen and (max-width: 640px) {
  .main p,
.main td,
.main span {
    font-size: 16px !important;
  }

  .wrapper {
    padding: 8px !important;
  }

  .content {
    padding: 0 !important;
  }

  .container {
    padding: 0 !important;
    padding-top: 8px !important;
    width: 100% !important;
  }

  .main {
    border-left-width: 0 !important;
    border-radius: 0 !important;
    border-right-width: 0 !important;
  }

  .btn table {
    max-width: 100% !important;
    width: 100% !important;
  }

  .btn a {
    font-size: 16px !important;
    max-width: 100% !important;
    width: 100% !important;
  }
}
@media all {
  .ExternalClass {
    width: 100%;
  }

  .ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
    line-height: 100%;
  }

  .apple-link a {
    color: inherit !important;
    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    text-decoration: none !important;
  }

  #MessageViewBody a {
    color: inherit;
    text-decoration: none;
    font-size: inherit;
    font-family: inherit;
    font-weight: inherit;
    line-height: inherit;
  }
}
</style>
  </head>
  <body style="font-family: Helvetica, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.3; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f4f5f6; margin: 0; padding: 0;">
	<!-- Preheader Text -->
    <span class="preheader" style="display: none; font-size: 1px; color: #fff; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        <?=strip_tags($mail_text);?>
    </span>
	  
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f4f5f6; width: 100%;" width="100%" bgcolor="#f4f5f6">
      <tr>
        <td style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top;" valign="top">&nbsp;</td>
        <td class="container" style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top; max-width: 600px; padding: 0; padding-top: 24px; width: 600px; margin: 0 auto;" width="600" valign="top">
          
          <?php if(isset($customer_settings) && $customer_settings->logo): ?>
          	<center><img src="<?=$customer_settings->logo;?>" width="200" style="width: 200px; max-width: 200px; height: auto; margin-bottom: 20px; margin-top: 20px;"></center>
          <?php endif; ?>
          
          <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 600px; padding: 0;">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"></span>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border: 1px solid #eaebed; border-radius: 16px; width: 100%;" width="100%">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top; box-sizing: border-box; padding: 24px;" valign="top">
                  <div style="font-family: Helvetica, sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 16px;">
	                  <p style="text-align: center;">
		                  <?=$mail_text;?>
		                  
		                  <?php if(isset($button_text) && isset($button_link)): ?>
						  	<a href="<?=$button_link;?>" style="color: #fff; background: #11069f; padding: 10px 20px; border-radius: 10px; text-decoration: none; margin: 10px; display: inline-block;"><?=$button_text;?></a>
						  <?php endif; ?>
	                  </p>
                  </div>
                  
                  <?php if(isset($user)): ?>
	                  <table cellpadding="0" cellspacing="0" border="0" globalstyles="[object Object]" class="table__StyledTable-sc-1avdl6r-0 gZiJTA" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: Verdana;">
						   <tbody>
						      <tr>
						         <td>
						            <table cellpadding="0" cellspacing="0" border="0" globalstyles="[object Object]" class="table__StyledTable-sc-1avdl6r-0 gZiJTA" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: Verdana;">
						               <tbody>
						                  <tr>
						                     <td width="150" style="vertical-align: middle;"><span class="template3__ImageContainer-sc-vj949k-0 jpHmCN" style="margin-right: 20px; display: block;"><img src="https://zigzaghr.be/wp-content/uploads/2021/05/xx_foto_71_expert_re_YnWXz.jpg" role="presentation" width="130" class="image__StyledImage-sc-hupvqm-0 kYkhsl" style="max-width: 130px;"></span></td>
						                     <td style="vertical-align: middle;">
						                        <h2 color="#10069f" class="name__NameContainer-sc-1m457h3-0 gsCpOr" style="margin: 0px; font-size: 18px; color: rgb(16, 6, 159); font-weight: 600;"><span><?=$user->name;?></span><span>&nbsp;</span><span></span></h2>
						                        <p color="#10069f" font-size="medium" class="job-title__Container-sc-1hmtp73-0 hWtcow" style="margin: 0px; color: rgb(16, 6, 159); font-size: 14px; line-height: 22px;"><span>Accountmanager</span></p>
						                     </td>
						                     <td width="30">
						                        <div style="width: 30px;"></div>
						                     </td>
						                     <td color="#10069f" direction="vertical" width="1" height="auto" class="color-divider__Divider-sc-1h38qjv-0 icFEOy" style="width: 1px; border-bottom: none; border-left: 1px solid rgb(16, 6, 159);"></td>
						                     <td width="30">
						                        <div style="width: 30px;"></div>
						                     </td>
						                     <td style="vertical-align: middle;">
						                        <table cellpadding="0" cellspacing="0" border="0" globalstyles="[object Object]" class="table__StyledTable-sc-1avdl6r-0 gZiJTA" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: Verdana;">
						                           <tbody>
						                              <tr height="25" style="vertical-align: middle;">
						                                 <td width="30" style="vertical-align: middle;">
						                                    <table cellpadding="0" cellspacing="0" border="0" globalstyles="[object Object]" class="table__StyledTable-sc-1avdl6r-0 gZiJTA" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: Verdana;">
						                                       <tbody>
						                                          <tr>
						                                             <td style="vertical-align: bottom;"><span color="#10069f" width="11" class="contact-info__IconWrapper-sc-mmkjr6-1 brbfIW" style="display: inline-block; background-color: rgb(16, 6, 159);"><img src="https://cdn2.hubspot.net/hubfs/53/tools/email-signature-generator/icons/link-icon-2x.png" color="#10069f" alt="website" width="13" class="contact-info__ContactLabelIcon-sc-mmkjr6-0 kInyhW" style="display: block; background-color: rgb(16, 6, 159);"></span></td>
						                                          </tr>
						                                       </tbody>
						                                    </table>
						                                 </td>
						                                 <td style="padding: 0px;"><a href="https://readmylips.be" color="#10069f" class="contact-info__ExternalLink-sc-mmkjr6-2 dExxuU" style="text-decoration: none; color: rgb(16, 6, 159); font-size: 12px;"><span>https://readmylips.be</span></a></td>
						                              </tr>
						                              <tr height="25" style="vertical-align: middle;">
						                                 <td width="30" style="vertical-align: middle;">
						                                    <table cellpadding="0" cellspacing="0" border="0" globalstyles="[object Object]" class="table__StyledTable-sc-1avdl6r-0 gZiJTA" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: Verdana;">
						                                       <tbody>
						                                          <tr>
						                                             <td style="vertical-align: bottom;"><span color="#10069f" width="11" class="contact-info__IconWrapper-sc-mmkjr6-1 brbfIW" style="display: inline-block; background-color: rgb(16, 6, 159);"><img src="https://cdn2.hubspot.net/hubfs/53/tools/email-signature-generator/icons/address-icon-2x.png" color="#10069f" alt="address" width="13" class="contact-info__ContactLabelIcon-sc-mmkjr6-0 kInyhW" style="display: block; background-color: rgb(16, 6, 159);"></span></td>
						                                          </tr>
						                                       </tbody>
						                                    </table>
						                                 </td>
						                                 <td style="padding: 0px;"><span color="#10069f" class="contact-info__Address-sc-mmkjr6-3 jomBme" style="font-size: 12px; color: rgb(16, 6, 159);"><span>Mechelsesteenweg&nbsp;271‑11, 2018&nbsp;Antwerpen</span></span></td>
						                              </tr>
						                           </tbody>
						                        </table>
						                     </td>
						                  </tr>
						               </tbody>
						            </table>
						         </td>
						      </tr>
						      <tr>
						         <td></td>
						      </tr>
						      <tr>
						         <td></td>
						      </tr>
						   </tbody>
						</table>
						<?php endif; ?>
	                </td>
	              </tr>
	
	              <!-- END MAIN CONTENT AREA -->
	              </table>

            <!-- START FOOTER -->
            <div class="footer" style="clear: both; padding-top: 24px; text-align: center; width: 100%;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                <tr>
                  <td class="content-block" style="font-family: Helvetica, sans-serif; vertical-align: top; color: #9a9ea6; font-size: 16px; text-align: center;" valign="top" align="center">
                  <span class="apple-link" style="color: #9a9ea6; font-size: 16px; text-align: center;"></span>
                  </td>
                </tr>
              </table>
            </div>

            <!-- END FOOTER -->
            
<!-- END CENTERED WHITE CONTAINER --></div>
        </td>
        <td style="font-family: Helvetica, sans-serif; font-size: 16px; vertical-align: top;" valign="top">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>