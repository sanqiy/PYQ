/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
//触底加载函数
function isScrollAtBottom() {
  // 获取文档的总高度
  const documentHeight = Math.max(
    document.body.scrollHeight,
    document.documentElement.scrollHeight,
    document.body.offsetHeight,
    document.documentElement.offsetHeight,
    document.body.clientHeight,
    document.documentElement.clientHeight
  );
  // 获取窗口的高度
  const windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
  // 获取滚动条位置
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
  // 判断是否滚动到最底部
  return scrollTop + windowHeight >= documentHeight;
}



//视频自动播放与暂停
/*function videozb(){
// 获取所有视频元素
var videos = document.getElementsByTagName('video');
for (var i = 0; i < videos.length; i++) {
    var video = videos[i];
    var rect = video.getBoundingClientRect();

    // 检查视频元素是否在可视区域内
    if (rect.top >= 0 && rect.bottom <= window.innerHeight) {
        // 如果视频未播放，则播放视频
        if (video.paused) {
            video.play();
        }
    } else {
        // 如果视频正在播放，则暂停视频
        if (!video.paused) {
            video.pause();
        }
    }
}

}*/


//监听屏幕上下滚动
window.onscroll = function () {

    if (document.getElementById("sh-main-head-top")) {
        var mainHeadTop = document.getElementById('sh-main-head-top');

        var mainHeadTopLeft = document.querySelector('#sh-main-head-top .sh-main-head-top-left');
        var hasChildElements = mainHeadTopLeft && mainHeadTopLeft.children.length > 0;

        var mainHeadTopLeft2 = document.querySelector('#sh-main-head-top .sh-main-head-top-right');
        var hasChildElements2 = mainHeadTopLeft2 && mainHeadTopLeft2.children.length > 0;

        //var leftElement = mainHeadTop.getElementsByClassName('sh-main-head-top-left')[0];
        //var rightElement = mainHeadTop.getElementsByClassName('sh-main-head-top-right')[0];

        if (!hasChildElements && !hasChildElements2) {
          mainHeadTop.parentNode.removeChild(mainHeadTop);
        }
    }

    //变量t是滚动条滚动时，距离顶部的距离
    var t = document.documentElement.scrollTop || document.body.scrollTop;
    //当滚动到距离顶部200px时，返回顶部的锚点显示
    if (t >= 230) {
        /* console.log("大于") */
        if (document.getElementById("sh-main-head-top")) {//判断顶部导航栏是否存在
            document.getElementById("sh-main-head-top").style = "background:var(--dbztlys);backdrop-filter: saturate(180%) blur(20px);-webkit-backdrop-filter: saturate(180%) blur(20px)";
        }

        if (document.getElementById("top-left-1")) {//判断登录按钮是否存在
            document.getElementById("top-left-1").className = "iconfont icon-account-circle-line ri-sxzytxh";
        }
        if (document.getElementById("top-left-xx")) {
            document.getElementById("top-left-xx").className = "iconfont icon-weibiaoti ri-sxh";
        }


        if (document.getElementById("top-right-1")) {//判断消息按钮是否存在
            document.getElementById("top-right-1").className = "iconfont icon-xiaoxitongzhi ri-sxh";
        }

        if (document.getElementById("top-right-2")) {//判断消息按钮是否存在
            document.getElementById("top-right-2").className = "iconfont icon-tongxunlu ri-sxh";
        }

        if (document.getElementById("top-right-3")) {//判断发布按钮是否存在
            document.getElementById("top-right-3").className = "iconfont icon-xiangji1 ri-sxh";
        }


        if (document.getElementById("sh-main-top-mu")) {//判断消息按钮是否存在
        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
        if (tul == "bb") {
            document.getElementById("sh-main-top-mu").className = "iconfont icon-bofang-tongyong-copy ri-z-sxh";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbh");
        }else if(tul == "bbz"){
            document.getElementById("sh-main-top-mu").className = "iconfont icon-iconstop ri-z-sxh";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","zbbh");
        }
        }

        if (document.getElementById("sh-main-top-mu-bgmq")) {//判断消息按钮是否存在
            document.getElementById("sh-main-top-mu-bgmq").className = "iconfont icon-yinle_2 ri-z-sxh";
        }

        if (document.getElementById("sh-menu")) {
            document.getElementById("sh-menu").style.display="flex";
        }

    } else {          //恢复正常
        /* console.log("小于") */
        if (document.getElementById("sh-main-head-top")) {//判断顶部导航栏是否存在
            document.getElementById("sh-main-head-top").style = "background:var(--dbztlysh)";
        }

        if (document.getElementById("top-left-1")) {//判断登录按钮是否存在
            document.getElementById("top-left-1").className = "iconfont icon-account-circle-fill ri-sxzytx";
        }
        if (document.getElementById("top-left-xx")) {
            document.getElementById("top-left-xx").className = "iconfont icon-weibiaoti ri-sx";
        }


        if (document.getElementById("top-right-1")) {//判断消息按钮是否存在
            document.getElementById("top-right-1").className = "iconfont icon-lingdang ri-sx";
        }

        if (document.getElementById("top-right-2")) {//判断消息按钮是否存在
            document.getElementById("top-right-2").className = "iconfont icon-tongxunlu-copy ri-sx";
        }

        if (document.getElementById("top-right-3")) {//判断消息按钮是否存在
            document.getElementById("top-right-3").className = "iconfont icon-xiangji2 ri-sx";
        }


        if (document.getElementById("sh-main-top-mu")) {//判断消息按钮是否存在
        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
        if (tul == "bbh") {
            document.getElementById("sh-main-top-mu").className = "iconfont icon-jixu ri-z-sx";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bb");
        }else if(tul == "zbbh"){
            document.getElementById("sh-main-top-mu").className = "iconfont icon-iconstop ri-z-sx";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbz");
        }
        }

        if (document.getElementById("sh-main-top-mu-bgmq")) {//判断消息按钮是否存在
            document.getElementById("sh-main-top-mu-bgmq").className = "iconfont icon-yinle_2 ri-z-sx";
        }

        if (document.getElementById("sh-menu")) {
            document.getElementById("sh-menu").style.display="none";
        }
    }


    //触底自动加载更多文章
    if (isScrollAtBottom()) {
      //已滚动到最底部
      hqgd();
      //return;
    }

    //视频自动播放与暂停
}





/* 评论合集开 --评论与点赞按钮打开或关闭*/
function plk() {
    var ele = window.event.srcElement.id;
    /* console.log(scid); */
    var ids = "pl-" + ele;
    if (document.getElementById(ids).style.display != "flex") {
        //先隐藏上次打开评论菜单↓
        var arrs = document.getElementsByName("pl");
        for (var i = 0; i < arrs.length; i++) {
            /* alert(arrs[i].id); */
            document.getElementById(arrs[i].id).style = "display: none";
        }
        //先隐藏上次打开的评论菜单↑
        document.getElementById(ids).style = "display: flex";
    } else {
        document.getElementById(ids).style = "display: none";
    }
    //设置参数id
    document.getElementById("sh-tieid").innerText = ele;
}



//跳转到发布页
function fby(){
    window.location = './edit.php';
}



//表情列表开关
function bqkg() {
    event.stopPropagation();//禁止冒泡

    var textarea = document.getElementById("bletext");
    textarea.focus(); // 将焦点设置到textarea

    if (document.getElementById('biaoqing').style.display != "grid") {
        document.getElementById('biaoqing').style = "display: grid";
        document.getElementById('sh-pinglun-fs-right-bqimg').className = "iconfont icon-biaoqing ri-sxbqxzls"//修改表情开关为绿色
    } else {
        document.getElementById('biaoqing').style = "display: none";
        document.getElementById('sh-pinglun-fs-right-bqimg').className = "iconfont icon-biaoqing ri-sxbqxz"//修复表情开关为灰色
    }
}




//游客输入框开关
function ykkg() {
    event.stopPropagation();//禁止冒泡

    var textarea = document.getElementById("bletext");
    textarea.focus(); // 将焦点设置到textarea

    if (document.getElementById('sh-plk-yk').style.display != "flex") {
        document.getElementById('sh-plk-yk').style = "display: flex";
        document.getElementById('sh-pinglun-fs-right-ykkgb').className = "iconfont icon-yonghu1 ri-sxbqxzls"//修改表情开关为绿色
    } else {
        document.getElementById('sh-plk-yk').style = "display: none";
        document.getElementById('sh-pinglun-fs-right-ykkgb').className = "iconfont icon-yonghu1 ri-sxbqxz"//修复表情开关为灰色
    }
}




//评论框插入与删除
function plkkg() {
    var ele = window.event.srcElement.id;
    //设置参数id
    document.getElementById("sh-tieid").innerText = ele;

    event.stopPropagation();//禁止冒泡
    //document.getElementById("sh-plkz").style = "display: flex";

    if (document.getElementById("sh-zanp-pl-"+ele).style.display == "none") {
        document.getElementById("sh-zanp-pl-"+ele).style.display="flex";
    }
    //详情页
    if (document.getElementById("sh-dz-z-"+ele)) {
        if (document.getElementById("sh-dz-z-"+ele).style.display == "none") {
            document.getElementById("sh-dz-z-"+ele).style.display="flex";
        }
    }

    var shZanpPlElement = document.getElementById('sh-zanp-pl-'+ele);
    var firstChildElement = shZanpPlElement.firstElementChild;
    if (firstChildElement && firstChildElement.id === 'pinglunkuang') {
        //恢复原位
        plkgb();//清除上次插入操作
        //恢复原位
    } else {
        //在执行完上面代码后都需要把所有的自定义属性都改为0，防止有的属性没有复位到0导致bug
        var elements = document.querySelectorAll('[data-comkzt]');
        for (var i = 0; i < elements.length; i++) {
            elements[i].setAttribute('data-comkzt', '0');
        }

        //移动到第一个位置
        var pinglunkuangElement = document.getElementById('pinglunkuang');
        var shZanpPlElement = document.getElementById('sh-zanp-pl-'+ele);
        shZanpPlElement.insertBefore(pinglunkuangElement, shZanpPlElement.firstChild);

        //隐藏所有没有评论但是显示了的评论列表元素
        var elements = document.getElementsByClassName("sh-zanp-pl");
        for (var i = 0; i < elements.length; i++) {
          var element = elements[i];
          if (element.children.length === 0) {
            element.style.display = "none";
          }
        }

        document.getElementById("sh-tiehf").innerText = "false";
        document.getElementById("sh-tieea").innerText = "false";
        document.getElementById("bletext").placeholder = "评论";
        var textarea = document.getElementById("bletext");
        textarea.focus(); // 将焦点设置到textarea
    }

    //隐藏菜单
    var ids = "pl-" + ele;
    if (document.getElementById(ids).style.display != "none") {
        //先隐藏上次打开评论菜单↓
        var arrs = document.getElementsByName("pl");
        for (var i = 0; i < arrs.length; i++) {
            /* alert(arrs[i].id); */
            document.getElementById(arrs[i].id).style = "display: none";
        }
        //先隐藏上次打开的评论菜单↑
    }

}




