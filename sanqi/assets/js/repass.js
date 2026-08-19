/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
//找回密码发送邮件
function zhfsyzm(){
    var safzh=document.getElementById("saf-zh").value;//取账号
    var safem=document.getElementById("saf-email").value;//取邮箱
    if (safzh == "" || safem == "") {
        warnpop("账号或邮箱未输入");
        return;
    }

    loadpop("正在发送验证码...",'ok');
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/repass.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('useke='+safzh+"&useem="+safem+"&tig=0");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }

            if (xhr.responseText == "发送成功") {
                successpop("验证码发送成功");
                var x = document.getElementById("sh-fsyk");
                if (x != null){
                    x.remove();
                }
            }else{
                warnpop(xhr.responseText);
            }
        }
    };
}



//重置密码按钮事件
function czmman(){
    var safzh=document.getElementById("saf-zh").value;//取账号
    var safem=document.getElementById("saf-email").value;//取邮箱
    var safyzm=document.getElementById("saf-yzm").value;//取验证码
    var safxmm=document.getElementById("saf-pass").value;//取新密码
    if (safzh == "" || safem == "") {
        warnpop("账号或邮箱未输入");
        return;
    }

    if (safyzm == "") {
        warnpop("请输入邮箱验证码");
        return;
    }
    if (safxmm == "") {
        warnpop("请输入新密码");
        return;
    }

    loadpop("正在重置密码，请稍后...",'ok');
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/repass.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('useke='+safzh+'&useem='+safem+'&safyzm='+safyzm+'&safxmm='+safxmm+'&tig=1');
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }

            if (xhr.responseText == "密码已重置!") {
                successpop("密码重置成功");
                window.location = './index.php';
            }else{
                warnpop(xhr.responseText);
            }
        }
    };
}

document.oncontextmenu=new Function("event.returnValue=false"); //禁止右键
