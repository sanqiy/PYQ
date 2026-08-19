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
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/emaitzzt.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('ztm='+ztm);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "邮件通知已开启!") {
                //开启成功
                document.getElementById("emaitz").style="background: rgb(7, 193, 96);justify-content: flex-end;";
                document.getElementById("emaitz").lang="1";
                successpop("邮件通知已开启");
                //开启成功
            }else if (xhr.responseText == "邮件通知已关闭!") {
                //关闭成功
                document.getElementById("emaitz").style="background: rgb(238 238 238);justify-content: flex-start;";
                document.getElementById("emaitz").lang="0";
                successpop("邮件通知已关闭");
                //关闭成功
            }
        }
    };
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
    var user_id = getCookie("username");//取登录的账号
    var user_passid = getCookie("passid");//取登录的passid唯一id
    if (user_id == "" || user_passid == "") {
        loadpop("当前没有登录");
        return;
    }
    document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "passid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    successpop("您已退出登录","ok");
    window.location.href="./index.php";
}






//账号与安全修改
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
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/updata.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('userzh='+userzh+"&usermm="+usermm+"&userxmm="+userxmm+"&userem="+userem+"&lx=aq");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            //alert(xhr.responseText);
            //console.log(xhr.responseText)
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //成功
                document.getElementById("usermm").value="";//清除密码框
                document.getElementById("userxmm").value="";//清除新密码框
                document.getElementById("setup-main-lieb-aq").lang="45";
                document.getElementById("setup-main-lieb-aq").style="height:45px";
                document.getElementById("setup-main-lieb-aq-img").className="iconfont icon-weibiaotiy al-sxbh-setup";
                successpop(msg);
                //window.location.reload();
                //成功
            }else{
                warnpop(msg);
            }
        }
    };
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

    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/updata.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    //var usimg=userimg.toString();
    //xhr.send('usernc='+usernc+"&userimg="+userimg+"&userurl="+userurl+"&userhomeimg="+userhomeimg+"&userqm="+userqm+"&lx=zl");
    xhr.send('usernc='+usernc+"&lx=zlnc");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            //alert(xhr.responseText);
            //console.log(xhr.responseText)
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //成功
                var srkxm=document.getElementById("usernc").value;//获取输入框里的名字
                document.getElementById("setup-main-lieb-title-y-usernc").innerText=srkxm;
                document.getElementById("setupnc").lang="45";
                document.getElementById("setupnc").style="height:45px";
                document.getElementById("setup-main-lieb-qnc-img").className="iconfont icon-weibiaotiy al-sxbh-setup1";
                successpop(msg);
                //window.location.reload();
                //成功
            }else{warnpop(msg);
            }
        }
    };
}
//签名修改
function zhqm(){
    var userqm=document.getElementById("userqm").value;//获取昵称

    loadpop("正在修改签名，请稍后...",'ok');
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/updata.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    //var usimg=userimg.toString();
    //xhr.send('usernc='+usernc+"&userimg="+userimg+"&userurl="+userurl+"&userhomeimg="+userhomeimg+"&userqm="+userqm+"&lx=zl");
    xhr.send('userqm='+userqm+"&lx=zlqm");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            //alert(xhr.responseText);
            //console.log(xhr.responseText)
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //成功
                var srkxqm=document.getElementById("userqm").value
                document.getElementById("setup-main-lieb-title-y-userqm").innerText=srkxqm;
                document.getElementById("setupqm").lang="45";
                document.getElementById("setupqm").style="height:45px";
                document.getElementById("setup-main-lieb-qqm-img").className="iconfont icon-weibiaotiy al-sxbh-setup1";
                successpop(msg);
                //window.location.reload();
                //成功
            }else{
                warnpop(msg);
            }
        }
    };
}
//网址修改
function zhwz(){
    var userurl=document.getElementById("userurl").value;//获取昵称

    loadpop("正在修改网址，请稍后...",'ok');
    re = new RegExp("&","g"); //定义正则表达式
    //第一个参数是要替换掉的内容，第二个参数"g"表示替换全部（global）。
    //var userimg=userimg.replace(re, "#$#");
    var userurl=userurl.replace(re, "#$#");

    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/updata.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    //var usimg=userimg.toString();
    //xhr.send('usernc='+usernc+"&userimg="+userimg+"&userurl="+userurl+"&userhomeimg="+userhomeimg+"&userqm="+userqm+"&lx=zl");
    xhr.send('userurl='+userurl+"&lx=zlwz");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            //alert(xhr.responseText);
            //console.log(xhr.responseText)
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //成功
                var srkxqm=document.getElementById("userurl").value
                document.getElementById("setup-main-lieb-title-y-userurl").innerText=srkxqm;
                document.getElementById("setupwz").lang="45";
                document.getElementById("setupwz").style="height:45px";
                document.getElementById("setup-main-lieb-qwz-img").className="iconfont icon-weibiaotiy al-sxbh-setup1";
                successpop(msg);
                //window.location.reload();
                //成功
            }else{
                warnpop(msg);
            }
        }
    };
}





document.oncontextmenu=new Function("event.returnValue=false"); //禁止右键



function loaddemand(){
    //懒加载图片
const images = document.querySelectorAll('img')

// 回调函数接受一个 数组参数
const callback = entries => {
  entries.forEach(entry => {
    // 是否交叉，即是否进行到可视区域
    if (entry.isIntersecting) {
      // 获取图片节点
      const imgae = entry.target
      // 获取自定义属性
      const data_src = imgae.getAttribute('data-src')
      // 修改 src
      imgae.setAttribute('src', data_src)
      // 修改完属性之后，取消观察
      observer.unobserve(imgae)
    }
  })
}

// 拿着望远镜的人            callback: 需要触发条件，然后执行        触发两次：1 目标元素看得见 2 目标元素看不见
const observer = new IntersectionObserver(callback)

images.forEach(imgae => {
  // 具体观察那个节点
  observer.observe(imgae)
})
}

loaddemand();//调用一次懒加载