function plkgb() {
    //设置出场动画

    //恢复输入框到原位
    var shPinglunElement = document.getElementById('pinglunkuang');
    var pinglunkfkElement = document.getElementById('pinglunkfk');
    pinglunkfkElement.appendChild(shPinglunElement);

    //在设置1前先把所有的自定义属性都改为0，防止有的属性没有复位到0导致bug
    var elements = document.querySelectorAll('[data-comkzt]');
    for (var i = 0; i < elements.length; i++) {
        elements[i].setAttribute('data-comkzt', '0');
    }

    //隐藏所有没有评论但是显示了的评论列表元素
    var elements = document.getElementsByClassName("sh-zanp-pl");
    for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        if (element.children.length === 0) {
            element.style.display = "none";
            //详情页
            var element = document.querySelector('.sh-dz-z');
            if (element) {
              element.style.display = 'none';
            }
            //详情页
        }
    }

    //清空输入框内容
    document.getElementById("bletext").value = "";
    document.getElementById("sh-tieid").innerText="-";
    document.getElementById("sh-tiehf").innerText="-";
    document.getElementById("sh-tieea").innerText="-";
}







/* 表情按钮事件 */
var input = document.getElementById("bletext");
var rangeIndex=null//光标地位
//监听失焦
input.onblur = function(){
  rangeIndex = this.selectionStart;//获取失焦时光标的地位
}
//插入函数
function biaoqzj(){
    var ele = window.event.srcElement.alt;//获取点击的表情alt
  if(rangeIndex){
    let oldVaue = input.value;
    input.value = oldVaue.slice(0,rangeIndex)+ele+oldVaue.slice(rangeIndex);
    rangeIndex = rangeIndex+ele.toString().length;
  }else{
    let oldVaue = input.value;
    input.value = oldVaue.slice(0,rangeIndex)+ele+oldVaue.slice(rangeIndex);
    rangeIndex = rangeIndex+ele.toString().length;
  }
  input.focus();
  input.setSelectionRange(rangeIndex,rangeIndex)//从新定位光标
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







/*点赞按钮事件 */
function dinazan() {
    var id = document.getElementById("sh-tieid").innerText;//获取点击的帖子id
    var user_id = getCookie("username");//取登录的账号
    var user_passid = getCookie("passid");//取登录的passid唯一id

    if (user_id == "" || user_passid == "") {
        //没有登录账号------------------------------------------------------------------------------
        //获取点赞span的内容判断是否为点赞
        var dzztl=document.getElementById("tiezdz-"+id).innerText;
        if (dzztl == "赞") {
            //执行点赞


    //显示提示信息
    loadpop("正在点赞,请稍后...","ok");
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/lcke.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('tieid='+id+"&user_id="+user_id+"&zts=0");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            //console.log(obj);
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //点赞成功
                    var name = msg//点赞者名字
                    if (document.getElementById("zans-" + id).style.display == "none") {
                    document.getElementById("zans-" + id).style.display = "flex";
                    document.getElementById("zanss-" + id).style.display = "block";
               }
    lis = document.getElementById('zlbeh-'+id).getElementsByTagName('li').length;
    //console.log(lis)
    if (lis < 1) {
            //没有点赞者显示点赞列表
      document.getElementById("zans-" + id).style.display = "flex";
         }else{
             //var name = "，"+name;
         }
         var li = '<li id="kfzan-'+id+'">'+name+'</li>';
          //$("#" + "zlbeh-"+id).append(li);
          if(document.getElementById("fkzan-"+id)){
             //$("#zlbeh-"+id+" li").eq(-1).before(li);
             document.getElementById("fkzan-"+id).innerText=name;
         }else{
             $("#" + "zlbeh-"+id).append(li);
         }


    document.getElementById("tiezdz-"+id).innerText="取消";
    document.getElementById("tiezimg-"+id).className="iconfont icon-aixin2 ri-sxdzlikehs";
    //显示提示信息
    successpop("点赞成功");
    loaddemand();//调用一次懒加载

                //点赞成功
            }else{
                //已经点赞过了
                //显示提示信息
                warnpop(msg)
                //已经点赞过了
            }
        }
    };

            //执行点赞
        }else{
            //执行取消点赞
            warnpop("游客暂不支持取消点赞哦");
            return;
            //执行取消点赞
        }

        //没有登录账号------------------------------------------------------------------------------
    }else{
        //登录了账号!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        //获取点赞span的内容判断是否为点赞
        var dzztl=document.getElementById("tiezdz-"+id).innerText;
        if (dzztl == "赞") {
            //执行点赞


    //显示提示信息
    loadpop("正在点赞,请稍后...","ok");
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/lcke.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('tieid='+id+"&user_id="+user_id+"&zts=0");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            //console.log(obj);
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //点赞成功
                var name = msg//点赞者名字
                    if (document.getElementById("zans-" + id).style.display == "none") {
                    document.getElementById("zans-" + id).style.display = "flex";
                    document.getElementById("zanss-" + id).style.display = "block";
               }
    lis = document.getElementById('zlbeh-'+id).getElementsByTagName('li').length;
    if (lis < 1) {
            //没有点赞者显示点赞列表
      document.getElementById("zans-" + id).style.display = "flex";
         }else{
             //var name = "，"+name;
         }
         var li = '<li id="zan-'+id+'" lang="'+msg+'">'+name+'</li>';
          //$("#" + "zlbeh-"+id).append(li);
          if(document.getElementById("fkzan-"+id)){
             $("#zlbeh-"+id+" li").eq(-1).before(li);
         }else{
             $("#zlbeh-"+id).append(li);
         }



    document.getElementById("tiezdz-"+id).innerText="取消";
    document.getElementById("tiezimg-"+id).className="iconfont icon-aixin2 ri-sxdzlikehs";
    //显示提示信息
    successpop("点赞成功");
    loaddemand();//调用一次懒加载

                //点赞成功
            }else{
                //已经点赞过了
                //显示提示信息
                warnpop(msg)
                //已经点赞过了
            }
        }
    };

            //执行点赞
        }else{
            //执行取消点赞


    //显示提示信息
    loadpop("正在取消点赞,请稍后...","ok");
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/lcke.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('tieid='+id+"&user_id="+user_id+"&zts=-1");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            var code =obj[0].code;//获取第一个的值 状态码
            var msg=obj[0].msg;//获取返回内容昵称
            if (code == 200) {
                //删除点赞成功
                //获取现有的点赞列表
                lis = document.getElementById('zlbeh-'+id).getElementsByTagName('li').length;

                var parentElement = document.getElementById('zlbeh-'+id);
                var elements = parentElement.querySelectorAll('#zan-'+id);
                // 遍历所有获取到的元素
                elements.forEach(function(element) {
                  if (element.getAttribute('lang') === msg) {
                    // 如果等于，则删除该元素
                    element.remove();
                  }
                });

                lis = document.getElementById('zlbeh-'+id).getElementsByTagName('li').length;
                if (lis < 1) {
                    //没有内容了则隐藏 点赞列表
                    document.getElementById("zans-" + id).style.display = "none";
                }


                document.getElementById("tiezdz-"+id).innerText="赞";
                document.getElementById("tiezimg-"+id).className="iconfont icon-aixin ri-sxdzlike";
                //显示提示信息
                successpop("点赞取消");
                //删除点赞成功
            }else{
                 //删除点赞失败
                 warnpop("取消点赞失败,请稍后再试");
                 //删除点赞失败
            }
        }
    };

            //执行取消点赞
        }

        //登录了账号!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    }






//隐藏菜单
var ids = "pl-" + id;
if (document.getElementById(ids).style.display != "none") {
    //先隐藏上次打开评论菜单↓
    var arrs = document.getElementsByName("pl");
    for (var i = 0; i < arrs.length; i++) {
        /* alert(arrs[i].id); */
        document.getElementById(arrs[i].id).style = "display: none";
    }
    //先隐藏上次打开的评论菜单↑
}


}





function plhuifu() {
    //获取点击的被回复者名字
    var elee = window.event.srcElement.lang;
    //获取点击的帖子id
    var eles = window.event.srcElement.id;
    //获取点击的邮箱
    var elea = window.event.srcElement.getAttribute('value');
    var dqzdsx=window.event.srcElement.getAttribute('data-comkzt');//取自定义属性 0为移动当前，1为恢复原位



    //移动div
    //document.addEventListener('click', function(event) {
      var target = event.target;
      if (target.parentElement.id === "sh-zanp-pl-"+eles) {
          if(dqzdsx == "1"){
              //恢复原位
              plkgb();//清除上次插入操作
              //恢复原位
          }else if(dqzdsx == "0"){
              //在设置1前先把所有的自定义属性都改为0，防止有的属性没有复位到0导致bug
              var elements = document.querySelectorAll('[data-comkzt]');
              for (var i = 0; i < elements.length; i++) {
                elements[i].setAttribute('data-comkzt', '0');
              }

              //移动到当前点击的元素后面
              var divToMove = document.getElementById('pinglunkuang');
              target.insertAdjacentElement('afterend', divToMove);

              var target = event.target;
              // 修改自定义属性的值为1
              target.setAttribute('data-comkzt', '1');//禁止再次移动到当前元素
              //移动到当前点击的元素后面

              //获得焦点
              var textarea = document.getElementById("bletext");
              textarea.focus(); // 将焦点设置到textarea

              //隐藏所有没有评论但是显示了的评论列表元素
              var elements = document.getElementsByClassName("sh-zanp-pl");
              for (var i = 0; i < elements.length; i++) {
                var element = elements[i];
                if (element.children.length === 0) {
                  element.style.display = "none";
                }
              }

              //设置参数id
              document.getElementById("sh-tieid").innerText = eles;
              document.getElementById("sh-tiehf").innerText = elee;
              document.getElementById("sh-tieea").innerText = elea;
              document.getElementById("bletext").placeholder = "回复" + elee;
          }

      }
    //});
}






//回复者名字url时间
function hfljurl(){
    //点击评论者的名字时跳转到它的网站,并且禁止冒泡，防止触发父元素事件
    event.stopPropagation();
}







/* 开启登录 */
function kqlogin() {
    var user_id = getCookie("username");//取登录的账号
    var user_passid = getCookie("passid");//取登录的passid唯一id

    if (user_id == "" || user_passid == "") {
        //没有登录账号
    }else{
        //登录了账号
        return;
    }
    document.getElementById("sh-login").style.display = "flex";
    /*var dqzy=document.getElementById("sh-login").className;
    var xgbzt=dqzy.replace(" move_4t","");
    document.getElementById("sh-login").className =xgbzt;
    document.getElementById("sh-login").className +=" move_4";*/
}

/* 关闭登录与注册弹窗 */
function gblogin() {
    /*var dqzy=document.getElementById("sh-login").className;
    var xgbzt=dqzy.replace(" move_4"," move_4t");
    document.getElementById("sh-login").className =xgbzt;
    window.setTimeout(function () {
            if (document.getElementById("sh-login")) {*///如果名为此id的div存在才执行
                document.getElementById("sh-login").style = "display: none";
            /*}
        }, 250);*/
}






/* 发布类型*/
function kqfabu() {
    document.getElementById("sh-fabu").style.display = "flex";
    //js_open()

}
/* 关闭 */
function gbfabu() {
    document.getElementById("sh-fabu").style.display = "none";
}


/* 消息通知弹窗 */
/* 开启 */
function kqnews() {
    document.getElementById("sh-news").style.display = "flex";
    //js_open()

}
/* 关闭 */
function gbnews() {
    document.getElementById("sh-news").style.display = "none";
}






