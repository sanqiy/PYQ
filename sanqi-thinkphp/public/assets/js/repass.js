/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
//找回密码发送邮件
function zhfsyzm(){
    var safzh=document.getElementById("saf-zh").value;
    var safem=document.getElementById("saf-email").value;
    if (safzh == "" || safem == "") {
        warnpop("账号或邮箱未输入");
        return;
    }

    loadpop("正在发送验证码...",'ok');
    Sanqi.apiPost('/api/repass', { useke: safzh, useem: safem, tig: 0 }, function(res){
        if (!res || !res.code) { errorpop("未获取到数据"); return; }
        if (res.code == '200') {
            successpop("验证码发送成功");
            var x = document.getElementById("sh-fsyk");
            if (x != null) x.remove();
        } else {
            warnpop(res.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}



//重置密码按钮事件
function czmman(){
    var safzh=document.getElementById("saf-zh").value;
    var safem=document.getElementById("saf-email").value;
    var safyzm=document.getElementById("saf-yzm").value;
    var safxmm=document.getElementById("saf-pass").value;
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
    Sanqi.apiPost('/api/repass', {
        useke: safzh, useem: safem, safyzm: safyzm, safxmm: safxmm, tig: 1
    }, function(res){
        if (!res || !res.code) { errorpop("未获取到数据"); return; }
        if (res.code == '200') {
            successpop("密码重置成功");
            window.location = '/';
        } else {
            warnpop(res.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}
