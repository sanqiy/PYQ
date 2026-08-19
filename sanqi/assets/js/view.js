/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
//监听屏幕上下滚动
window.onscroll = function () {
    //变量t是滚动条滚动时，距离顶部的距离
    var t = document.documentElement.scrollTop || document.body.scrollTop;
    //当滚动到距离顶部200px时，返回顶部的锚点显示
    if (t >= 230) {
        /* console.log("大于") */
        if (document.getElementById("sh-main-head-top")) {//判断顶部导航栏是否存在
            document.getElementById("sh-main-head-top").style = "background:var(--dbztlys);backdrop-filter: saturate(180%) blur(20px);-webkit-backdrop-filter: saturate(180%) blur(20px)";
        }

        if (document.getElementById("top-left-1")) {//判断登录按钮是否存在
            document.getElementById("top-left-1").className = "iconfont icon-weibiaoti al-sxbh";
        }
        if (document.getElementById("top-right-1")) {//判断登录按钮是否存在
            document.getElementById("top-right-1").className = "iconfont icon-gengduo al-sxbh";
        }

        if (document.getElementById("setup-view-title")) {//判断消息按钮是否存在
            document.getElementById("setup-view-title").style = "color: var(--iconhs);";
        }

        if (document.getElementById("sh-menu")) {
            document.getElementById("sh-menu").style.display="flex";
        }
    } else {//恢复正常
        /* console.log("小于") */
        if (document.getElementById("sh-main-head-top")) {//判断顶部导航栏是否存在
            document.getElementById("sh-main-head-top").style = "background:var(--dbztlysh)";
        }

        if (document.getElementById("top-left-1")) {//判断登录按钮是否存在
            document.getElementById("top-left-1").className = "iconfont icon-weibiaoti al-sxb";
        }

        if (document.getElementById("top-right-1")) {//判断登录按钮是否存在
            document.getElementById("top-right-1").className = "iconfont icon-gengduo al-sxb";
        }

        if (document.getElementById("setup-view-title")) {//判断消息按钮是否存在
            document.getElementById("setup-view-title").style = "color: var(--iconbs);";
        }

        if (document.getElementById("sh-menu")) {
            document.getElementById("sh-menu").style.display="none";
        }
    }
}