/* 友链弹窗 */
/* 开启 */
function kqlink() {
    document.getElementById("sh-link").style.display = "flex";

}
/* 关闭 */
function gblink() {
    document.getElementById("sh-link").style.display = "none";
}








/* 发送评论按钮事件 */
function fasong() {
    var id = document.getElementById("sh-tieid").innerText;//获取点击的帖子id
    var tid = "sh-zanp-pl-" + id;

    if (document.getElementById("bletext").value == "") {
        warnpop("请输入评论内容");
        return;
    }
    var user_id = getCookie("username");//取登录的账号
    var user_passid = getCookie("passid");//取登录的passid唯一id

    if (document.getElementById("sh-plk-yk")) {
        var vis_name=document.getElementById("vis_name").value;//获取游客昵称
        var vis_email=document.getElementById("vis_email").value;//获取游客邮箱
        var vis_url=document.getElementById("vis_url").value;//获取游客网址
    }



    if (user_id == "" || user_passid == "") {
        //没有登录账号
        if(!document.getElementById("sh-plk-yk"))//判断有没有游客信息栏,没有说明网站没开启此功能
        {
            if(document.getElementById("sh-login")){
                document.getElementById("sh-login").style.display="flex";
            }else{
                warnpop("请先登录");
            }

            /*var dqzy=document.getElementById("sh-login").className;
            var xgbzt=dqzy.replace(" move_4t","");
            document.getElementById("sh-login").className =xgbzt;
            document.getElementById("sh-login").className +=" move_4";*/
            return;
        }
        if (vis_name == "" && vis_email == "") {
            ykkg();
            return;
            /*document.getElementById("sh-login").style.display="flex";
            var dqzy=document.getElementById("sh-login").className;
            var xgbzt=dqzy.replace(" move_4t","");
            document.getElementById("sh-login").className =xgbzt;
            document.getElementById("sh-login").className +=" move_4";
            return;*/
        }else{
            if(vis_name == ""){
                warnpop("请输入昵称");
                return;
            }else if(vis_email == ""){
                warnpop("请输入邮箱");
                return;
            }
            if(!vis_email.match(/^\w+@\w+\.\w+$/i)){
                warnpop("邮箱格式不正确");
                return;
            }
            //判断网址是否正确
            if (vis_url != "") {
                /*if (vis_url.includes('http://') || vis_url.includes('https://')) {
                }else{
                    warnpop("网址必须含有http[s]://");
                    return;
                }*/
                var urlPattern = /^(https?:\/\/)?([\w.-]+)\.([a-z]{2,})(\/\S*)?$/i;
                if (!urlPattern.test(vis_url)) {
                    warnpop("网址格式不正确");
                    return;
                }
            }
        }

    }
        //登录了账号

        var tieid=document.getElementById("sh-tieid").innerText;//取文章id
        var tiehf=document.getElementById("sh-tiehf").innerText;//取评被评论者昵称
        var tieea=document.getElementById("sh-tieea").innerText;//取被评论者账号
        var pltext=document.getElementById("bletext").value;//获取评论内容



    //显示提示信息
    loadpop("正在发送评论，请稍后...","ok");
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', '../api/fbpl.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('tieid='+tieid+'&tiehf='+tiehf+'&tieea='+tieea+'&pltext='+pltext+'&vis_name='+vis_name+'&vis_email='+vis_email+'&vis_url='+vis_url);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText)
            //return;
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
             var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
             var code =obj[0].code;//获取第一个的值 状态码
             var msg=obj[0].msg;//获取返回内容
             var uuzh=obj[0].uuzh;//获取返回账号
             var uunc=obj[0].uunc;//获取返回昵称
             var uutx=obj[0].uutx;//获取返回头像
             var uuwz=obj[0].uuwz;//获取返回网址
             var buunc=obj[0].buunc;//获取返回被评论者昵称
             var buuzh=obj[0].buuzh;//获取返回被评论者账号
             var pluunr=obj[0].pluunr;//获取返回评论内容
             if (code == 200) {
                 //评论成功
                 plkgb();
                     //console.log(xhr.responseText)
                     if (buunc == "false" || buuzh == "false") {
                         //没有被回复者
                         if (document.getElementById(tid).style.display == "none") {
                             document.getElementById(tid).style.display = "block";
                         }

                         //获取内容
                         var li = '<li lang="'+uunc+'" onclick="plhuifu()" id="'+id+'" value="'+uuzh+'" data-comkzt="0"><div class="sh-zanp-pl-n"><a href="'+uuwz+'" style="pointer-events: all;" class="sh-zanp-pl-n-nc" onclick="hfljurl()" target="_blank">'+uunc+'</a>：<span class="sh-zanp-pl-n-nr">'+pluunr+'</span></div></li>';
                         $("#" + tid).append(li);
                         //关闭评论框
                         //document.getElementById("sh-plkz").style.display = "none";
                         //document.getElementById("bletext").value = "";//清空输入框内容

                         // 通过元素的 id 获取到指定的元素
                         /*const element = document.getElementById("sh-content-"+id);
                         // 将页面滚动到指定元素的位置，并设置滚动行为为平滑滚动
                         element.scrollIntoView({
                             behavior: 'smooth'
                        });*/
                         //document.getElementById("plgdxs-"+tieid).lang=parseInt(document.getElementById("plgdxs-"+tieid).lang)+parseInt(1);//当前文章评论数量-1
                        //没有被回复者
                     }else{
                         //有被回复者
                         if (document.getElementById(tid).style.display == "none") {
                             document.getElementById(tid).style.display = "block";
                         }

                         //获取内容
                         var li='<li lang="'+uunc+'" onclick="plhuifu()" id="'+id+'" value="'+uuzh+'" data-comkzt="0"><div class="sh-zanp-pl-n"><a style="pointer-events: all;" class="sh-zanp-pl-n-nc" onclick="hfljurl()" target="_blank">'+uunc+'</a> <span> 回复 </span> <span class="sh-zanp-pl-n-nc">'+buunc+'</span>：<span class="sh-zanp-pl-n-nr">'+pluunr+'</span></div></li>';
                         $("#" + tid).append(li);
                         //关闭评论框
                         //document.getElementById("sh-plkz").style.display = "none";
                         //document.getElementById("bletext").value = "";//清空输入框内容

                         // 通过元素的 id 获取到指定的元素
                         /*const element = document.getElementById("sh-content-"+id);
                         // 将页面滚动到指定元素的位置，并设置滚动行为为平滑滚动
                         element.scrollIntoView({
                             behavior: 'smooth'
                        });*/
                         //document.getElementById("plgdxs-"+tieid).lang=parseInt(document.getElementById("plgdxs-"+tieid).lang)+parseInt(1);//当前文章评论数量加1
                         //有被回复者
                     }
                     successpop("评论发送成功");
                     loaddemand();//调用一次懒加载

                 //评论成功
             }else{
                 //评论失败
                 //关闭评论框
                 //document.getElementById("sh-plkz").style.display = "none";
                 //document.getElementById("bletext").value = "";//清空输入框内容

                 // 通过元素的 id 获取到指定的元素
                 /*const element = document.getElementById("sh-content-"+id);
                 // 将页面滚动到指定元素的位置，并设置滚动行为为平滑滚动
                 element.scrollIntoView({
                     behavior: 'smooth'
                 });*/
                 //判断是否需要审核
                 if (msg.indexOf('评论成功,审核通过后即可显示!') > -1) {
                     warnpop(msg);
                     plkgb();
                 }else{errorpop(msg)}

                 //评论失败
             }
        }
    };

        //登录了账号






}











/* 设置视频初始音量 */
/*document.getElementById("sh-content-video").volume = 0.2;*/
/* 设置视频循环播放 */
/*var elevideo = document.getElementById("sh-content-video");
elevideo.addEventListener('ended', function () { //结束
    elevideo.play();
}, false);*/







/* 音乐播放控制 */
function audbf(){
    var ele = window.event.srcElement.lang;//获取点击的id
    var name="musicurl-"+ele;//合成待取音乐播放地址
    var nam=document.getElementById(name).lang;//取音乐播放地址
    var naf=document.getElementById(name).className;//取音乐封面
    var bfkztu="sh-aud-left-plak-"+ele;//获得文章播放控制按钮
    var bfz=document.getElementById("musicplay").lang;//获得独立播放器播放状态

    var audio = document.getElementById("musicplay");//取到独立音乐播放器

    if (bfz == 0) {//等于0说明未播放 需要把音乐信息传给独立播放器
        if (ele != document.getElementById("musicplay").className) {
            document.getElementById("musicplay").src=nam;//将音乐播放地址传给独立播放器
            document.getElementById("musicplay").lang="1";//将播放器状态设为播放中
            document.getElementById("yszt").src=naf;//将音乐封面传给播放器
            document.getElementById("ming").src=naf;//将音乐封面传给播放器背景
            document.getElementById("musicplay").className=ele;//将本次的文章音乐id传给播放器
        }

        //设置封面
        var content= document.getElementById('yszt');
        content.dataset.src=naf;
        //设置背景
        var content= document.getElementById('ming');
        content.dataset.src=naf;


        var bfz=document.getElementById("musicplay").lang;//传完后再次获得播放状态
        //console.log(bfz)

        if (document.getElementById("sh-main-top-musicplay-b")) {
            document.getElementById("sh-main-top-musicplay-b").pause();//暂停首页顶部音乐
        }
        audio.play();//开始播放音乐
            document.getElementById(bfkztu).lang="1";//更新播放状态为播放中
            document.getElementById("musicplay").lang ="1"//将播放器状态设为播放中
            document.getElementById(bfkztu).className="iconfont icon-iconstop";//开始播放后设置播放按钮图片为暂停的图片
            document.getElementById("sh-musiccz-zb").className="iconfont icon-iconstop ri-z-sx"//开始播放设置独立音乐播放器为暂停图标
            if(document.getElementById("musicbc").lang == 1){
                document.getElementById("musicbc").style.right="10px";//显示独立音乐播放器
            }else{
                document.getElementById("musicbc").style.right="-80px";//显示独立音乐播放器
            }

            //仅针对经典播放器拖动
            /*if (document.getElementById("musicbc").style.display == "none") {
                document.getElementById("musicbc").style.display="grid";
            }*/

            timer=setInterval(function(){
               if(audio.paused){
                   // 当音乐没有播放时则关闭定时器
                   //---------console.log("关闭定时器");
                   //clearInterval(timer);
                   document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
                   document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
                   document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
                   document.getElementById("musicplay").lang ="0"//将播放器状态设为未播放
                   //console.log("定时器关闭")
                   clearInterval(timer);
                   return;
               }
            },500);
    }else if (bfz == 1) {//等于1说明音乐播放中 需要暂停
        audio.pause();//暂停播放音乐
        clearInterval(timer);//关闭定时器
        document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
        document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
        document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
        document.getElementById("musicplay").lang ="0"//将播放器状态设为未播放
        document.getElementById("sh-aud-left-plak-"+document.getElementById("musicplay").className).className="iconfont icon-jixu"
    }




    //判断点击的音乐是否和正在播放的是同一首,不是则切换到当前点击的音乐
if (bfz == 0 || bfz == 1) {
    //if (nam != document.getElementById("musicplay").src) {
    if (ele != document.getElementById("musicplay").className) {

        document.getElementById("musicplay").src=nam;//将音乐播放地址传给独立播放器
        document.getElementById("musicplay").lang="1";//将播放器状态设为播放中
        document.getElementById("yszt").src=naf;//将音乐封面传给播放器
        document.getElementById("ming").src=naf;//将音乐封面传给播放器背景
        document.getElementById("musicplay").className=ele;//将本次的文章音乐id传给播放器


            audio.play();//开始播放音乐
            document.getElementById(bfkztu).lang="1";//更新播放状态为播放中
            document.getElementById("musicplay").lang ="1"//将播放器状态设为播放中
            document.getElementById(bfkztu).className="iconfont icon-iconstop";//开始播放后设置播放按钮图片为暂停的图片
            document.getElementById("sh-musiccz-zb").className="iconfont icon-iconstop ri-z-sx"//开始播放设置独立音乐播放器为暂停图标
            //document.getElementById("musicbc").style.right="-80px";//显示独立音乐播放器
            if(document.getElementById("musicbc").lang == 1){
                document.getElementById("musicbc").style.right="10px";//显示独立音乐播放器
            }else{
                document.getElementById("musicbc").style.right="-80px";//显示独立音乐播放器
            }

            timer=setInterval(function(){
               if(audio.paused){
                   // 当音乐没有播放时则关闭定时器
                   //---------console.log("关闭定时器");
                   document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
                   document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
                   document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
                   document.getElementById("musicplay").lang ="0"//将播放器状态设为未播放
                   //console.log("不一样的定时器关闭")
                   clearInterval(timer);
                   return;
               }
            },500);
        //}
    }
}


}



