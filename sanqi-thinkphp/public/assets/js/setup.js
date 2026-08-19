/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
//设置界面的所有js事件

//安全中心 列表开启与关闭事件
function anquan(){
    //取高度
    var hei=document.getElementById('setup-main-lieb-aq').lang;
    if (hei == "45") {
        //展开列表
        document.getElementById('setup-main-lieb-aq').lang="auto";//修改高度参数
        document.getElementById('setup-main-lieb-aq').style.height="auto";//修改div高度
        document.getElementById('setup-main-lieb-aq-img').className="iconfont icon-weibiaoti-x al-sxbh-setup";//修改标识图片
    }else{
        //关闭列表
        document.getElementById('setup-main-lieb-aq').lang="45";//修改高度参数
        document.getElementById('setup-main-lieb-aq').style.height="45px";//修改div高度
        document.getElementById('setup-main-lieb-aq-img').className="iconfont icon-weibiaotiy al-sxbh-setup";//修改标识图片
    }
}




//权限管理 列表开启与关闭事件
function quanxian(){
    //取高度
    var hei=document.getElementById('setup-main-lieb-qx').lang;
    if (hei == "45") {
        //展开列表
        document.getElementById('setup-main-lieb-qx').lang="auto";//修改高度参数
        document.getElementById('setup-main-lieb-qx').style.height="auto";//修改div高度
        document.getElementById('setup-main-lieb-qx-img').className="iconfont icon-weibiaoti-x al-sxbh-setup";//修改标识图片
    }else{
        //关闭列表
        document.getElementById('setup-main-lieb-qx').lang="45";//修改高度参数
        document.getElementById('setup-main-lieb-qx').style.height="45px";//修改div高度
        document.getElementById('setup-main-lieb-qx-img').className="iconfont icon-weibiaotiy al-sxbh-setup";//修改标识图片
    }
}









//头像修改 开启与关闭
function setuptx(){
    //取高度
    var hei=document.getElementById('setuptx').lang;
    if (hei == "60") {
        //展开列表
        document.getElementById('setuptx').lang="auto";//修改高度参数
        document.getElementById('setuptx').style.height="auto";//修改div高度
        document.getElementById('setup-main-lieb-qtx-img').className="iconfont icon-weibiaoti-x al-sxbh-setup1";//修改标识图片
    }else{
        //关闭列表
        document.getElementById('setuptx').lang="60";//修改高度参数
        document.getElementById('setuptx').style.height="60px";//修改div高度
        document.getElementById('setup-main-lieb-qtx-img').className="iconfont icon-weibiaotiy al-sxbh-setup1";//修改标识图片
    }
}
//昵称修改 开启与关闭
function setupnc(){
    //取高度
    var hei=document.getElementById('setupnc').lang;
    if (hei == "45") {
        //展开列表
        document.getElementById('setupnc').lang="auto";//修改高度参数
        document.getElementById('setupnc').style.height="auto";//修改div高度
        document.getElementById('setup-main-lieb-qnc-img').className="iconfont icon-weibiaoti-x al-sxbh-setup1";//修改标识图片
    }else{
        //关闭列表
        document.getElementById('setupnc').lang="45";//修改高度参数
        document.getElementById('setupnc').style.height="45px";//修改div高度
        document.getElementById('setup-main-lieb-qnc-img').className="iconfont icon-weibiaotiy al-sxbh-setup1";//修改标识图片
    }
}
//签名修改 开启与关闭
function setupqm(){
    //取高度
    var hei=document.getElementById('setupqm').lang;
    if (hei == "45") {
        //展开列表
        document.getElementById('setupqm').lang="auto";//修改高度参数
        document.getElementById('setupqm').style.height="auto";//修改div高度
        document.getElementById('setup-main-lieb-qqm-img').className="iconfont icon-weibiaoti-x al-sxbh-setup1";//修改标识图片
    }else{
        //关闭列表
        document.getElementById('setupqm').lang="45";//修改高度参数
        document.getElementById('setupqm').style.height="45px";//修改div高度
        document.getElementById('setup-main-lieb-qqm-img').className="iconfont icon-weibiaotiy al-sxbh-setup1";//修改标识图片
    }
}
//网址修改 开启与关闭
function setupwz(){
    //取高度
    var hei=document.getElementById('setupwz').lang;
    if (hei == "45") {
        //展开列表
        document.getElementById('setupwz').lang="auto";//修改高度参数
        document.getElementById('setupwz').style.height="auto";//修改div高度
        document.getElementById('setup-main-lieb-qwz-img').className="iconfont icon-weibiaoti-x al-sxbh-setup1";//修改标识图片
    }else{
        //关闭列表
        document.getElementById('setupwz').lang="45";//修改高度参数
        document.getElementById('setupwz').style.height="45px";//修改div高度
        document.getElementById('setup-main-lieb-qwz-img').className="iconfont icon-weibiaotiy al-sxbh-setup1";//修改标识图片
    }
}




