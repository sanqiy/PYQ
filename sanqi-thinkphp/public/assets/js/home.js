/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
window.onscroll = function () {
    Sanqi.scrollHeader({
        activeClasses: {
            'top-left-1': 'iconfont icon-weibiaoti al-sxbh',
            'top-right-2': 'iconfont icon-a31shezhi al-sxbh'
        },
        normalClasses: {
            'top-left-1': 'iconfont icon-weibiaoti al-sxb',
            'top-right-2': 'iconfont iconfont icon-a31shezhi al-sxb'
        }
    });

    if (Sanqi.isScrollAtBottom()) {
        hqgd();
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
      var footerZt = document.getElementById("footer-text-zt");
      var footerHqgd = document.getElementById("footer-text-hqgd");
      if (!footerZt || !footerHqgd) {
          return;
      }
      if (footerZt.innerText == "- 没有更多啦 -") {
          return;
      }
      if (footerZt.innerText == "正在加载..") {//防止触底加载多次触发
          return;
      }
      if ((parseInt(footerHqgd.innerText, 10) || 0) <= 0) {
          footerHqgd.innerText = document.querySelectorAll("#sh-nrbk .sh-homecontent-lie").length;
      }
      var lastId = footerHqgd.getAttribute('data-last-id') || '';
      if (!lastId) {
          var lastPost = document.querySelector("#sh-nrbk .sh-homecontent-lie:last-child");
          lastId = lastPost ? (lastPost.getAttribute('data-post-id') || '') : '';
      }
      var page=String(footerHqgd.innerText);//旧接口兼容字段，实际优先使用last_id cursor

      var urlParams = new URLSearchParams(window.location.search);
      var timelineUser = document.getElementById("timeline-user");
      var getuser = timelineUser ? timelineUser.value : urlParams.get('user');

      if (getuser == "" || getuser == null) {
          var getuser="";
      }

      //loadpop("正在加载...","ok");
      //显示加载中动画
      footerZt.style.animation = "colorChange 0.8s infinite";
      footerZt.innerText="正在加载..";


      Sanqi.apiPost('/api/home/load-more', {
          last_id: lastId, page: page, getuser: getuser
      }, function(res){
          footerZt.removeAttribute("style");
          if (!res) { errorpop("未获取到数据"); footerZt.innerText="加载更多.."; return; }
          var code = res.code || '';
          var data = res.data || {};
          if (code != '200' || !data) { errorpop(res.msg || "加载失败"); footerZt.innerText="加载更多.."; return; }
          var html = data.html || '';
          var hasMore = data.hasMore || false;
          var newOffset = data.offset || 0;
          var nextCursor = data.next_cursor || '';
          if (!html) { footerZt.innerText="- 没有更多啦 -"; return; }
          $("#sh-nrbk").append(html);
          wzcsql();
          footerZt.innerText=hasMore ? "加载更多.." : "- 没有更多啦 -";
          loaddemand();
          if (typeof observeNewVideos === 'function') observeNewVideos();
          footerHqgd.innerText=newOffset;
          if (nextCursor) { footerHqgd.setAttribute('data-last-id', nextCursor); }
      }, function(res){
          footerZt.removeAttribute("style");
          errorpop((res && res.msg) || "加载失败");
          footerZt.innerText="加载更多..";
      });


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







// 页面加载时立即处理日期显示
wzcsql();







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
    event.preventDefault();
    var form = document.getElementById('myFormbg');
    var formData = new FormData(form);
    Sanqi.apiPost(form.action || '/api/user/cover', formData, function(obj){
        if (!obj || !obj.code) { errorpop("未获取到数据"); return; }
        if (obj.code == '200') {
            successpop(obj.msg || "修改封面成功");
            var imgSrc = document.getElementById('sh-resces-gl-z-formup-img').src;
            var mainHeadImg = document.getElementsByClassName('sh-main-head-img')[0];
            mainHeadImg.style.backgroundImage = 'url(' + imgSrc + ')';
            recgb();
        } else {
            warnpop(obj.msg || "上传失败");
        }
    }, function(res){
        warnpop((res && res.msg) || "上传失败，请稍后重试");
    });
}






function loaddemand(){
  Sanqi.lazyLoad();
}

loaddemand();//调用一次懒加载