//独立音乐播放器播放按钮事件
function bfpy(){//播放与暂停
    var ele=document.getElementById("musicplay").className;
    var bfkztu="sh-aud-left-plak-"+ele;//获得文章播放控制按钮
    var bfz=document.getElementById("musicplay").lang;//获得独立播放器播放状态
    var audio = document.getElementById("musicplay");//取到独立音乐播放器


    if (bfz == 0) {//0为未播放
            if (document.getElementById("sh-main-top-musicplay-b")) {
                document.getElementById("sh-main-top-musicplay-b").pause();//暂停首页顶部音乐
            }
            audio.play();//开始播放音乐
            document.getElementById(bfkztu).lang="1";//更新播放状态为播放中
            document.getElementById("musicplay").lang ="1"
            document.getElementById(bfkztu).className="iconfont icon-iconstop";//开始播放后设置播放按钮图片为暂停的图片
            document.getElementById("sh-musiccz-zb").className="iconfont icon-iconstop ri-z-sx"//开始播放设置独立音乐播放器为暂停图标
            //document.getElementById("musicbc").style.right="-80px";//显示独立音乐播放器
            if(document.getElementById("musicbc").lang == 1){
                document.getElementById("musicbc").style.right="10px";//显示独立音乐播放器
            }else{
                document.getElementById("musicbc").style.right="-80px";//显示独立音乐播放器
            }


            timer=setInterval(function(){
               if(audio.paused){
                   // 当音乐没有播放时则关闭定时器
                   //---------console.log("关闭定时器");
                   document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
                   document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
                   document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
                   document.getElementById("musicplay").lang ="0"//将播放器状态设为未播放
                   clearInterval(timer);
                   return;
               }
            },500);
        }else if (bfz == 1) {//等于2说明音乐播放中 需要暂停
        audio.pause();//暂停播放音乐
        //clearInterval(timer);//关闭定时器
        document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
        document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
        document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
        document.getElementById("musicplay").lang ="1"//将播放器状态设为播放中
        document.getElementById("sh-aud-left-plak-"+document.getElementById("musicplay").className).className="iconfont icon-jixu"
    }
}



//清除歌曲并隐藏播放器
function bfpg(){
    var ele=document.getElementById("musicplay").className;
    var bfkztu="sh-aud-left-plak-"+ele;//获得文章播放控制按钮
    var bfz=document.getElementById("musicplay").lang;//获得独立播放器播放状态

    document.getElementById("musicplay").scr=""
    var audio = document.getElementById("musicplay");//取到独立音乐播放器


        audio.pause();//暂停播放音乐
        clearInterval(timer);//关闭定时器
        document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
        document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
        document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
        document.getElementById("musicplay").lang ="0"//将播放器状态设为未播放

        document.getElementById("sh-aud-left-plak-"+document.getElementById("musicplay").className).className="iconfont icon-jixu"
        document.getElementById("musicplay").className =""
        //document.getElementById("musicbc").style.right="-800px";
        if(document.getElementById("musicbc").lang == 1){
            document.getElementById("musicbc").style.right="-200px";
        }else{
            document.getElementById("musicbc").style.right="-800px";
        }

        //仅针对经典播放器拖动
        /*var  str= document.getElementById("musicbc").style.left;
        var Newstr = str.replace("px", "");
        if (Newstr != "") {
            document.getElementById("musicbc").style.display="none";
        }*/
}


//独立播放器封面点击展开与收起
function mbpy(){
    if(document.getElementById("musicbc").lang == 1){
        return;
    }
    var elez = document.getElementById("yszt").lang;
    if(elez == 0){
        //展开
        const myDiv = document.getElementById('musicbc');
        myDiv.style.transform = 'translateX(-80px)';
        document.getElementById("yszt").lang="1";
    }else{
        //收起
        const myDiv = document.getElementById('musicbc');
        myDiv.style.transform = 'translateX(0px)';
        document.getElementById("yszt").lang="0";
    }
}







//注册账号按钮事件
function regzc(){
    //执行账号注册
        var zh=document.getElementById("login-zh").value;//取账号
        var em=document.getElementById("login-email").value;//取邮箱
        var mm=document.getElementById("login-pass").value;//取密码
        if (document.getElementById("login-yzm")) {
            var yzm=document.getElementById("login-yzm").value;//取验证码
        }
        //判断所有参数是否为空和邮箱格式是否正确
        if (zh == "") {
            //alert("账号未输入!");
            warnpop("账号未输入");
            return;
        }else if(em == ""){
            //alert("邮箱未输入!");
            warnpop("邮箱未输入");
            return;
        }else if(mm == ""){
            //alert("密码未输入!");
            warnpop("密码未输入");
            return;
        }else{
            var regx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
            if (regx.test(em) != true) {
                //alert("邮箱格式错误!");
                warnpop("邮箱格式错误");
                return;
            }
        }
        if (document.getElementById("login-yzm")) {
            if (yzm == "") {
                warnpop("验证码未输入");
                return;
            }
        }

        //判断账号密码长度是否符合
        if (zh.length < 5) {//账号要求 不小于5 不大于32 位
            warnpop("账号不可低于5位数");
            return;
        }else if(zh.length > 32){
            warnpop("账号不可大于32位数");
            return;
        }else if(mm.length < 3){
            warnpop("密码不可低于3位数");
            return;
        }else if(mm.length > 16){
            warnpop("密码不可大于16位数");
            return;
        }

        //显示提示信息
        loadpop("正在注册账号，请稍后...","ok")

        //提交注册
        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/reg.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('zh='+zh+'&em='+em+'&mm='+mm+"&allkey="+myallkeyVar+"&yzm="+yzm);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                //alert("未获取到数据!");
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "账号注册成功!") {
                //注册成功
                //alert(xhr.responseText);
                successpop(xhr.responseText);
                //document.getElementById("login-zh").value="";//清空账号
                document.getElementById("login-email").value="";//清空邮箱
                //document.getElementById("sh-login-main-con-anu").style.display="none";//隐藏邮箱输入框
                //document.getElementById("sh-left-dl").style.display="block";//显示登录按钮
                //document.getElementById("sh-left-zc").style.display="none";//隐藏注册按钮
                //document.getElementById("sh-zck-an").innerText="注册账号";//底部切换
                //document.getElementById("login-pass").value="";//清空密码
                zcanxy();

            }else{
                //注册失败
                errorpop(xhr.responseText);
            }
        }
    };
        //执行账号注册

}


//发送注册验证码
if (document.getElementById('yzm')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('yzm').addEventListener('click', function() {
      var zh=document.getElementById("login-zh").value;//取账号
      var em=document.getElementById("login-email").value;//取邮箱
      var mm=document.getElementById("login-pass").value;//取密码

      if (document.getElementById("yzm").innerText != "发送") {
          return;
      }
      if(em == ""){
            warnpop("邮箱未输入");
            return;
        }else{
            var regx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
            if (regx.test(em) != true) {
                warnpop("邮箱格式错误");
                return;
            }
        }

      //显示提示信息
        loadpop("正在发送验证码，请稍后...","ok")

        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/reg.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('zh='+zh+'&em='+em+'&mm='+mm+"&allkey="+myallkeyVar+"&fsyzm=1");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                //alert("未获取到数据!");
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "发送成功") {
                //成功
                successpop(xhr.responseText);
                let countDown = 60; // 倒计时秒数
                let yzmElement = document.getElementById('yzm'); // 获取id为yzm的元素
                // 设置倒计时函数
                let countDownFunction = setInterval(function() {
                  yzmElement.textContent = '发送(' + countDown + '秒)'; // 更新元素内容
                  countDown--; // 减少倒计时秒数
                  if (countDown <= 0) { // 如果倒计时结束
                    yzmElement.textContent = '发送'; // 设置元素内容为"发送"
                    clearInterval(countDownFunction); // 停止倒计时函数
                  }
                }, 1000); // 每秒更新一次
            }else{
                //失败
                errorpop(xhr.responseText);
            }
        }
    };

  });
}




//禁止注册账号和找回密码按钮响应回车事件
function checkKeyDown(event) {
  if (event.keyCode === 13) {
    event.preventDefault();
  }
}

//注册按钮显示与隐藏
function zcanxy(){
    var zcan=document.getElementById("sh-login-main-con-anu").style.display;
    if (zcan == "none") {
        //document.getElementById("sh-left-zc").style.display="block";//显示注册按钮
        //document.getElementById("sh-left-dl").style.display="none";//隐藏登录按钮
        document.getElementById("sh-login-main-con-anu").style.display="flex";//显示邮箱
        if (document.getElementById("login-yzm")) {
            document.getElementById("sh-login-main-con-yzmwk").style.display="flex";//显示验证码
        }
        document.getElementById("sh-zck-an").innerText="登录账号";//设置底部开关注册为登录
        document.getElementById("zhdzsx").innerText="账号注册";//设置标题
        //document.getElementById("sh-left-zc").type="submit";
        //document.getElementById("sh-left-dl").type="button";
        document.getElementById("sh-left-dlzc").className="sh-left"
        document.getElementById("sh-left-dlzc").value="注册";
        document.getElementById('sh-left-dlzc').setAttribute('onclick', 'regzc()');

    }else{
        //document.getElementById("sh-left-zc").style.display="none";//隐藏注册按钮
        //document.getElementById("sh-left-dl").style.display="block";//显示登录按钮
        document.getElementById("sh-login-main-con-anu").style.display="none";//隐藏邮箱
        if (document.getElementById("login-yzm")) {
            document.getElementById("sh-login-main-con-yzmwk").style.display="none";//隐藏验证码
        }
        document.getElementById("sh-zck-an").innerText="注册账号";//设置底部开关注册为注册
        document.getElementById("zhdzsx").innerText="账号登录";//设置标题
        //document.getElementById("sh-left-zc").type="button";
        //document.getElementById("sh-left-dl").type="submit";
        //document.getElementById('sh-left-dl').setAttribute('onclick', 'a()');
        document.getElementById("sh-left-dlzc").className="sh-right"
        document.getElementById("sh-left-dlzc").value="登录";
        document.getElementById('sh-left-dlzc').setAttribute('onclick', 'logy()');
    }
    return false;
}





