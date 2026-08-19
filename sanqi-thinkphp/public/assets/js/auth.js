/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
// Authentication and account flows extracted from index.js.

function getCookie(cookieName) {
    var strCookie = document.cookie;
    var arrCookie = strCookie.split("; ");
    for(var i = 0; i < arrCookie.length; i++){
        var arr = arrCookie[i].split("=");
        if(cookieName == arr[0]){
            return arr[1];
        }
    }
    if (window.__SANQI_LOGIN__ && window.__SANQI_LOGIN__.isLogin) {
        if (cookieName == "username") {
            return window.__SANQI_LOGIN__.username || "login";
        }
        if (cookieName == "passid") {
            return "session";
        }
    }
    return "";
}

function regzc(){
    var zh = Sanqi.id("login-zh").value;
    var em = Sanqi.id("login-email").value;
    var mm = Sanqi.id("login-pass").value;
    var yzm = Sanqi.id("login-yzm") ? Sanqi.id("login-yzm").value : "";

    if (zh == "") {
        warnpop("\u8d26\u53f7\u672a\u8f93\u5165");
        return;
    } else if (em == "") {
        warnpop("\u90ae\u7bb1\u672a\u8f93\u5165");
        return;
    } else if (mm == "") {
        warnpop("\u5bc6\u7801\u672a\u8f93\u5165");
        return;
    }

    var regx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    if (regx.test(em) != true) {
        warnpop("\u90ae\u7bb1\u683c\u5f0f\u9519\u8bef");
        return;
    }
    if (Sanqi.id("login-yzm") && yzm == "") {
        warnpop("\u9a8c\u8bc1\u7801\u672a\u8f93\u5165");
        return;
    }
    if (zh.length < 3) {
        warnpop("\u8d26\u53f7\u4e0d\u53ef\u4f4e\u4e8e3\u4f4d\u6570");
        return;
    } else if (zh.length > 32) {
        warnpop("\u8d26\u53f7\u4e0d\u53ef\u5927\u4e8e32\u4f4d\u6570");
        return;
    } else if (mm.length < 3) {
        warnpop("\u5bc6\u7801\u4e0d\u53ef\u4f4e\u4e8e3\u4f4d\u6570");
        return;
    } else if (mm.length > 16) {
        warnpop("\u5bc6\u7801\u4e0d\u53ef\u5927\u4e8e16\u4f4d\u6570");
        return;
    }

    loadpop("\u6b63\u5728\u6ce8\u518c\u8d26\u53f7,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/register', { zh: zh, em: em, mm: mm, yzm: yzm }, function(res){
        if (!res || !res.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        if (res.code == '200') {
            successpop(res.msg);
            Sanqi.id("login-email").value = "";
            zcanxy();
        } else {
            errorpop(res.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "\u6ce8\u518c\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}

//发送注册验证码
if (document.getElementById('yzm')) {
  document.getElementById('yzm').addEventListener('click', function() {
      var zh = Sanqi.id("login-zh").value;
      var em = Sanqi.id("login-email").value;
      var mm = Sanqi.id("login-pass").value;
      var yzmButton = Sanqi.id("yzm");
      if (yzmButton.innerText != "\u53d1\u9001") {
          return;
      }
      if (em == "") {
          warnpop("\u90ae\u7bb1\u672a\u8f93\u5165");
          return;
      }
      var regx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
      if (regx.test(em) != true) {
          warnpop("\u90ae\u7bb1\u683c\u5f0f\u9519\u8bef");
          return;
      }

      loadpop("\u6b63\u5728\u53d1\u9001\u9a8c\u8bc1\u7801,\u8bf7\u7a0d\u540e...", "ok");
      Sanqi.apiPost('/api/register', { zh: zh, em: em, mm: mm, fsyzm: 1 }, function(res){
          if (!res || !res.code) {
              errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
              return;
          }
          if (res.code == '200') {
              successpop(res.msg);
              var countDown = 60;
              var countDownFunction = setInterval(function() {
                  yzmButton.textContent = '\u53d1\u9001(' + countDown + '\u79d2)';
                  countDown--;
                  if (countDown <= 0) {
                      yzmButton.textContent = '\u53d1\u9001';
                      clearInterval(countDownFunction);
                  }
              }, 1000);
          } else {
              errorpop(res.msg);
          }
      }, function(res){
          errorpop((res && res.msg) || "\u9a8c\u8bc1\u7801\u53d1\u9001\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
      });
  });
}

//禁止注册账号和找回密码按钮响应回车事件
function checkKeyDown(event) {
  if (event.keyCode === 13) {
    event.preventDefault();
  }
}

//注册按钮显示与隐藏

//找回密码事件
function zhmm(){
    var userzhzh=document.getElementById("login-zh").value;//获取需要找回的账号
    if (userzhzh == "") {
        warnpop("请先输入您的账号");
    }else{
        loadpop("正在验证信息，请稍后","ok");
        window.location.href='/repass?useke='+userzhzh;
    }
}





//登录按钮事件
function logy(){
    if (_isAdminTwoFactorStep()) {
        var twoFactorToken = Sanqi.id("login-2fa-token").value;
        var twoFactorCode = Sanqi.id("login-2fa-code").value;
        if (twoFactorCode == "") {
            warnpop("\u8bf7\u8f93\u5165\u4e8c\u6b65\u9a8c\u8bc1\u7801");
            return;
        }
        if (!/^\d{6}$/.test(twoFactorCode)) {
            warnpop("\u4e8c\u6b65\u9a8c\u8bc1\u7801\u4e3a6\u4f4d\u6570\u5b57");
            return;
        }

        loadpop("\u6b63\u5728\u9a8c\u8bc1,\u8bf7\u7a0d\u540e...", "ok");
        Sanqi.apiPost('/api/login/verify-2fa', { token: twoFactorToken, code: twoFactorCode }, function(res){
            if (!res || !res.code) {
                errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
                return;
            }
            if (res.code == '200') {
                _resetAdminTwoFactorLogin();
                successpop("\u767b\u5f55\u6210\u529f,\u5373\u5c06\u8df3\u8f6c", "ok");
                window.location.href = _getLoginRedirect();
            } else {
                errorpop(res.msg);
            }
        }, function(res){
            errorpop((res && res.msg) || "\u9a8c\u8bc1\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
        });
        return;
    }

    var zh = Sanqi.id("login-zh").value;
    var mm = Sanqi.id("login-pass").value;
    if (zh == "") {
        warnpop("\u8bf7\u8f93\u5165\u8d26\u53f7");
        return;
    } else if (mm == "") {
        warnpop("\u8bf7\u8f93\u5165\u5bc6\u7801");
        return;
    }

    if (zh.length < 3) {
        warnpop("\u8d26\u53f7\u4e0d\u53ef\u4f4e\u4e8e3\u4f4d\u6570");
        return;
    } else if (zh.length > 32) {
        warnpop("\u8d26\u53f7\u4e0d\u53ef\u5927\u4e8e32\u4f4d\u6570");
        return;
    } else if (mm.length < 3) {
        warnpop("\u5bc6\u7801\u4e0d\u53ef\u4f4e\u4e8e3\u4f4d\u6570");
        return;
    } else if (mm.length > 16) {
        warnpop("\u5bc6\u7801\u4e0d\u53ef\u5927\u4e8e16\u4f4d\u6570");
        return;
    }

    loadpop("\u767b\u5f55\u4e2d,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/login', { zh: zh, mm: mm, redirect: _getLoginRedirect() }, function(res){
        if (!res || !res.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        if (res.code == '200') {
            var data = res.data || {};
            if (data.requires_2fa) {
                _saveLoginAccount(zh);
                _showAdminTwoFactorLogin(data);
                if (data.method === "totp") {
                    successpop("\u8bf7\u8f93\u5165 TOTP \u52a8\u6001\u9a8c\u8bc1\u7801", "ok");
                } else {
                    successpop("\u9a8c\u8bc1\u7801\u5df2\u53d1\u9001\u5230\u7ba1\u7406\u5458\u90ae\u7bb1", "ok");
                }
                return;
            }
            _saveLoginAccount(zh);
            successpop("\u767b\u5f55\u6210\u529f,\u5373\u5c06\u8df3\u8f6c", "ok");
            window.location.href = _getLoginRedirect();
        } else {
            errorpop(res.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "\u767b\u5f55\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}

//消息操作菜单开关
