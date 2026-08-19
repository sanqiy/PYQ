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


          if (document.getElementById("top-right-2")) {//判断消息按钮是否存在
              document.getElementById("top-right-2").className = "iconfont icon-a31shezhi al-sxbh";
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
              document.getElementById("top-left-1").className = "iconfont icon-weibiaoti al-sxb";
          }


          if (document.getElementById("top-right-2")) {//判断消息按钮是否存在
              document.getElementById("top-right-2").className = "iconfont iconfont icon-a31shezhi al-sxb";
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
    //videozb();
}



  /* 音乐播放控制 */
  function audbf(){
      var ele = window.event.srcElement.lang;//获取点击的id
      var name="musicurl-"+ele;//合成待取音乐播放地址
      var nam=document.getElementById(name).lang;//取音乐播放地址
      var naf=document.getElementById(name).className;//取音乐封面

      var bfkztu="sh-aud-left-plak-"+ele;//获得文章播放控制按钮
      var bfz=document.getElementById("musicplay").lang;//获得独立播放器播放状态
      //console.log(bfz);


      //document.getElementById("musicplay").scr=""
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
      //console.log(bfz);


      //document.getElementById("musicplay").scr=""
      var audio = document.getElementById("musicplay");//取到独立音乐播放器


      if (bfz == 0) {//0为未播放
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
      //console.log(bfz);


      document.getElementById("musicplay").scr=""
      var audio = document.getElementById("musicplay");//取到独立音乐播放器


          audio.pause();//暂停播放音乐
          clearInterval(timer);//关闭定时器
          document.getElementById(bfkztu).lang="0";//更新播放状态为未播放
          document.getElementById(bfkztu).className="iconfont icon-jixu";//暂停播放后设置播放按钮图片为开始的图片
          document.getElementById("sh-musiccz-zb").className="iconfont icon-jixu ri-z-sx"//开始播放设置独立音乐播放器为开始标
          document.getElementById("musicplay").lang ="0"//将播放器状态设为未播放
          //document.getElementById("musicplay").className =""
          document.getElementById("sh-aud-left-plak-"+document.getElementById("musicplay").className).className="iconfont icon-jixu"
          document.getElementById("musicplay").className =""
          //document.getElementById("musicbc").style.right="-800px";
          if(document.getElementById("musicbc").lang == 1){
              document.getElementById("musicbc").style.right="-200px";
          }else{
              document.getElementById("musicbc").style.right="-800px";
          }


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











//删除重复年月日
function wzcsql(){
    //删除重复年
var timedElements = document.getElementsByClassName('sh-homecontent-timed');
var timedElementsArray = Array.from(timedElements);

timedElementsArray.forEach((element, index) => {
  if (index > 0 && element.innerHTML === timedElementsArray[index - 1].innerHTML) {
    element.remove();
  }
});

//隐藏重复日期
var elements = document.getElementsByClassName('sh-homecontent-left-time');
var langValues = [];
var duplicateIndexes = [];

for (var i = 0; i < elements.length; i++) {
  var lang = elements[i].lang;

  if (langValues.includes(lang)) {
    duplicateIndexes.push(i);
  } else {
    langValues.push(lang);
  }
}
for (var j = 0; j < elements.length; j++) {
  if (duplicateIndexes.includes(j)) {
    elements[j].style.display = 'none';
    elements[j].lang = '';
  }
}


//设置每个不同月日的顶部间距
// 获取所有class为sh-homecontent-left-time的元素
var elements = document.querySelectorAll('.sh-homecontent-left-time');
// 遍历元素
for (var i = 0; i < elements.length; i++) {
  // 判断元素是否有display: none;属性
  if (window.getComputedStyle(elements[i]).display !== 'none') {
    // 给父元素的父元素添加属性 margin-top: 20px;
    elements[i].parentNode.parentNode.style.marginTop = '25px';
  }
}

//恢复时间颜色
document.querySelectorAll(".homecontent-left-time-h, .homecontent-left-time-y").forEach(function(element) {
    element.style.color = "var(--textqh)";
});

}

//获取更多的文章
function hqgd(){
      if (document.getElementById("footer-text-zt").innerText == "- 没有更多啦 -") {
          return;
      }
      if (document.getElementById("footer-text-zt").innerText == "正在加载..") {//防止触底加载多次触发
          return;
      }
      var page=String(document.getElementById("footer-text-hqgd").innerText);//获取当前的文章数量

      var urlParams = new URLSearchParams(window.location.search);
      var getuser = urlParams.get('user');

      if (getuser == "" || getuser == null) {
          var getuser="";
      }

      //loadpop("正在加载...","ok");
      //显示加载中动画
      document.getElementById("footer-text-zt").style.animation = "colorChange 0.8s infinite";
      document.getElementById("footer-text-zt").innerText="正在加载..";


      // 异步对象
      var xhr = new XMLHttpRequest();
      // 设置属性
      xhr.open('post', './api/homeapi.php');
      // 如果想要使用post提交数据,必须添加此行
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      // 将数据通过send方法传递
      xhr.send('page='+page+'&getuser='+getuser);
      // 发送并接受返回值
      xhr.onreadystatechange = function () {
          // 这步为判断服务器是否正确响应
          if (xhr.readyState == 4 && xhr.status == 200) {
              //console.log(xhr.responseText);
              //清除加载中动画
              document.getElementById("footer-text-zt").removeAttribute("style");
              if (xhr.responseText == "") {
                  errorpop("未获取到数据");
                  document.getElementById("footer-text-zt").innerText="加载更多..";
                  return;
              }
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
              wzcsql();//调用前端文章整理函数,去除重复年,月日
              document.getElementById("footer-text-zt").innerText="加载更多..";
              loaddemand();//调用一次懒加载



              //数据追加完成后获取页面一共有多少条数据 并设置它
              //var res=$(".sh-content").length;
              //alert(res)
              document.getElementById("footer-text-hqgd").innerText=str;
          }else{document.getElementById("footer-text-zt").removeAttribute("style");}
      };


  }








  //禁止冒泡
  function evjz(){
      event.stopPropagation();//禁止冒泡
  }

  //个人主页背景修改 预览选中的图片
  if (document.getElementById("sh-resces-gl")) {
      document.querySelector('#files').onchange = function (){
        if(this.files.length){
          let file = this.files[0];
          let reader = new FileReader();
          //新建 FileReader 对象
          reader.onload = function(){
            // 当 FileReader 读取文件时候，读取的结果会放在 FileReader.result 属性中
            document.querySelector('#sh-resces-gl-z-formup-img').src = this.result;

            var userbtu = document.querySelector("#sh-resces-gl-z-formup-img");
            userbtu.setAttribute("data-src", this.result);

            document.getElementById("sh-resces-gl-z-formup-img").style.display="flex";
            document.getElementById("files").style.display="none";
            document.getElementById("sh-resces-gl-z-upload-btn").style.display="none";
          };
          // 设置以什么方式读取文件，这里以base64方式
          reader.readAsDataURL(file);
         }
      }
  }



  function sctfm(){
      var fiewj=document.getElementById("files").value;
      if (fiewj == "" || fiewj == null) {
          warnpop("请选择文件");
          return;
      }
      loadpop("正在上传封面，请稍后...","ok");
  }



  function recgb(){
      document.getElementById("sh-resces-gl").style="display: none";//隐藏上个人主页传背景图板块
      document.getElementById("sh-resces-gl-z-formup-img").src="";//删除图片预览

      var userbtu = document.querySelector("#sh-resces-gl-z-formup-img");
      userbtu.setAttribute("data-src","");

      document.getElementById("sh-resces-gl-z-formup-img").style.display="none";//删除图片预览
      document.getElementById("files").value=null;//删除图片文件

      document.getElementById("files").style.display="flex";//显示上传
      document.getElementById("sh-resces-gl-z-upload-btn").style.display="flex";//显示上传图标
  }
  function reckq(){
      document.getElementById("sh-resces-gl").style="display: flex";//开启上个人主页传背景图板块
  }







document.oncontextmenu=new Function("event.returnValue=false"); //禁止右键







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





//上传封面图
function dosubmit(){
        event.preventDefault(); // 阻止表单的默认提交行为
// 获取表单元素
var form = document.getElementById('myFormbg');
// 创建XMLHttpRequest对象
var xhr = new XMLHttpRequest();
// 设置请求处理程序
xhr.onreadystatechange = function() {
  if (xhr.readyState == 4 && xhr.status == 200) {
    // 如果请求成功并且状态为200，则处理服务器返回的数据
    var response = xhr.responseText;
    // 在这里处理服务器返回的数据
    if (response == "") {
        errorpop("未获取到数据");
        return false;
    }
    if (response == "修改封面成功") {
        successpop(response);
        // 获取id为'sh-resces-gl-z-formup-img'的img元素的src
        var imgSrc = document.getElementById('sh-resces-gl-z-formup-img').src;
        // 获取class为'sh-main-head-img'的元素
        var mainHeadImg = document.getElementsByClassName('sh-main-head-img')[0];
        // 设置background-image
        mainHeadImg.style.backgroundImage = 'url(' + imgSrc + ')';
        recgb();
    }else{
        warnpop(response);
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