/* 发送评论按钮事件 */
function fasongv() {
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
            warnpop("请先登录");
            return;
        }
        if (vis_name == "" && vis_email == "") {
            //warnpop("请先登录");
            ykkg();
            return;
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
                             document.getElementById("sh-dz-z-"+id).style.display = "flex";
                         }

                         //获取内容
                         var li = '<li lang="'+uunc+'" onclick="plhuifu()" id="'+id+'" value="'+uuzh+'" data-comkzt="0"><div class="sh-zanp-pl-tx"><img src="./assets/img/thumbnail.svg" data-src="'+uutx+'" alt=""></div><div class="sh-zanp-pl-n sh-zanp-pl-n2"><div class="sh-zanp-pl-n-mz"><a href="'+uuwz+'" style="pointer-events: all;" class="sh-zanp-pl-n-nc" onclick="hfljurl()" target="_blank">'+uunc+'</a> <span>· 0秒前</span></div><span class="sh-zanp-pl-n-nr">'+pluunr+'</span></div></li>';
                         $("#" + tid).append(li);
                         //关闭评论框

                         // 通过元素的 id 获取到指定的元素
                         /*const element = document.getElementById("sh-content-"+id);
                         // 将页面滚动到指定元素的位置，并设置滚动行为为平滑滚动
                         element.scrollIntoView({
                             behavior: 'smooth'
                         });*/
                        //没有被回复者
                     }else{
                         //有被回复者
                         if (document.getElementById(tid).style.display == "none") {
                             document.getElementById(tid).style.display = "block";
                         }

                         //获取内容
                         var li = '<li lang="'+uunc+'" onclick="plhuifu()" id="'+id+'" value="'+uuzh+'" data-comkzt="0"><div class="sh-zanp-pl-tx"><img src="./assets/img/thumbnail.svg" data-src="'+uutx+'" alt=""></div><div class="sh-zanp-pl-n sh-zanp-pl-n2"><div class="sh-zanp-pl-n-mz"><a href="'+uuwz+'" style="pointer-events: all;" class="sh-zanp-pl-n-nc" onclick="hfljurl()" target="_blank">'+uunc+'</a> <span>· 0秒前</span></div><span>回复</span><span class="sh-zanp-pl-n-nc">'+buunc+'</span>：<span class="sh-zanp-pl-n-nr">'+pluunr+'</span></div></li>';
                         $("#" + tid).append(li);
                         //关闭评论框

                         // 通过元素的 id 获取到指定的元素
                         /*const element = document.getElementById("sh-content-"+id);
                         // 将页面滚动到指定元素的位置，并设置滚动行为为平滑滚动
                         element.scrollIntoView({
                             behavior: 'smooth'
                         });*/
                         //有被回复者
                     }
                     successpop("评论发送成功");
                    loaddemand();//调用一次懒加载
                 //评论成功
             }else{
                 //评论失败
                 //关闭评论框

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







/*点赞按钮事件 */
function dinazanv() {
    var id = document.getElementById("sh-tieid").innerText;//获取点击的帖子id
    var user_id = getCookie("username");//取登录的账号
    var user_passid = getCookie("passid");//取登录的passid唯一id

    if (user_id == "" || user_passid == "") {
        //没有登录账号------------------------------------------------------------------------------
        //warnpop("请先登录");
        //获取点赞span的内容判断是否为点赞
        var dzztl=document.getElementById("tiezdz-"+id).innerText;
        if (dzztl == "赞") {
            //执行点赞

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
            var img=obj[0].img;//获取返回头像
            if (code == 200) {
                //点赞成功

                lis = document.getElementById('sh-zanp-ul').getElementsByTagName('li').length;

                if (document.getElementById("zans-" + id).style.display == "none") {
                    document.getElementById("zans-" + id).style.display = "flex";
                }
                //var li = '<li id="zan-'+user_id+'"><img src="./assets/img/thumbnail.svg" data-src="'+img+'" alt=""></li>';
                //$("#sh-zanp-ul").append(li);
                if (!document.getElementById("fkzan-zan")) {
                    var li = '<li id="fkzan-zan" style="width: fit-content;height: 30px;margin-right: 5px;margin-bottom: 5px;display: flex;align-items: center;"><span style="font-size: 12px;color: var(--adgg);max-width: 100px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;"></span></li>';
                    $("#sh-zanp-ul").append(li);
                }
                var parentElement = document.getElementById("fkzan-zan");
                // 获取该元素内部的span元素
                var spanElement = parentElement.querySelector("span");
                // 设置span元素的文本内容
                spanElement.textContent = msg;

                document.getElementById("tiezdz-"+id).innerText="取消";
                document.getElementById("tiezimg-"+id).className="iconfont icon-aixin2 ri-sxdzlikehs";
                successpop("点赞成功");
                loaddemand();//调用一次懒加载
                //点赞成功
            }else{
                //已经点赞过了
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
            var img=obj[0].img;//获取返回头像
            if (code == 200) {
                //点赞成功

                lis = document.getElementById('sh-zanp-ul').getElementsByTagName('li').length;

                if (document.getElementById("zans-" + id).style.display == "none") {
                    document.getElementById("zans-" + id).style.display = "flex";
                }
                var li = '<li id="zan-'+user_id+'"><img src="./assets/img/thumbnail.svg" data-src="'+img+'" alt=""></li>';

                if(document.getElementById("fkzan-zan")){
                    $("#sh-zanp-ul"+" li").eq(-1).before(li);
                    //$("#sh-zanp-ul"+"li").eq(-1).before(li);
                }else{
                    $("#sh-zanp-ul").append(li);
                }


                document.getElementById("tiezdz-"+id).innerText="取消";
                document.getElementById("tiezimg-"+id).className="iconfont icon-aixin2 ri-sxdzlikehs";
                successpop("点赞成功");
                loaddemand();//调用一次懒加载
                //点赞成功
            }else{
                //已经点赞过了
                warnpop(msg)
                //已经点赞过了
            }
        }
    };

            //执行点赞
        }else{
            //执行取消点赞


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
            var img=obj[0].img;//获取返回头像
            if (code == 200) {
                //删除点赞成功
                //获取现有的点赞列表

                var ml="zan-"+user_id;
                var x = document.getElementById(ml);
                //如果对象x不为空
                if (x != null){
                    x.remove();
                    document.getElementById("tiezdz-"+id).innerText="赞";
                    document.getElementById("tiezimg-"+id).src="./assets/img/like.svg";
                }

                lis = document.getElementById('sh-zanp-ul').getElementsByTagName('li').length;
                if (lis <= 0) {
                    document.getElementById("zans-" + id).style.display = "none";
                }

                document.getElementById("tiezdz-"+id).innerText="赞";
                document.getElementById("tiezimg-"+id).className="iconfont icon-aixin ri-sxdzlike";
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








//删除文章事件
  function delewenz(){
      if (confirm("确定要删除此文章吗?")) {
        // 用户点击了确认按钮
      } else {
        // 用户点击了取消按钮或关闭了弹窗
        return;
      }

      var ele = window.event.srcElement.id;//获取点击的id
      var eles=ele.replace("sh-delewza-","");//去掉id前面的标识符 只留id

      loadpop("正在删除文章,请稍后...","ok");
      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/delewz.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('wzdid='+eles);
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  return;
              }
              if (xhr.responseText == "已删除该文章!") {
                  //删除页面上的对应消息
                  successpop("已删除该文章");
                  //location.href='./home.php'
                  window.history.back(-1);
                  //删除页面上的对应消息
              }else{
                  errorpop(xhr.responseText);
              }
          }
      };
  }




//删除评论
  function pldels(Obj){
      event.stopPropagation();//禁止冒泡

      if (confirm("确定要删除此评论吗?")) {
        // 用户点击了确认按钮
      } else {
        // 用户点击了取消按钮或关闭了弹窗
        return;
      }

      var eleecid = window.event.srcElement.id;//获取点击的id

      var elewidl = window.event.srcElement.lang;//获取当前点击的元素lang
      var liswd=elewidl.replace("yswid-","sh-zanp-pl-");
      var ulmli=liswd;



      loadpop("正在删除评论,请稍后...","ok");
      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/delcomm.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('plid='+eleecid);
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              //alert(xhr.responseText);
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  return;
              }
              if (xhr.responseText == "删除评论成功!") {
                  //删除成功
                  Obj.parentNode.parentNode.removeChild(Obj.parentNode);//同步删除页面元素

                  //判断父元素的父元素ul里面还有多少数据
                  lisew = document.getElementById(liswd).getElementsByTagName('li').length;//获取父元素的父元素里面还有没有li元素 没有则隐藏
                  if (lisew <=0) {
                      document.getElementById(ulmli).style.display="none";//当里面的数量为0或小于0时隐藏评论整个框架
                      document.querySelector(".sh-dz-z").style.display = "none";
                  }
                  //判断父元素的父元素ul里面还有多少数据

                  successpop("已删除该评论");
                  //删除成功
              }else{
                  //删除失败
                  warnpop(xhr.responseText);
                  return;
              }

          }
      };


  }


