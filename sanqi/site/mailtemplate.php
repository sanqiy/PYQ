<!--
sanqi

@copyright Copyright (c) sanqi
@link      https://xaacn.com
-->
<div class="open_email" style="margin-left: 8px; margin-top: 8px; margin-bottom: 8px; margin-right: 8px;">
    <div>
        <span class="genEmailNicker"></span>
        <br>
        <span class="genEmailContent">
            <div id="cTMail-Wrap"
                style="word-break: break-all;box-sizing:border-box;text-align:center;min-width:320px; max-width:660px; border:1px solid #f6f6f6; background-color:#f7f8fa; margin:auto; padding:20px 0 30px; font-family:'helvetica neue',PingFangSC-Light,arial,'hiragino sans gb','microsoft yahei ui','microsoft yahei',simsun,sans-serif">
                <div class="main-content">
                    <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse">
                        <tbody>
                            <tr style="font-weight:300">
                                <td style="width:3%;max-width:30px;"></td>
                                <td style="max-width:600px;">
                                    <div id="cTMail-logo" style="width:92px; height:25px;">
                                        <a href="{{ url }}" rel="noopener" target="_blank">
                                            <img border="0" src="{{ url }}/assets/img/logo.png" alt="logo"
                                                style="width:92px; height:25px;display:block">
                                        </a>
                                    </div>
                                    <p style="height:2px;background-color: #00a4ff;border: 0;font-size:0;padding:0;width:100%;margin-top:20px;"></p>
                                    <div id="cTMail-inner"
                                        style="background-color:#fff; padding:23px 0 20px;box-shadow: 0px 1px 1px 0px rgba(122, 55, 55, 0.2);text-align:left;">
                                        <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse;text-align:left;">
                                            <tbody>
                                                <tr style="font-weight:300">
                                                    <td style="width:3.2%;max-width:30px;"></td>
                                                    <td style="max-width:480px;text-align:left;">
                                                        <h1 id="cTMail-title"
                                                            style="font-weight:bold;font-size:20px; line-height:36px; margin:0 0 16px;">
                                                            {{ wz_name }}</h1>
                                                        <p id="cTMail-userName" style="font-size:14px;color:#333; line-height:24px; margin:0;">
                                                            尊敬的用户，您好！</p>
                                                        <p class="cTMail-content"
                                                            style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;">
                                                            {{ title }}</p>
                                                        <p class="cTMail-content"
                                                            style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 6px 0px 0px; word-wrap: break-word; word-break: break-all;">
                                                            <span style="font-size: 16px; line-height: 45px; display: block; background-color: rgb(0, 164, 255); color: rgb(255, 255, 255); text-align: center; text-decoration: none; margin-top: 20px; border-radius: 3px;">{{ text }}</span>
                                                        </p>
                                                        <p style="border-top: 1px solid rgb(234, 237, 240); margin: 20px 0px;"></p>
                                                        <dl style="font-size: 14px; color: rgb(51, 51, 51); line-height: 18px;">
                                                            <dt style="margin: 0px 0px 8px; padding: 0px;">温馨提示：</dt>
                                                            <dd style="margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;">
                                                                1. 请确保不要将验证码告知他人，避免造成不必要的麻烦。</dd>
                                                            <dd style="margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;">
                                                                2. 如果您收到的验证码并非您本人请求或获取，请立即忽略此邮件。</dd>
                                                        </dl>
                                                        <dl style="font-size: 14px; color: rgb(51, 51, 51); line-height: 18px;">
                                                            <dd style="margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;">
                                                                <p id="cTMail-sender"
                                                                    style="font-size: 14px; line-height: 26px; word-wrap: break-word; word-break: break-all; margin-top: 32px;">
                                                                    此致<br>
                                                                    <a href="{{ url }}" rel="noopener" target="_blank"><strong>{{ wz_name }}</strong></a>
                                                                </p>
                                                            </dd>
                                                        </dl>
                                                    </td>
                                                    <td style="width:3.2%;max-width:30px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="cTMail-copy" style="text-align:center; font-size:12px; line-height:18px; color:#999">
                                        <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse">
                                            <tbody>
                                                <tr style="font-weight:300">
                                                    <td style="width:3.2%;max-width:30px;"></td>
                                                    <td style="max-width:540px;">
                                                        <p style="text-align:center; margin:20px auto 14px auto;font-size:12px;color:#999;">
                                                            此为系统邮件，请勿回复。</p>
                                                        <p style="text-align:center;margin:0 auto 4px;">
                                                            Copyright © {{ wz_name }} All rights reserved.
                                                        </p>
                                                    </td>
                                                    <td style="width:3.2%;max-width:30px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                                <td style="width:3%;max-width:30px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </span>
    </div>
</div>