//找回密码事件
function zhmm(){
    var userzhzh=document.getElementById("login-zh").value;//获取需要找回的账号
    if (userzhzh == "") {
        warnpop("请先输入您的账号");
    }else{
        loadpop("正在验证信息，请稍后","ok");
        window.location.href='./repass.php?useke='+userzhzh;
    }
}





//登录按钮事件
function logy(){
    //执行登录
        var zh=document.getElementById("login-zh").value;//取账号
        var mm=document.getElementById("login-pass").value;//取密码
        if (zh == "") {
            warnpop("请输入账号");
            return;
        }else if(mm == ""){
            warnpop("请输入密码");
            return;
        }

        //判断账号密码长度是否符合
        if (zh.length < 5) {//账号要求 不小于5 不大于32 位
            warnpop("账号不可低于5位数");
            return;
        }else if(zh.length > 32){
            warnpop("账号不可大于32位数");
            return;
        }else if(mm.length < 3){
            warnpop("密码不可低于3位数");
            return;
        }else if(mm.length > 16){
            warnpop("密码不可大于16位数");
            return;
        }

        //显示提示信息,带遮罩
        loadpop("登录中，请稍后...","ok");


        //提交登录
        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/login.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('zh='+zh+'&mm='+mm);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "登录成功!") {
                //登录成功
                successpop("登录成功，即将跳转","ok");
                window.location.href="./index.php";
                //登录成功
            }
            else{
                //登录失败
                errorpop(xhr.responseText);
            }

        }
    };

        //执行登录
}






//消息操作菜单开关
function js_menu() {
    if (document.getElementById("xxtzsul").innerText == "0") {
        return;
    }
    var adElement = document.getElementById('js_menu');
    var isAdVisible = adElement.style.display === 'none';

    if (isAdVisible) {
      adElement.style.display = 'flex';
    } else {
      adElement.style.display = 'none';
    }
}

//消息删除选中
function xxsczt(){
    event.stopPropagation();
    if (document.getElementById("xxtzsul").innerText == "0") {
        return;
    }
    var xxsczt=document.getElementById("xxsczt").lang;//获取消息选中状态 0为已读 -1为删除
    if (xxsczt == 0) {
        //为0 则设置成-1 并且改变消息列表颜色 进入删除状态
        document.getElementById("xxsczt").lang="-1";
        //document.getElementById("xxsczt").className="iconfont icon-shanchu ri-sxhsh";
        document.getElementById("js_menu").style.display="none";
        //document.getElementById("sh-news-con").style="background: var(--fgxys);";
        //document.getElementById("xxscztqbk").style.display="flex";

        document.querySelectorAll('.sh-xxliebfm .delmes').forEach(element => {
            element.style.display = 'flex';
        });

        //为0 则设置成-1 并且改变消息列表颜色 进入删除状态
    }else{
        //设置成0 并且改变消息列表颜色 进入正常状态
        document.getElementById("xxsczt").lang="0";
        //document.getElementById("xxsczt").className="iconfont icon-shanchu ri-sxhs";
        document.getElementById("js_menu").style.display="none";
        //document.getElementById("sh-news-con").style="background: var(--cobg)";
        //document.getElementById("xxscztqbk").style.display="none";

        document.querySelectorAll('.sh-xxliebfm .delmes').forEach(element => {
            element.style.display = 'none';
        });

        //设置成0 并且改变消息列表颜色 进入正常状态
    }
}



//删除所有消息
function xxscztqb(){
    if (document.getElementById("xxtzsul").innerText == "0") {
        return;
    }
    if (confirm("确定要删除所有消息吗?")) {
        // 用户点击了确认按钮
      } else {
        // 用户点击了取消按钮或关闭了弹窗
        return;
      }

    var ele = window.event.srcElement.id;//获取点击的id
    loadpop("正在删除消息，请稍后...","ok");
        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/messg.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('plid=-2'+"&type=-2");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "消息已删除") {
                //document.getElementById(xxzt).style.display="none";//隐藏小红点提示  当用户点击消息说明消息已被查看 则隐藏小红点
                //ele.remove();
                //删除页面上的所有消息
                // 获取指定元素
                var element = document.getElementById("sh-news-con");
                // 删除所有子元素
                while (element.firstChild) {
                  element.removeChild(element.firstChild);
                }

                document.getElementById("xxsczt").lang="0";
                //document.getElementById("xxsczt").className="iconfont icon-shanchu ri-sxhs";
                //document.getElementById("xxscztqbk").style.display="none";

                var xxtszajg=0;//数量数字减1
                document.getElementById("xxtzsul").innerText=xxtszajg;//将新结果显示上去
                //删除页面上的对应消息
                if (document.getElementById("xiaoxhd")) {
                    document.getElementById("xiaoxhd").style.display="none";//隐藏消息通知红点
                }
                document.getElementById("js_menu").style.display="none";

                successpop("已删除所有消息");
            }else{errorpop(xhr.responseText);}
        }
    };
    event.stopPropagation();//禁止冒泡
}

//已读所有消息
function xxscyd(){
    if (document.getElementById("xxtzsul").innerText == "0") {
        return;
    }
    var ele = window.event.srcElement.id;//获取点击的id
    loadpop("正在已读，请稍后...","ok");
        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/messg.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('plid=-3'+"&type=-3");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "消息已读") {
                // 获取 id 为 "sh-news-con" 的元素
                var container = document.getElementById("sh-news-con");
                // 递归函数，用于遍历元素及其子元素
                function hideElementsWithPrefix(element, prefix) {
                  // 遍历元素的子元素
                  for (var i = 0; i < element.children.length; i++) {
                    var child = element.children[i];

                    // 如果子元素的 id 以指定前缀开头，则隐藏该元素
                    if (child.id.startsWith(prefix)) {
                      child.style.display = "none";
                    }

                    // 递归调用，继续查找子元素的子元素
                    hideElementsWithPrefix(child, prefix);
                  }
                }
                // 调用函数，隐藏以 "xxztx-" 开头的元素及其子元素
                hideElementsWithPrefix(container, "xxztx-");
                if (document.getElementById("xiaoxhd")) {
                    document.getElementById("xiaoxhd").style.display="none";
                }
                document.getElementById("js_menu").style.display="none";


                successpop("已读所有消息");
            }else{errorpop(xhr.responseText);}
        }
    };
    event.stopPropagation();//禁止冒泡
}


//查看消息详情按钮事件
function mesgxq(){
    event.stopPropagation();

    var ele = window.event.srcElement.id;//获取点击的id
    var elela = window.event.srcElement.lang;//获取点击的id
    //var bt="xxtzidtitle-"+ele;//对得对应标题id
    var nr="xxtzidtext-"+ele;//对得对应内容id
    var xxzt="xxztx-"+ele;//获取消息小红点id

    //var btt=document.getElementById(bt).innerText;//取得消息标题内容
    var btt=document.getElementById(nr).lang;//取得消息标题内容
    var nrr=document.getElementById(nr).innerText;//取得消息内容


    //alert(btt+"\n"+nrr);
        //swal(btt,nrr,'success');//弹窗消息标题与内容
        //document.getElementById(xxzt).style.display="none";//隐藏小红点提示  当用户点击消息说明消息已被查看 则隐藏小红点

        var xxsczt=document.getElementById("xxsczt").lang;//获取消息选中状态
        if (xxsczt == 0) {
            //为0则是设置已读状态

            if (elela == "#-1") {
                warnpop("此消息已不存在，无法查看");
                return;
            }
                    //设置消息为已读 --提交服务器
        //swal(btt,nrr,'success');//弹窗消息标题与内容


        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/messg.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('plid='+ele+"&type=0");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            //alert(xhr.responseText);
            if (xhr.responseText == "消息已读") {
                document.getElementById(xxzt).style.display="none";//隐藏小红点提示  当用户点击消息说明消息已被查看 则隐藏小红点
                if (elela != "#-1") {
                   location.href='./view.php?cid='+elela;
                }
            }else{errorpop(xhr.responseText);}
        }
    };

        //设置消息为已读 --提交服务器

            //为0则是设置已读状态
        }else if(xxsczt == -1){
            //为-1则是设置删除状态
            //为-1则是设置删除状态
        }

}



//删除单个消息通知
function demes(){
    if (document.getElementById("xxtzsul").innerText == "0") {
        return;
    }
    var ele = window.event.srcElement.id;//获取点击的id
    loadpop("正在删除消息，请稍后...","ok");
        // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/messg.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('plid='+ele+"&type=-1");
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            if (xhr.responseText == "消息已删除") {
                //document.getElementById(xxzt).style.display="none";//隐藏小红点提示  当用户点击消息说明消息已被查看 则隐藏小红点
                //ele.remove();
                //删除页面上的对应消息
                var x = document.getElementById(ele);
                //如果对象x不为空
                if (x != null){
                    x.remove();
                }

                var xxtsza=document.getElementById("xxtzsul").innerText;//获取当前条数数字内容
                if (xxtsza > 0) {
                    var xxtszajg=xxtsza-1;//数量数字减1
                }else{var xxtszajg=xxtsza;}

                document.getElementById("xxtzsul").innerText=xxtszajg;//将新结果显示上去
                //删除页面上的对应消息
                if (xxtszajg <= 0) {
                    if (document.getElementById("xiaoxhd")) {
                        document.getElementById("xiaoxhd").style.display="none";//隐藏消息通知红点
                    }
                }

                successpop("消息已删除");
            }else{errorpop(xhr.responseText);}
        }
    };
    event.stopPropagation();//禁止冒泡
}












//获取更多的文章
function hqgd(){
    if (!document.getElementById("footer-text-zt")) {
        return;
    }
    if (document.getElementById("footer-text-zt").innerText == "- 没有更多啦 -") {
        return;
    }
    if (document.getElementById("footer-text-zt").innerText == "正在加载..") {//防止触底加载多次触发
        return;
    }
    var page=String(document.getElementById("footer-text-hqgd").innerText);//获取当前的文章数量
    // 获取URL中的GET参数
    var urlParams = new URLSearchParams(window.location.search);
    // 获取SO值
    var soValue = urlParams.get('so');

    //显示提示信息
    //loadpop("正在加载...","ok");
    //显示加载中动画
    document.getElementById("footer-text-zt").style.animation = "colorChange 0.8s infinite";
    document.getElementById("footer-text-zt").innerText="正在加载..";

    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/api.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('page='+page+'&so='+soValue);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //清除加载中动画
            document.getElementById("footer-text-zt").removeAttribute("style");
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                document.getElementById("footer-text-zt").innerText="加载更多..";
                return;
            }
            //console.log(xhr.responseText);
            var suu=String(xhr.responseText);//将结果转为字符串 {+*&".$wid."+*&}
            //console.log(suu)
            //判断是否还有数据
            var bool = suu.includes("<div");
            if(bool>0){
                }else{
                    document.getElementById("footer-text-zt").innerText="- 没有更多啦 -";
                    //successpop("没有更多啦");
                    return;
                }


            //将获取的数据追加到页面
            var li=suu;
            //console.log(li)
            var str = li;//设置字符串
            str = str.match(/8965896582315263skfjskfjsgdfgdgddjskdjgdsdsjhd(\S*)fedfrtgg6h5j8u5j5d45hgd5s4fh5sd5gdf8w5dss48sd1fds56ds156/)[1];//取出当前数量
            //console.log(li)
            var qk="8965896582315263skfjskfjsgdfgdgddjskdjgdsdsjhd"+str+"fedfrtgg6h5j8u5j5d45hgd5s4fh5sd5gdf8w5dss48sd1fds56ds156";//需要清空数量数字
            var li=li.replace(qk,"")
            $("#sh-nrbk").append(li);
            //successpop("已加载更多内容");
            document.getElementById("footer-text-zt").innerText="加载更多..";
            loaddemand();//调用一次懒加载


            //数据追加完成后获取页面一共有多少条数据 并设置它
            //var res=$(".sh-content").length;
            //alert(res)
            document.getElementById("footer-text-hqgd").innerText=str;
        }else{document.getElementById("footer-text-zt").removeAttribute("style");}
    };

}