//邮件通知开启和关闭
function emailkq(){
    //取状态
    var zt=document.getElementById('emaitz').lang;//0为关闭 1为开启
    if (zt == 0 ) {
        var ztm=0;
    }else if(zt == 1){
        var ztm=1;
    }else{
        errorpop("数据类型错误");
        return;
    }

    loadpop("正在更新邮件通知，请稍后...",'ok');
    Sanqi.apiPost('/api/user/email-notify', { ztm: ztm }, function(res){
        if (!res || !res.code) { errorpop("未获取到数据"); return; }
        if (res.code == '200') {
            if (res.msg.indexOf('开启') > -1) {
                document.getElementById("emaitz").style="background: rgb(7, 193, 96);justify-content: flex-end;";
                document.getElementById("emaitz").lang="1";
            } else {
                document.getElementById("emaitz").style="background: rgb(238 238 238);justify-content: flex-start;";
                document.getElementById("emaitz").lang="0";
            }
            successpop(res.msg);
        } else {
            errorpop(res.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}



//获取cookie函数
function getCookie(cookieName) {
    var strCookie = document.cookie;
    var arrCookie = strCookie.split("; ");
    for(var i = 0; i < arrCookie.length; i++){
        var arr = arrCookie[i].split("=");
        if(cookieName == arr[0]){
            return arr[1];
        }
    }
    return "";
}
//退出登录
function logut(){
    loadpop("正在退出登录...",'ok');
    window.location.href="/logout";
}






//账号与安全修改
function logoutAllDevices(){
    if (!confirm("确定要退出所有设备吗？当前设备会保留登录状态。")) {
        return;
    }
    loadpop("正在退出其他设备，请稍后...", "ok");
    Sanqi.apiPost('/api/user/logout-all', { q: 1 }, function(obj){
        if (!obj || !obj.code) { errorpop("未获取到数据"); return; }
        if (obj.code == 200) {
            successpop(obj.msg);
        } else {
            warnpop(obj.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}

function zhyanxg(){
    var userzh=document.getElementById("userzh").value;//获取账号
    var usermm=document.getElementById("usermm").value;//获取旧密码
    var userxmm=document.getElementById("userxmm").value;//获取新密码
    var userem=document.getElementById("userem").value;//获取邮箱

    if (usermm != "" && userxmm != "") {
        if (usermm == userxmm) {
            warnpop("新密码不能与旧密码相同");
            return;
        }
        if(userxmm.length < 3){
            warnpop("密码不可低于3位数");
            return;
        }else if(userxmm.length > 16){
            warnpop("密码不可大于16位数");
            return;
        }

    }

    if (userem != "") {
        var regx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
            if (regx.test(userem) != true) {
                warnpop("邮箱格式错误");
                return;
            }
    }

    if (usermm == "" && userxmm =="" & userem== "") {
        return;
    }



    loadpop("正在更新，请稍后...",'ok');
    Sanqi.apiPost('/api/user/update', {
        userzh: userzh, usermm: usermm, userxmm: userxmm, userem: userem, lx: 'aq'
    }, function(obj){
        if (!obj || !obj.code) { errorpop("未获取到数据"); return; }
        if (obj.code == 200) {
            document.getElementById("usermm").value="";
            document.getElementById("userxmm").value="";
            document.getElementById("setup-main-lieb-aq").lang="45";
            document.getElementById("setup-main-lieb-aq").style="height:45px";
            document.getElementById("setup-main-lieb-aq-img").className="iconfont icon-weibiaotiy al-sxbh-setup";
            successpop(obj.msg);
        } else {
            warnpop(obj.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}









//头像修改 预览选中的头像
document.querySelector('#files').onchange = function (){
      if(this.files.length){
        let file = this.files[0];
        let reader = new FileReader();
        //新建 FileReader 对象
        reader.onload = function(){
          // 当 FileReader 读取文件时候，读取的结果会放在 FileReader.result 属性中
          document.querySelector('#setup-formup-img').src = this.result;
          var userbtu = document.querySelector("#setup-formup-img");
          userbtu.setAttribute("data-src", this.result);
          document.getElementById("setup-formup-img").style.display="flex";
          document.getElementById("files").style.display="none";
          document.getElementById("cupload-upload-btn").style.display="none";
        };
        // 设置以什么方式读取文件，这里以base64方式
        reader.readAsDataURL(file);
       }
    }

function sctxs(){
    var fiewj=document.getElementById("files").value;
    if (fiewj == "" || fiewj == null) {
        warnpop("请选择文件");
        return;
    }
    loadpop("正在上传头像，请稍后...","ok");
}







//昵称修改
function zhnc(){
    var usernc=document.getElementById("usernc").value;//获取昵称
    if (usernc =="") {
        warnpop("名字不可为空");
        return;
    }
    if (usernc.length > 10) {
        warnpop("名字不可超过10个字符");
        return;
    }
    loadpop("正在修改名字，请稍后...",'ok');
    Sanqi.apiPost('/api/user/update', { usernc: usernc, lx: 'zlnc' }, function(obj){
        if (!obj || !obj.code) { errorpop("未获取到数据"); return; }
        if (obj.code == 200) {
            var srkxm=document.getElementById("usernc").value;
            document.getElementById("setup-main-lieb-title-y-usernc").innerText=srkxm;
            document.getElementById("setupnc").lang="45";
            document.getElementById("setupnc").style="height:45px";
            document.getElementById("setup-main-lieb-qnc-img").className="iconfont icon-weibiaotiy al-sxbh-setup1";
            successpop(obj.msg);
        } else {
            warnpop(obj.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}
//签名修改
function zhqm(){
    var userqm=document.getElementById("userqm").value;//获取昵称

    loadpop("正在修改签名，请稍后...",'ok');
    Sanqi.apiPost('/api/user/update', { userqm: userqm, lx: 'zlqm' }, function(obj){
        if (!obj || !obj.code) { errorpop("未获取到数据"); return; }
        if (obj.code == 200) {
            var srkxqm=document.getElementById("userqm").value;
            document.getElementById("setup-main-lieb-title-y-userqm").innerText=srkxqm;
            document.getElementById("setupqm").lang="45";
            document.getElementById("setupqm").style="height:45px";
            document.getElementById("setup-main-lieb-qqm-img").className="iconfont icon-weibiaotiy al-sxbh-setup1";
            successpop(obj.msg);
        } else {
            warnpop(obj.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}
//网址修改
function zhwz(){
    var userurl=document.getElementById("userurl").value;//获取昵称

    loadpop("正在修改网址，请稍后...",'ok');
    re = new RegExp("&","g");
    var userurl=userurl.replace(re, "#$#");
    Sanqi.apiPost('/api/user/update', { userurl: userurl, lx: 'zlwz' }, function(obj){
        if (!obj || !obj.code) { errorpop("未获取到数据"); return; }
        if (obj.code == 200) {
            var srkxqm=document.getElementById("userurl").value;
            document.getElementById("setup-main-lieb-title-y-userurl").innerText=srkxqm;
            document.getElementById("setupwz").lang="45";
            document.getElementById("setupwz").style="height:45px";
            document.getElementById("setup-main-lieb-qwz-img").className="iconfont icon-weibiaotiy al-sxbh-setup1";
            successpop(obj.msg);
        } else {
            warnpop(obj.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "请求失败");
    });
}







function loaddemand(){
    Sanqi.lazyLoad();
}

loaddemand();//调用一次懒加载

// 收款码上传
function uploadQr(type, input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
        warnpop('图片不能超过5MB');
        return;
    }
    var formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);
    loadpop('正在上传...', 'ok');
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/api/user/qr');
    xhr.onload = function() {
        try {
            var res = JSON.parse(xhr.responseText);
            if (res.code == 200) {
                successpop('上传成功');
                var preview = document.getElementById(type + 'QrPreview');
                if (preview) {
                    preview.innerHTML = '<img src="' + res.data.url + '" alt="收款码">';
                }
                // 更新按钮文字
                var btn = preview ? preview.parentElement.querySelector('.sh-setup-qr-btn span') : null;
                if (btn) btn.textContent = '更换';
            } else {
                warnpop(res.msg || '上传失败');
            }
        } catch(e) {
            warnpop('上传失败');
        }
    };
    xhr.onerror = function() { warnpop('网络错误'); };
    xhr.send(formData);
}

function toggleSection(id) {
    var el = document.getElementById('setup-main-lieb-' + id);
    if (!el) return;
    var img = document.getElementById('setup-main-lieb-' + id + '-img');
    if (el.lang === '45') {
        el.lang = '999';
        el.style.height = 'auto';
        if (img) img.className = 'iconfont icon-weibiaotiy al-sxbh-setup icon-rotate';
    } else {
        el.lang = '45';
        el.style.height = '45px';
        if (img) img.className = 'iconfont icon-weibiaotiy al-sxbh-setup';
    }
}