//打开设置弹窗层
function viewsetk(){
    //显示最外层
    document.getElementById("sh-view-set").style.display="flex";
    //给弹窗菜单设置从下往上出现的动画
    document.getElementById("sh-view-set-wk-con").style.animation = "move_4 0.2s";
}
//关闭设置弹窗层
function viewsetg(){
    event.stopPropagation();//禁止冒泡
    //让弹窗背景遮罩层淡出
    document.getElementById('sh-view-set-wk').style.transition = 'opacity 0.25s';
    document.getElementById('sh-view-set-wk').style.opacity = '0';
    //给给弹窗菜单设置从上往下的退出动画
    document.getElementById("sh-view-set-wk-con").style.animation = "move_4t 0.2s";

        let throttleTimer; // 声明用于节流的定时器变量
        function throttleFunction() {//定时器中要执行的代码
          if (document.getElementById("sh-view-set-wk-con")) { // 如果名为此id的div存在才执行
            //弹窗关闭后恢复所有的变动
            document.getElementById("sh-view-set").style.display = "none";
            document.getElementById('sh-view-set-wk').style.opacity = "";
            document.getElementById("sh-view-set-wk-con").removeAttribute("style");
            document.getElementById("sh-view-set-wk").removeAttribute("style");
          }
        }
        function throttle() {
          clearTimeout(throttleTimer); // 清除上一次的节流定时器
          throttleTimer = setTimeout(throttleFunction, 200); // 创建新的节流定时器
        }
        // 调用throttle函数来触发节流逻辑
        throttle();


}