//获取更多的评论
/*function plgdh(){
    var ele = window.event.srcElement.id;//获取点击的id
    //console.log(ele)
    var wyid=ele.replace("plgdxs-","")//将id前面的字母去掉 作文文章id
    //console.log(wyid)
    var lan =document.getElementById(ele).lang;//获取当前的评论数量
    //console.log("当前"+lan)
    var sljhs=parseInt(lan)+parseInt(10);//将当前数量加10备用 结合comm.php文件的评论加载数量使用
    //console.log("加后"+sljhs)
    var fjys="sh-zanp-pl-"+wyid;//获取父级元素id
    //console.log(fjys)


    //显示提示信息
    loadpop("评论加载中，请稍后...","ok");
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/comm.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('page='+lan+'&wzcidd='+wyid);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                //没有数据
                document.getElementById("plgdxs-"+wyid).style.display="none";
                warnpop("没有更多评论");
                return;
            }

            var plsu=xhr.responseText;
            //console.log(plsu)
            //将获取的数据追加到页面
            var li=plsu;
            $("#"+fjys).append(li);

            var temp = document.getElementById(fjys);//获取父元素id
            var linum = temp.getElementsByTagName("li").length;//获取更新后的元素个数
            //console.log("更新后的"+linum)
            if (linum>sljhs) {
                //console.log("大于")
                document.getElementById(ele).lang=linum-1;
            }else{
                document.getElementById(ele).lang=linum;//去掉+1 评论正序排列不需要加1
            }

            successpop("已加载更多评论");
            loaddemand();//调用一次懒加载
        }
    };
}
*/











//图片放大区域函数集↓

//禁止放大的图片冒泡
/*if (document.getElementById('imgfd')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('imgfd').addEventListener('click', function() {
    // 在这里编写要执行的事件处理逻辑
    event.stopPropagation();
  });
}
//图片拖动查看
function tuodongimg(){
    var imgElement1 = document.getElementById('imgfd');
var parentElement = document.getElementById('imgfdb');
var initialX, initialY, currentX, currentY;
var dragging = false;

imgElement1.addEventListener('mousedown', function(event) {
    initialX = event.clientX - imgElement1.offsetLeft;
    initialY = event.clientY - imgElement1.offsetTop;
    dragging = true;
});
//电脑
document.addEventListener('mousemove', function(event) {
    if (dragging) {
        currentX = event.clientX - initialX;
        currentY = event.clientY - initialY;
        imgElement1.style.left = currentX + 'px';
        imgElement1.style.top = currentY + 'px';
    }
});

document.addEventListener('mouseup', function() {
    dragging = false;
});
//手机
imgElement1.addEventListener('touchstart', function(event) {
    document.body.style.overflow = "hidden";//隐藏滚动条
    initialX = event.touches[0].clientX - imgElement1.offsetLeft;
    initialY = event.touches[0].clientY - imgElement1.offsetTop;
    dragging = true;
});

document.addEventListener('touchmove', function(event) {
    if (dragging) {
        document.body.style.overflow = "hidden";//隐藏滚动条
        currentX = event.touches[0].clientX - initialX;
        currentY = event.touches[0].clientY - initialY;
        imgElement1.style.left = currentX + 'px';
        imgElement1.style.top = currentY + 'px';
    }
});

document.addEventListener('touchend', function() {
    dragging = false;
});
}
tuodongimg();



//查看大图时  缩放图像 电脑 手机 -----------------↓↓↓
// 获取图片元素
const imgElement = document.getElementById("imgfd");
// 设置最小和最大缩放比例
const minScale = 0.5; // 最小缩小比例
const maxScale = 5; // 最大放大比例
let currentScale = 1; // 当前缩放比例

// 监听鼠标滚轮事件
imgElement.addEventListener('wheel', function(event) {
    event.preventDefault(); // 阻止默认的滚动行为
  if (document.getElementById("imgfdb").style.display=="flex") {//图片放大的情况才执行
    // 滚轮向上滚动时放大，向下滚动时缩小
    if (event.deltaY < 0) {
        zoomIn();
    } else {
        zoomOut();
    }
  }
});  */
// 处理手机双指缩放事件
/*imgElement.addEventListener('touchstart', function(event) {
    if (event.touches.length === 2) { // 如果是双指触摸
    if (document.getElementById("imgfdb").style.display=="flex") {//图片放大的情况才执行
        let startX = event.touches[0].clientX;
        let startY = event.touches[0].clientY;
        let endX = event.touches[1].clientX;
        let endY = event.touches[1].clientY;

        // 计算两个手指之间的距离变化，用于判断是放大还是缩小
        let initialDistance = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
        // 监听触摸结束事件
        imgElement.addEventListener('touchend', function(event) {
            let finalDistance = Math.sqrt(Math.pow(endX - event.changedTouches[0].clientX, 2) + Math.pow(endY - event.changedTouches[0].clientY, 2));
            let scaleChange = finalDistance / initialDistance; // 计算缩放比例变化
            // 如果距离变大，说明是放大操作，否则是缩小操作
            if (scaleChange > 1) {
                zoomIn();
            } else {
                zoomOut();
            }
            // 移除触摸结束事件的监听器，防止多次触发
            imgElement.removeEventListener('touchend', arguments.callee);
        });
    }}
});  */
// 放大图片的函数
/*function zoomIn() {
    if (currentScale < maxScale) {
        currentScale += 0.1; // 每次放大0.1倍，可以根据需要调整
        imgElement.style.transform = `scale(${currentScale})`; // 设置缩放比例
    }
}
// 缩小图片的函数
function zoomOut() {
    if (currentScale > minScale) {
        currentScale -= 0.1; // 每次缩小0.1倍，可以根据需要调整
        imgElement.style.transform = `scale(${currentScale})`; // 设置缩放比例
    }
}
//查看大图时  缩放图像 电脑 手机 -----------------↑↑↑


//当放大图片时 监控 左右 esc 滚轮 键盘
window.addEventListener('keydown', function(event) {
    if (document.getElementById("imgfdb").style.display=="flex") {//图片放大的情况才执行
        // event.keyCode === 37 代表左箭头
        if (event.keyCode === 37) {
            imgfdqs();
        }
        // event.keyCode === 39 代表右箭头
        else if (event.keyCode === 39) {
            imgfdqx();
        }
        // event.keyCode === 27 代表 Esc 键
        else if (event.keyCode === 27) {
            imgfdg();
        }
    }

});


//图片切换上一张
function imgfdqs(){
    event.stopPropagation();
    document.getElementById("imgfd").style= "";//设置放大图片初始大小
    currentScale = 1; // 恢复当前缩放比例
    var ele = document.getElementById("imgfd-sy").lang; // 获取当前照片所属文章id

// 获取元素
let container = document.getElementById('imglib-' + ele);
let elements = container.getElementsByClassName('sh-content-right-img-pic');
let imgSrcArray = [];

// 提取图片src，并存储为二维数组
for(let i = 0; i < elements.length; i++) {
    let imgElement = elements[i].getElementsByTagName('img')[0];
    imgSrcArray[i] = [imgElement.src, i]; // 存储图片src和原始索引
}

// 对图片src数组从左到右从上到下排序
imgSrcArray.sort(function(a, b) {
    if (a[1] === b[1]) { // 如果原始索引相同，按照图片src排序
        return a[0].localeCompare(b[0]);
    } else {
        return a[1] - b[1]; // 按照原始索引排序
    }
});

// 获取imgfd元素的src图片名
let imgfd = document.getElementById('imgfd');
let imgfdSrc = imgfd.src;
let currentImgIndex = imgSrcArray.findIndex(function(item) { // 查找当前图片在排序后的数组中的索引
    return item[0] === imgfdSrc;
});

if (currentImgIndex === 0) {
    currentImgIndex = imgSrcArray.length - 1; // 如果已经是第一张，切换到最后一张
} else {
    currentImgIndex--; // 点击上一张时就按顺序切换
}

imgfd.src = imgSrcArray[currentImgIndex][0]; // 将src赋值给imgfd元素

// 设置 'imgfdb-fk-tu-dang' 元素的文本内容为当前图片的位置
document.getElementById('imgfdb-fk-tu-dang').textContent = currentImgIndex + 1;

imgfdjzpd(); // 调用你的图片切换函数


}

//图片切换下一张
function imgfdqx(){
    event.stopPropagation();
    document.getElementById("imgfd").style= "";//设置放大图片初始大小
    currentScale = 1; // 恢复当前缩放比例
    var ele = document.getElementById("imgfd-sy").lang; // 获取当前照片所属文章id

// 获取元素
let container = document.getElementById('imglib-' + ele);
let elements = container.getElementsByClassName('sh-content-right-img-pic');
let imgSrcArray = [];

// 提取图片src，并存储为二维数组
for(let i = 0; i < elements.length; i++) {
    let imgElement = elements[i].getElementsByTagName('img')[0];
    imgSrcArray[i] = [imgElement.src, i]; // 存储图片src和原始索引
}

// 对图片src数组从左到右从上到下排序
imgSrcArray.sort(function(a, b) {
    if (a[1] === b[1]) { // 如果原始索引相同，按照图片src排序
        return a[0].localeCompare(b[0]);
    } else {
        return a[1] - b[1]; // 按照原始索引排序
    }
});

// 获取imgfd元素的src图片名
let imgfd = document.getElementById('imgfd');
let imgfdSrc = imgfd.src;
let currentImgIndex = imgSrcArray.findIndex(function(item) { // 查找当前图片在排序后的数组中的索引
    return item[0] === imgfdSrc;
});

if (currentImgIndex === imgSrcArray.length - 1) {
    currentImgIndex = 0; // 如果已经是最后一张，切换到第一张
} else {
    currentImgIndex++; // 点击下一张时就按顺序切换
}

imgfd.src = imgSrcArray[currentImgIndex][0]; // 将src赋值给imgfd元素

// 设置 'imgfdb-fk-tu-dang' 元素的文本内容为当前图片的位置
document.getElementById('imgfdb-fk-tu-dang').textContent = currentImgIndex + 1;

imgfdjzpd(); // 调用你的图片切换函数


}
*/



