<?php

use yii\helpers\Url;
?>
<html>
    <head>
        <style type="text/css" rel="stylesheet" media="all">

            .email-masthead_logo {
                max-width: 400px;
                border: 0;
            }

            /*Media Queries ------------------------------ */
            @media only screen and (max-width: 600px) {
                .email-body_inner,
                .email-footer {
                    width: 100% !important;
                }
            }
            @media only screen and (max-width: 500px) {
                .button {
                    width: 100% !important;
                }
            }
        </style>
    </head>
    <body style="width: 100% !important;height: 100%;margin: 0;line-height: 1.4;background-color: #F2F4F6;color: #74787E;-webkit-text-size-adjust: none;font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;-webkit-box-sizing: border-box;box-sizing: border-box;">
        <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" style="width: 100%;margin: 0;padding: 0;background-color: #F2F4F6;">
            <tbody><tr>
                    <td align="center" style="text-align: center;">
                        <table class="email-content" width="100%" cellpadding="0" cellspacing="0" style="width: 100%;margin: 0;padding: 0;">
                            <!-- Logo -->
                            <tbody><tr>
                                    <td class="email-masthead" style="padding:5px 0;text-align: center;display: flex">
                                        <div class="navbar nav_title" style="border: 0;overflow: hidden;text-align: center;margin: auto;">
                                            <div>
                                                <p class="flotte" style="float: left;margin-left: 10px;margin-top: 5px;">
                                                    <a href="<?= $baseUrl; ?>" class="site_title" style="width: 35px;height: 35px;padding: 2px;margin: 0;"><img style="width: 35px;" src="<?= $baseUrl; ?>/public/images/icons/ams-icon.png" id="app-icon" class="profile_img"></a>
                                                </p>
                                                <div style="float: left; color: gray">
                                                    <div class="app_name" style="margin-top: 10px;font-size: 22px;text-align: center;color: gray;"><b>A&nbsp;&nbsp;S&nbsp;&nbsp;S&nbsp;&nbsp;E&nbsp;&nbsp;T</b></div>
                                                    <div class="app_name" style="font-size:11px;float: left;margin-left: 10px;font-size: 9px;position: relative;bottom: 5px;">M A N A G E M E N T &nbsp;&nbsp;S Y S T E M</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Email Body -->
                                <tr>
                                    <td class="email-body" width="100%" style="width: 100%;margin: 0;padding: 0;border-top: 1px solid #EDEFF2;border-bottom: 1px solid #EDEFF2;background-color: #FFF;">
                                        <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" style="width: 570px;margin: 0 auto;padding: 0;">
                                            <!-- Body content -->
                                            <tbody>
                                                <tr>
                                                    <td class="content-cell" style="padding: 35px;padding-top: 10px;">
                                                        <h1 style="margin-top: 0;color: #2F3133;font-size: 19px;font-weight: bold;text-align: left;">Hello <?= $model->firstname; ?>,</h1>
                                                        <p style=" margin-top: 0;color: #74787E;font-size: 16px;line-height: 1.5em;text-align: left;">You have subscribed to <?= strtolower($reportOption); ?> emails for "<?= $fileName; ?>" in your <?= Yii::$app->name; ?> account. Download your report by clicking on the button below.</p>
                                                        <!-- Action -->
                                                        <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" style="width: 100%;margin: 30px auto;padding: 0;text-align: center;">
                                                            <tbody><tr>
                                                                    <td align="center">
                                                                        <div>
                                                                            <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{action_url}}" style="height:45px;v-text-anchor:middle;width:60%;" arcsize="7%" stroke="f" fill="t">
                                                                              <v:fill type="tile" color="#dc4d2f" />
                                                                              <w:anchorlock/>
                                                                              <center style="color:#ffffff;font-family:sans-serif;font-size:15px;">Download</center>
                                                                            </v:roundrect><![endif]-->
                                                                            <a href="<?= $downloadFileLink; ?>" class="button button--red" style="background-color: #4cae4c;display: inline-block;width: 60%;border-radius: 3px;color: #ffffff;font-size: 15px;line-height: 45px;text-align: center;text-decoration: none;-webkit-text-size-adjust: none;mso-hide: all;">Download Report</a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody></table>
<!--                                                        <p style=" margin-top: 0;color: #74787E;font-size: 16px;line-height: 1.5em;text-align: left;">If you didn't want to receive report Daily, you can change your preference.</p>-->
                                                        <p style=" margin-top: 0;color: #74787E;font-size: 16px;line-height: 1.5em;text-align: left;">Thanks,<br><?= Yii::$app->name; ?> Team</p>
                                                        <p style=" margin-top: 0;color: #74787E;font-size: 16px;line-height: 1.5em;text-align: left;"><strong>P.S.</strong>If you do not wish to receive this report <?= strtolower($reportOption); ?> you can change your preference anytime by logging into your <?= Yii::$app->name; ?> account. If you wish to change your preference now, <a href='<?= $baseUrl. 'analytics/reportsettings'; ?>'>click here</a>.
                                                        <!-- Sub copy -->
                                                        <table class="body-sub" style="margin-top: 25px;padding-top: 25px;border-top: 1px solid #EDEFF2;">
                                                            <tbody><tr>
                                                                    <td>
                                                                        <p class="sub" style="font-size: 12px;margin-top: 0;color: #74787E;line-height: 1.5em;text-align: left;">If you're having trouble clicking the download button, copy and paste the URL below into your web browser.</p>
                                                                        <p class="sub" style="font-size: 12px;margin-top: 0;color: #74787E;line-height: 1.5em;text-align: left;"><a style="color: #3869D4;" href="<?= $downloadFileLink; ?>"><?= $downloadFileLink; ?></a></p>
                                                                    </td>
                                                                </tr>
                                                            </tbody></table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>