if (document.getElementById('sh-view-set-wk-con')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('sh-view-set-wk-con').addEventListener('click', function() {
    event.stopPropagation();
  });
}



//文章私密锁定与解除
function wzsdyj(){
      var ele = window.event.srcElement.id;//获取点击的id
      var eles=ele.replace("sh-wzdsdzt-","");
      var eleid=document.getElementById(ele).lang;//获取点击的lang 帖子状态 0不可见 1为可见

      if (eleid == 0) {//判断状态是否等于0 如果是则设为可见
         //当前为不可见  需要设置可见


      loadpop("修改私密中,请稍后...","ok");
      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/homeptpzt.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('ztys='+eles+'&ztwid=0');
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              //alert(xhr.responseText);
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  return;
              }
              if(xhr.responseText == "已解除私密") {
                  document.getElementById(ele).lang="1";
                  document.getElementById(ele).getElementsByTagName('span')[0].innerText = '设为私密';
                  successpop("文章已设为公开");
                  viewsetg();
              }else{
                  warnpop(xhr.responseText);
              }
          }
      };

         //当前为不可见  需要设置可见
      }else if(eleid == 1){
          //当前为可见  需要设置不可见


      loadpop("修改私密中,请稍后...","ok");
      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/homeptpzt.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('ztys='+eles+'&ztwid=1');
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              //alert(xhr.responseText);
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  return;
              }
              if (xhr.responseText == "已设置私密") {
                  document.getElementById(ele).lang="0";
                  document.getElementById(ele).getElementsByTagName('span')[0].innerText = '取消私密';
                  successpop("文章已设为私密");
                  viewsetg();
              }else{
                  warnpop(xhr.responseText);
              }

          }
      };

          //当前为可见  需要设置不可见
      }
  }



//文章置顶与取消
function wzszd(){
      var ele = window.event.srcElement.id;//获取点击的id
      var eles=ele.replace("sh-wzzdyqx-","");
      var eleid=document.getElementById(ele).lang;//获取点击的lang 帖子状态 0不可见 1为可见

      if (eleid == "sw") {//判断状态是否等于0 如果是则设为可见
         //当前为不可见  需要设置可见


      loadpop("正在置顶文章","ok");
      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/sqtopes.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('wid='+eles+'&lx=sw');
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              //alert(xhr.responseText);
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  return;
              }
              if (xhr.responseText == "已设为置顶") {
                  document.getElementById(ele).lang="qx";
                  document.getElementById(ele).getElementsByTagName('span')[0].innerText = '取消置顶';
                  successpop("已设为置顶");
                  viewsetg();

              }else{
                  warnpop(xhr.responseText);
              }
          }
      };

         //当前为不可见  需要设置可见
      }else if(eleid == "qx"){
          //当前为可见  需要设置不可见


      loadpop("正在取消置顶","ok");
      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/sqtopes.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('wid='+eles+'&lx=qx');
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              //alert(xhr.responseText);
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  return;
              }
              if (xhr.responseText == "已取消置顶") {
                  document.getElementById(ele).lang="sw";
                  document.getElementById(ele).getElementsByTagName('span')[0].innerText = '置顶';
                  successpop("已取消置顶");
                  viewsetg();

              }else{
                  warnpop(xhr.responseText);
              }
          }
      };

          //当前为可见  需要设置不可见
      }
  }