//判断放大的图片加载是否完成函数
/*function isImageLoaded(imagfdeUrl) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(true);
    img.onerror = () => reject(false);
    img.src = imagfdeUrl;
  });
}
//图片加载完成状态判断函数
function imgfdjzpd(){
    //图片加载完成状态判断
    //先需要判断是否已经存在定时器,如果存在则需要先关闭,因为如果存在就是上一次没关闭的
    if(typeof(imgfdtime) == "undefined" || imgfdtime == null){
        //console.log("不存在定时器")
    }else{
        //console.log("存在定时器")
        clearInterval(imgfdtime);//关闭定时器
        //console.log("以关闭上次的定时器")
    }
    //clearInterval(imgfdtime);//关闭定时器
    document.getElementById("imgfd").style.display="none";//先隐藏大图 由于有懒加载所有第一次查看大图会加载2张图,有一张是懒加载加载的,不影响
    document.getElementById("imgfdloading").style.display="block";//加载前先显示加载动画
    imgfdtime=setInterval(function(){//开一个定时器,每50微妙检测图片加载是否完成
        var imgfdu=document.getElementById('imgfd').src;
        // 使用示例
        const imagfdeUrl = imgfdu;
        isImageLoaded(imagfdeUrl)
          .then(() => {
            //console.log("图片已加载完成");
            document.getElementById("imgfdloading").style.display="none";//加载完成隐藏加载动画
            document.getElementById("imgfd").style.display="block";//显示大图
            clearInterval(imgfdtime);//关闭定时器
            return;
          })
          .catch(() => {
            //console.log("图片加载失败");
            clearInterval(imgfdtime);//关闭定时器
            return;
          });
    },50);
}
*/
//图片放大事件
/*function imgfd(){
    //调用图片加载动画以及判断是否加载完成的函数
    imgfdjzpd();

    var ele = window.event.srcElement.id;//获取图片id
    var lan = window.event.srcElement.lang;//获取当前图片所属文章id
    var tuele = ele.replace("imglib-tuid-"+lan+"-","");//获取当前位置数纯id
    var img = window.event.srcElement.src;//获取点击的图片链接

    document.getElementById("imgfd").src=img;//设置大图
    var userbtu = document.querySelector("#imgfd");
    userbtu.setAttribute("data-src",img);
    document.getElementById("imgfdb").style.display="flex";//显示大图容器

    lis = document.getElementById('imglib-'+lan).getElementsByTagName('div').length;//获取该文章共有多少张图片
    document.getElementById("imgfdb-fk-tu-zs").innerText=lis;//设置总数显示

    document.getElementById("imgfdb-fk-tu-dang").innerText=parseInt(tuele)+1;//设置当前数显示
    document.getElementById("imgfd-sy").lang=lan;
    document.getElementById("imgfd-xy").lang=lan;

    document.body.style.overflow = "hidden";//隐藏滚动条
    tuodongimg();
}

//图片大图容器关闭事件
function imgfdg(){
    event.stopPropagation();//禁止冒泡
    document.getElementById("imgfdb").style.display="none";//关闭大图容器
    document.body.style.overflow = "auto";//显示滚动条
    document.getElementById("imgfd").style= "";//设置放大图片初始大小
    currentScale = 1; // 恢复当前缩放比例
}
*/
//图片放大区域函数集↑




//视频放大事件
function videofd(){
    event.stopPropagation();

    var ele = window.event.srcElement.id;//获取点击的视频id
    var vbzt = document.getElementById(ele);
    var eleg=ele.replace("sh-content-videok-","");

    //视频已处于放大预览
    var eleid1=ele.replace("sh-content-videok-","sh-content-video-");
    if (document.getElementById(eleid1).lang == 1) {
        return;
    }

    //判断视频是否有值切有效
    var videoElement = document.getElementById(ele);
    var srcValue = videoElement.getAttribute("src");
    if (srcValue && srcValue.trim() !== "") {
      // 值存在且有效
    } else {
      // 值不存在或无效
      warnpop("视频源无效");
      return;
    }


    if (vbzt.paused) {
       vbzt.play();
       //隐藏播放三角标识
       var newStr = ele.replace("sh-content-videok-", "");
       if (document.getElementById("sh-content-video-videobfb-"+newStr)) {
           document.getElementById("sh-content-video-videobfb-"+newStr).style.display="none";
       }
    }else{
        var eleid=ele.replace("sh-content-videok-","sh-content-video-");
    if (window.event.srcElement.lang != 0) {
        //恢复原始样式
        var wbvidek=document.getElementById(eleid).className;
        var wbvth=wbvidek.replace(" videofdb","");
        document.getElementById(eleid).className = wbvth;//设置外部的class还原
        document.getElementById(eleid).style = "";//设置外部的style还原
        document.getElementById("sh-content-videog-"+eleg).style="display:none";//隐藏右上角关闭
        document.getElementById(eleid).lang = "0";//设置外部的style还原
        document.getElementById(ele).lang = "0";//设置外部的style还原
        var videoum = document.getElementById(ele);//获取视频
        videoum.muted = true;//设置静音
        videoum.volume=0;//设置音量

        document.getElementById("sh-menu").style.zIndex = 1;
        document.getElementById("musicbc").style.zIndex = 99;

        //隐藏进度条
        document.getElementById(ele).controls = false;
        setTimeout(function(){
            document.getElementById(ele).play();
        },200);

    }else{
        //放大预览
        document.getElementById(eleid).className += ' videofdb';//设置外部的class放大
        document.getElementById(eleid).style="display: flex;";//设置外部的style显示
        document.getElementById("sh-content-videog-"+eleg).style="display:flex";//显示右上角关闭
        document.getElementById(eleid).lang = "1";//设置外部的style还原
        document.getElementById(ele).lang = "1";//设置外部的style还原
        var videoum = document.getElementById(ele);//获取视频
        videoum.muted = false;//取消静音
        videoum.volume=0.5;//设置音量
        videoum.play();//播放视频

        document.getElementById("sh-menu").style.zIndex = 0;
        document.getElementById("musicbc").style.zIndex = 0;

        document.body.style.overflow = "hidden";//隐藏滚动条

        var elementy = document.getElementById("sh-content-videok-"+eleg); // 获取id为123的元素
        if (elementy) { // 判断元素是否存在
            var dataYbs = elementy.getAttribute('data-ybs'); // 获取自定义属性data-ybs
            //console.log(dataYbs)
            if (dataYbs === '1') { // 判断data-ybs是否等于1
                document.getElementById("sh-content-videok-"+eleg).setAttribute('style', 'max-width: 90%;max-height: 90%;border-radius: 8px;'); // 添加style属性
            }
        }

        //显示进度条
        document.getElementById(ele).controls = true;
        //不要画中画:disablePictureInPicture="true"
        document.getElementById(ele).setAttribute("disablePictureInPicture", "true");
        //nodownload:不要下载、nofullscreen:不要全屏、noremoteplayback:不要远程回放、noplaybackrate:不要播放速度
        document.getElementById(ele).setAttribute("controlslist", "nodownload noremoteplayback noplaybackrate");
        setTimeout(function(){
            document.getElementById(ele).play();
        },200);
    }

    //隐藏标识
    var newStr = ele.replace("sh-content-videok-", "");
    if(document.getElementById("sh-video-span-"+newStr).style.display == "none"){
        document.getElementById("sh-video-span-"+newStr).style.display="block";
    }else{
        document.getElementById("sh-video-span-"+newStr).style.display="none";
    }
    //隐藏播放三角标识
    if (document.getElementById("sh-content-video-videobfb-"+newStr)) {
        document.getElementById("sh-content-video-videobfb-"+newStr).style.display="none";
    }


    }


}
//视频放大关闭事件
function videofdgb(){
    event.stopPropagation();//禁止冒泡

    var ele = window.event.srcElement.id;//获取点击的视频id
    var ele = ele.replace("sh-content-videog-","sh-content-video-");
    var eleid=ele.replace("sh-content-video-","sh-content-videok-");
    var ele2=ele.replace("sh-content-video-","");

    //判断视频是否有值切有效
    var videoElement = document.getElementById(eleid);
    var srcValue = videoElement.getAttribute("src");
    if (srcValue && srcValue.trim() !== "") {
      // 值存在且有效
      var videosrcx="1";
    } else {
      // 值不存在或无效
      var videosrcx="0";
    }

    var elementy = document.getElementById("sh-content-videok-"+ele2); // 获取id为123的元素
        if (elementy) { // 判断元素是否存在
            var dataYbs = elementy.getAttribute('data-ybs'); // 获取自定义属性data-ybs
            //console.log(dataYbs)
            if (dataYbs === '1') { // 判断data-ybs是否等于1
                document.getElementById("sh-content-videok-"+ele2).setAttribute('style', ''); // 添加style属性
            }
        }

    var wbvidek=document.getElementById(ele).className;
    var wbvth=wbvidek.replace(" videofdb","");
    document.getElementById(ele).className = wbvth;//设置外部的class还原
    document.getElementById(ele).style= "";//设置外部的style还原
    document.getElementById("sh-content-videog-"+ele2).style="display:none";//隐藏右上角关闭
    document.getElementById(eleid).lang = "0";//设置外部的style还原
    document.getElementById(ele).lang = "0";//设置外部的style还原
    var videoum = document.getElementById(eleid);//获取视频
    videoum.muted = true;//设置静音
    videoum.volume=0;//设置音量

    //隐藏标识
    var newStr = ele.replace("sh-content-video-", "");
    if(document.getElementById("sh-video-span-"+newStr).style.display == "none"){
        document.getElementById("sh-video-span-"+newStr).style.display="block";
        //隐藏进度条
        document.getElementById(eleid).controls = false;
        setTimeout(function(){
            if (videosrcx == 1) {
                document.getElementById(eleid).play();
            }
        },200);
    }else{
        document.getElementById("sh-video-span-"+newStr).style.display="none";
        //显示进度条
        document.getElementById(eleid).controls = true;
        setTimeout(function(){
           if (videosrcx == 1) {
                document.getElementById(eleid).play();
            }
        },200);
    }
    document.body.style.overflow = "auto";//显示滚动条
}














