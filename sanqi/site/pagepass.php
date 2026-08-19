<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

//网站访问密码弹出层 网站设置了访问密码此文件才会被调用 请勿随意改动否则可能导致报错
echo '<div class="centent">
        <div class="sh-main" style="display: flex;justify-content: center;align-items: center;height: 100vh;">
           <div style="width: 100%;max-width: 80%;display: flex;flex-direction: column;padding: 20px 10px 15px 10px;background: var(--backbg);border-radius: 4px;background-image: repeating-linear-gradient(135deg, var(--bodys) 0, var(--bodys) 15px, transparent 0, transparent 30px);">
           <label style="font-size: 12px;color: var(--adgg);">输入密码进入网站↓</label>
              <form action="./api/pagepassver.php" method="post" onsubmit="return dosubmit()" id="myForm" autocomplete="off" style="display: flex;flex-direction: column;align-items: flex-end;">
                 <input type="password" name="pagepass" id="pagepass" style="width: 100%;height: 30px;border: none;outline: none;background: var(--dbztlysh);color: var(--thetitle);border-bottom: 2px solid var(--thetitle);" maxlength="100" autofocus required>
                 <input type="submit" style="font-size: 14px;border: none;outline: none;border-radius: 4px;color: var(--thetitle);background: var(--backbg);cursor: pointer;padding: 8px 15px;margin-top: 15px;" name="submit" value="确定">
              </form>
           </div>
        </div>
</div>';
echo '<script type="text/javascript" src="./assets/mesg/dist/js/sh-noytf.js"></script>
<script>
    function dosubmit(){
        event.preventDefault(); // 阻止表单的默认提交行为
// 获取表单元素
var form = document.getElementById(\'myForm\');
// 创建XMLHttpRequest对象
var xhr = new XMLHttpRequest();
// 设置请求处理程序
xhr.onreadystatechange = function() {
  if (xhr.readyState == 4 && xhr.status == 200) {
    // 如果请求成功并且状态为200，则处理服务器返回的数据
    var response = xhr.responseText;
    if(response == "密码正确"){
        successpop(response);
        location.href="./";
    }else{
        if (response == "网站未设置密码") {
           successpop(response);
           location.href="./";
        }else{
            warnpop(response);
        }

    }
  }
};
// 设置表单数据为请求主体
var formData = new FormData(form);
// 初始化请求
xhr.open("POST", form.action, true);
// 发送表单数据
xhr.send(formData);
}
</script>';
exit;
?>