/* 首页顶部音乐播放控制 */
function syaudbf(){

    var bfkztu="sh-main-top-mu";//获得播放控制按钮
    var bfz=document.getElementById(bfkztu).lang;//获得播放状态
    //console.log(bfz);

    var audio = document.getElementById("sh-main-top-musicplay-b");//取到音乐播放器


    if (bfz == 0) {
        //没有播放则开始播放音乐
        document.getElementById("musicplay").pause();//暂停独立音乐播放器

        audio.play();//播放音乐
        document.getElementById(bfkztu).lang="1";//更新播放状态为播放中
        document.getElementById("sh-main-top-mucisjd").style.display="block";//显示音乐播放动画
        document.getElementById("sh-main-top-g-m").style="background: rgb(255,255,255,0);";//去除上级父元素背景

        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
        //console.log(tul)
        if (tul == "bb") {
            document.getElementById("sh-main-top-mu").className="iconfont icon-iconstop ri-z-sx";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbz");
        }else if(tul == "bbh"){
            document.getElementById("sh-main-top-mu").className="iconfont icon-iconstop ri-z-sxh";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","zbbh");
        }

            //开一个定时器,每500毫秒检测音乐播放状态
            timer2=setInterval(function(){
            if(audio.paused){
                // 当音乐没有播放时则关闭定时器
                //--------console.log("关闭定时器");
                //console.log("未播放");

                document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
                document.getElementById("sh-main-top-mucisjd").style.display="none";//隐藏音乐播放动画
                document.getElementById("sh-main-top-g-m").style="background: rgb(215 215 215 / 75%);";//恢复上级父元素背景
                var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
                //console.log(tul)
                if (tul == "bbz") {
                    document.getElementById("sh-main-top-mu").className = "iconfont icon-jixu ri-z-sx"
                    document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bb");
                }else if(tul == "zbbh"){
                    document.getElementById("sh-main-top-mu").className = "iconfont icon-bofang-tongyong-copy ri-z-sxh"
                    document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbh");
                }
                clearInterval(timer2);//关闭定时器
                return;

            }else{
                //console.log("在播放");
            }

            },500);
    }else{
        //在播放则暂停播放
        audio.pause();//暂停播放音乐
        clearInterval(timer2);//关闭定时器
        document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
        document.getElementById("sh-main-top-mucisjd").style.display="none";//隐藏音乐播放动画
        document.getElementById("sh-main-top-g-m").style="background: rgb(215 215 215 / 75%);";//恢复上级父元素背景
        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
        //console.log(tul)
        if (tul == "bbz") {
            document.getElementById("sh-main-top-mu").className = "iconfont icon-jixu ri-z-sx"
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bb");
        }else if(tul == "zbbh"){
            document.getElementById("sh-main-top-mu").className = "iconfont icon-bofang-tongyong-copy ri-z-sxh"
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbh");
        }

    }




}




//首页顶部音乐随机获取
function sjsyyy(){
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/symusic.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('q=1');
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            //alert(xhr.responseText);
            if (xhr.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            //console.log(xhr.responseText)
            var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
            var code =obj[0].code;//获取第一个的值 状态码
            var mum=obj[0].mum;//获取返回歌名
            var muurl=obj[0].muurl;//获取返回歌链接
            if (code == 200) {
                //获取到音乐

                bfkztu="sh-main-top-mu";//获得播放控制按钮
                var audio = document.getElementById("sh-main-top-musicplay-b");//取到音乐播放器
                audio.pause();//暂停播放音乐
                document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
                document.getElementById("sh-main-top-mucisjd").style.display="none";//隐藏音乐播放动画
                document.getElementById("sh-main-top-g-m").style="background: rgb(215 215 215 / 75%);";//恢复上级父元素背景
                var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
                //console.log(tul)
                if (tul == "bbz") {
                    document.getElementById("sh-main-top-mu").className = "iconfont icon-jixu ri-z-sx"
                    document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bb");
                }else if(tul == "zbbh"){
                    document.getElementById("sh-main-top-mu").className = "iconfont icon-bofang-tongyong-copy ri-z-sxh"
                    document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbh");
                }



                document.getElementById("sh-main-top-g-m").lang = mum;//设置新歌名
                document.getElementById("sh-main-top-musicplay-b").src = muurl;//设置新音乐

        //歌曲切换完成后继续开始播放↓
        audio.play();//播放音乐
        document.getElementById(bfkztu).lang="1";//更新播放状态为播放中
        document.getElementById("sh-main-top-mucisjd").style.display="block";//显示音乐播放动画
        document.getElementById("sh-main-top-g-m").style="background: rgb(255,255,255,0);";//去除上级父元素背景

        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
        //console.log(tul)
        if (tul == "bb") {
            document.getElementById("sh-main-top-mu").className="iconfont icon-iconstop ri-z-sx";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbz");
        }else if(tul == "bbh"){
            document.getElementById("sh-main-top-mu").className="iconfont icon-iconstop ri-z-sxh";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","zbbh");
        }

            //开一个定时器,每500毫秒检测音乐播放状态
            timer2=setInterval(function(){
            if(audio.paused){
                // 当音乐没有播放时则关闭定时器
                //--------console.log("关闭定时器");
                //console.log("未播放");

                document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
                document.getElementById("sh-main-top-mucisjd").style.display="none";//隐藏音乐播放动画
                document.getElementById("sh-main-top-g-m").style="background: rgb(215 215 215 / 75%);";//恢复上级父元素背景
                var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");//获取当前的图标
                //console.log(tul)
                if (tul == "bbz") {
                    document.getElementById("sh-main-top-mu").className = "iconfont icon-jixu ri-z-sx"
                    document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bb");
                }else if(tul == "zbbh"){
                    document.getElementById("sh-main-top-mu").className = "iconfont icon-bofang-tongyong-copy ri-z-sxh"
                    document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbh");
                }
                clearInterval(timer2);//关闭定时器
                return;

            }else{
                //console.log("在播放");
            }

            },500);
            //歌曲切换完成后继续开始播放↑


                //获取到音乐
            }else{errorpop("获取歌曲失败");}
        }
    };
}




document.oncontextmenu=new Function("event.returnValue=false"); //禁止右键




//设置评论框高度自适应
function autoResizeTextarea(element) {
  element.style.height = 'auto';
  element.style.height = `${element.scrollHeight}px`;
}
// 获取Textarea元素
var textarea = document.getElementById('bletext');
// 监听输入事件，当内容发生变化时调用自动调整函数
textarea.addEventListener('input', function() {
    autoResizeTextarea(this);
});
//评论框内容改变监听
function myjtbl(){
    if (document.getElementById("bletext").value != "") {

        document.getElementById('sh-pinglun').style = "box-shadow: inset 0px 0px 0px 1px #07c160;";//修改边框为绿色
        document.getElementById('sh-pinglun-fs-right-fs').style = "background: var(--theme);color:#ffffff"//修改发送按钮背景颜色为绿色字体白色
    }else{
        document.getElementById('sh-pinglun').style = "box-shadow: inset 0px 0px 0px 0px #07c160";//恢复边框为灰色
        document.getElementById('sh-pinglun-fs-right-fs').style = "background: var(--backbg);color:#576b95"//修改发送按钮背景颜色为绿色字体白色
    }
}






//全文按钮事件
function quanwenan(){
    var ele = window.event.srcElement.id;
    var elelang=window.event.srcElement.lang;
    var quanwid=ele.replace("sh-content-quanwenan-","");

    if(elelang == 0){
        var dqdcla = document.getElementById("sh-content-qwdid-"+quanwid).className;//获取当前class
        var re = new RegExp("wzndhycyc","g"); //定义正则表达式
        var Newdqdcla = dqdcla.replace(re, ""); //替换指定class
        document.getElementById("sh-content-qwdid-"+quanwid).className=Newdqdcla;
        document.getElementById("sh-content-quanwenan-"+quanwid).lang=1;
        document.getElementById("sh-content-quanwenan-"+quanwid).innerText="收起";
    }else if(elelang == 1){
        document.getElementById("sh-content-qwdid-"+quanwid).className +="wzndhycyc";
        document.getElementById("sh-content-quanwenan-"+quanwid).lang=0;
        document.getElementById("sh-content-quanwenan-"+quanwid).innerText="全文";
    }

}





//音乐悬浮窗拖动
if (document.getElementById('musicbc').lang == 1) {
    //经典悬浮音乐播放器拖动
var draggable = document.getElementById('yszt');
var draggable2 = document.getElementById('musicbc');
//var button = document.getElementById('musiccz-btn');
var isDragging = false;
var offset = { x: 0, y: 0 };

// 监听鼠标按下或触摸开始事件
draggable.addEventListener('mousedown', startDragging);
draggable.addEventListener('touchstart', startDragging);

// 监听鼠标移动或触摸移动事件
document.addEventListener('mousemove', drag);
document.addEventListener('touchmove', drag);

// 监听鼠标释放或触摸结束事件
document.addEventListener('mouseup', stopDragging);
document.addEventListener('touchend', stopDragging);

// 子元素的点击事件不受拖动影响
// button.addEventListener('click', function(e) {
//    e.stopPropagation();
//});

function startDragging(e) {
    e.preventDefault();
    isDragging = true;

    // 计算鼠标或触摸点在div内的偏移量
    var rect = draggable.getBoundingClientRect();
    var clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
    var clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
    offset.x = clientX - rect.left;
    offset.y = clientY - rect.top;

    // 将div置于最上层
    //draggable.style.zIndex = 9999;
}

function drag(e) {
    if (!isDragging) return;

    // 计算div的新位置
    var clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    var clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
    var x = clientX - offset.x;
    var y = clientY - offset.y;

    // 获取屏幕宽度和高度
    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

    // 限制div不能拖出屏幕外
    x = Math.max(0, Math.min(x, screenWidth - draggable2.offsetWidth));
    y = Math.max(0, Math.min(y, screenHeight - draggable2.offsetHeight));

    // 更新div的位置
    //draggable2.style.left = x + 'px';
    draggable2.style.top = y + 'px';
}

function stopDragging() {
    isDragging = false;
}
}











//阻止 Safari 双击放大
/*document.addEventListener('gesturestart', function(e) {
  e.preventDefault();
});
//禁止双指放大
document.addEventListener('touchstart', function(event) {
  if (event.touches.length > 1) {
    event.preventDefault();
  }
}, { passive: false });
// 禁用双击放大
var lastTouchEnd = 0;
document.documentElement.addEventListener('touchend', function (event) {
    var now = Date.now();
    if (now - lastTouchEnd <= 300) {
        event.preventDefault();
    }
    lastTouchEnd = now;
}, {
    passive: false
});*/





//日夜模式切换
if (document.getElementById('day')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('day').addEventListener('click', function() {
    var day=document.getElementById("day").lang;
    var body = document.querySelector('body');
    if (day == 1) {
        // 切换到夜晚模式
        body.classList.toggle('dark-theme');
        document.getElementById("day").lang="0";
        document.getElementById("day-i").className="iconfont icon-yueliang";
        document.cookie = "dark_theme=dark-theme";

    }else if (day == 0){
        // 切换到白天模式
        body.classList.toggle('dark-theme');
        document.getElementById("day").lang="1";
        document.getElementById("day-i").className="iconfont icon-ai250";
        document.cookie = "dark_theme=root";
    }
  });
}



//回到顶部
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}





//开启搜索
function kqso(){
    if (document.getElementById("so").style.display != "flex") {
        document.getElementById("so").style.display="flex";
        document.querySelector('.sobd-in').focus();
    }else{
        document.getElementById("so").style.display="none";
    }

}
//关闭搜索
function gbso(){
    document.getElementById("so").style.display="none";
}

//微信打开隐藏
/*function isWechatOpen() {
  var ua = navigator.userAgent.toLowerCase();
  return (ua.indexOf('micromessenger') !== -1);
}
if (isWechatOpen()) {
  var mainHeadTop = document.getElementById('sh-main-head-top');
  mainHeadTop.parentNode.removeChild(mainHeadTop);
} else {
  //console.log('不在微信内打开');
}*/


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

//图片毛玻璃加载特效===================================
/*for (var i = 0; i < images.length; i++) {
    //console.log(images[i])
    //console.log(images[i].src)
    var dqhimg=images[i].src
    if (dqhimg.indexOf("/assets/img/thumbnail.svg") != -1) {
        // 如果为空，说明图片还未到可视区域
    }else{
        if (images[i].className == "mimg") {
            //排除音乐播放器的背景
        }else if(images[i].id == "imgfd"){
            //排除放大的图片
        }else{
            images[i].style.animation = "myfirsjsimgtm 0.5s";
        }
    }
}*/
//图片毛玻璃加载特效===================================

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
