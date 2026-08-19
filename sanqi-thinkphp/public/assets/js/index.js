/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
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

    var t = document.documentElement.scrollTop || document.body.scrollTop;
    if (t >= 230) {
        /* console.log("大于") */
        if (document.getElementById("sh-main-head-top")) {
            document.getElementById("sh-main-head-top").style = "background:var(--dbztlys);backdrop-filter: saturate(180%) blur(20px);-webkit-backdrop-filter: saturate(180%) blur(20px)";
        }

        if (document.getElementById("top-left-1")) {
            document.getElementById("top-left-1").className = "iconfont icon-account-circle-line ri-sxzytxh";
        }
        if (document.getElementById("top-left-xx")) {
            document.getElementById("top-left-xx").className = "iconfont icon-weibiaoti ri-sxh";
        }

        if (document.getElementById("top-right-1")) {
            document.getElementById("top-right-1").className = "iconfont icon-xiaoxitongzhi ri-sxh";
        }

        if (document.getElementById("top-right-2")) {
            document.getElementById("top-right-2").className = "iconfont icon-tongxunlu ri-sxh";
        }

        if (document.getElementById("top-right-3")) {
            document.getElementById("top-right-3").className = "iconfont icon-xiangji1 ri-sxh";
        }


        if (document.getElementById("sh-main-top-mu")) {
        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");
        if (tul == "bb") {
            document.getElementById("sh-main-top-mu").className = "iconfont icon-bofang-tongyong-copy ri-z-sxh";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbh");
        }else if(tul == "bbz"){
            document.getElementById("sh-main-top-mu").className = "iconfont icon-iconstop ri-z-sxh";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","zbbh");
        }
        }

        if (document.getElementById("sh-main-top-mu-bgmq")) {
            document.getElementById("sh-main-top-mu-bgmq").className = "iconfont icon-yinle_2 ri-z-sxh";
        }

        if (document.getElementById("sh-menu")) {
            document.getElementById("sh-menu").classList.add("scrtop-show");
        }

    } else {
        /* console.log("小于") */
        if (document.getElementById("sh-main-head-top")) {
            document.getElementById("sh-main-head-top").style = "background:var(--dbztlysh)";
        }

        if (document.getElementById("top-left-1")) {
            document.getElementById("top-left-1").className = "iconfont icon-account-circle-fill ri-sxzytx";
        }
        if (document.getElementById("top-left-xx")) {
            document.getElementById("top-left-xx").className = "iconfont icon-weibiaoti ri-sx";
        }


        if (document.getElementById("top-right-1")) {
            document.getElementById("top-right-1").className = "iconfont icon-lingdang ri-sx";
        }

        if (document.getElementById("top-right-2")) {
            document.getElementById("top-right-2").className = "iconfont icon-tongxunlu-copy ri-sx";
        }

        if (document.getElementById("top-right-3")) {
            document.getElementById("top-right-3").className = "iconfont icon-xiangji2 ri-sx";
        }


        if (document.getElementById("sh-main-top-mu")) {
        var tul=document.querySelector("#sh-main-top-mu").getAttribute("data-bfzt");
        if (tul == "bbh") {
            document.getElementById("sh-main-top-mu").className = "iconfont icon-jixu ri-z-sx";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bb");
        }else if(tul == "zbbh"){
            document.getElementById("sh-main-top-mu").className = "iconfont icon-iconstop ri-z-sx";
            document.querySelector("#sh-main-top-mu").setAttribute("data-bfzt","bbz");
        }
        }

        if (document.getElementById("sh-main-top-mu-bgmq")) {
            document.getElementById("sh-main-top-mu-bgmq").className = "iconfont icon-yinle_2 ri-z-sx";
        }

        if (document.getElementById("sh-menu")) {
            document.getElementById("sh-menu").classList.remove("scrtop-show");
        }
    }



    if (isScrollAtBottom()) {

      hqgd();
      //return;
    }
}

// Message notification handlers live in notifications.js.

/*function plgdh(){
    var ele = window.event.srcElement.id;//获取点击的id
    //console.log(ele)
    var wyid=ele.replace("plgdxs-","")
    //console.log(wyid)
    var lan =document.getElementById(ele).lang;
    var sljhs=parseInt(lan)+parseInt(10);
    var fjys="sh-zanp-pl-"+wyid;//获取父级元素id
    //console.log(fjys)


    //显示提示信息
    loadpop("评论加载中，请稍后..","ok");
    // 寮傛对象
    var xhr = new XMLHttpRequest();
    // 设置属性        xhr.open('post', '/api/comment/load');
    // 如果想使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

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

            $("#"+fjys).append(li);

            var temp = document.getElementById(fjys);//获取父元素id
            var linum = temp.getElementsByTagName("li").length;
            //console.log("更新后的"+linum)
            if (linum>sljhs) {
                //console.log("大于")
                document.getElementById(ele).lang=linum-1;
            }else{
                document.getElementById(ele).lang=linum;//去掉+1 璇勮正序排列不需要加1
            }

            successpop("已加载更多评论");
            loaddemand();//璋冪敤涓€娆℃噿鍔犺浇
        }
    };
}
*/

/*if (document.getElementById('imgfd')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('imgfd').addEventListener('click', function() {

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
    document.body.style.overflow = "hidden";
    initialY = event.touches[0].clientY - imgElement1.offsetTop;
    dragging = true;
});

document.addEventListener('touchmove', function(event) {
    if (dragging) {
        document.body.style.overflow = "hidden";
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

const imgElement = document.getElementById("imgfd");

const minScale = 0.5;
const maxScale = 5;
let currentScale = 1; // 当前缩放比例

// 监听鼠标滚轮事件
imgElement.addEventListener('wheel', function(event) {
    event.preventDefault();
  if (document.getElementById("imgfdb").style.display=="flex") {

    if (event.deltaY < 0) {
        zoomIn();
    } else {
        zoomOut();
    }
  }
});  */
// 处理手机双指缩放事件
/*imgElement.addEventListener('touchstart', function(event) {
    if (event.touches.length === 2) {
        let startX = event.touches[0].clientX;
        let startY = event.touches[0].clientY;
        let endX = event.touches[1].clientX;
        let endY = event.touches[1].clientY;


        let initialDistance = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
        // 监听触摸结束事件
        imgElement.addEventListener('touchend', function(event) {
            let finalDistance = Math.sqrt(Math.pow(endX - event.changedTouches[0].clientX, 2) + Math.pow(endY - event.changedTouches[0].clientY, 2));
            let scaleChange = finalDistance / initialDistance; // 计算缩放比例变化

            if (scaleChange > 1) {
                zoomIn();
            } else {
                zoomOut();
            }

            imgElement.removeEventListener('touchend', arguments.callee);
        });
    }}
});  */

/*function zoomIn() {
    if (currentScale < maxScale) {
        currentScale += 0.1;
        imgElement.style.transform = `scale(${currentScale})`; // 设置缩放比例
    }
}

function zoomOut() {
    if (currentScale > minScale) {
        currentScale -= 0.1;
        imgElement.style.transform = `scale(${currentScale})`; // 设置缩放比例
    }
}

// 当放大图片时 监控 左右 esc 滚轮 键盘
window.addEventListener('keydown', function(event) {
    if (document.getElementById("imgfdb").style.display=="flex") {

        if (event.keyCode === 37) {
            imgfdqs();
        }

        else if (event.keyCode === 39) {
            imgfdqx();
        }

        else if (event.keyCode === 27) {
            imgfdg();
        }
    }

});

    event.stopPropagation();
    document.getElementById("imgfd").style= "";
    currentScale = 1;
    var ele = document.getElementById("imgfd-sy").lang;

// 获取元素
let container = document.getElementById('imglib-' + ele);
let elements = container.getElementsByClassName('sh-content-right-img-pic');
let imgSrcArray = [];

// 提取图片src锛屽苟瀛樺偍涓轰簩缁存暟缁?
for(let i = 0; i < elements.length; i++) {
    let imgElement = elements[i].getElementsByTagName('img')[0];
    imgSrcArray[i] = [imgElement.src, i]; // 存储图片src鍜屽師濮嬬储寮?
}

// 对图片src数组从左到右从上到下排序
imgSrcArray.sort(function(a, b) {
    if (a[1] === b[1]) {
        return a[0].localeCompare(b[0]);
    } else {
        return a[1] - b[1];
    }
});


let imgfd = document.getElementById('imgfd');
let imgfdSrc = imgfd.src;
let currentImgIndex = imgSrcArray.findIndex(function(item) {
    return item[0] === imgfdSrc;
});

if (currentImgIndex === 0) {
    currentImgIndex = imgSrcArray.length - 1;
} else {
    currentImgIndex--; // 点击上一张时就按顺序切换
}

imgfd.src = imgSrcArray[currentImgIndex][0]; // 将src璧嬪€肩粰imgfd元素


document.getElementById('imgfdb-fk-tu-dang').textContent = currentImgIndex + 1;

imgfdjzpd(); // 调用你的图片切换函数

}

    event.stopPropagation();
    document.getElementById("imgfd").style= "";
    currentScale = 1;
    var ele = document.getElementById("imgfd-sy").lang;

// 获取元素
let container = document.getElementById('imglib-' + ele);
let elements = container.getElementsByClassName('sh-content-right-img-pic');
let imgSrcArray = [];

// 提取图片src锛屽苟瀛樺偍涓轰簩缁存暟缁?
for(let i = 0; i < elements.length; i++) {
    let imgElement = elements[i].getElementsByTagName('img')[0];
    imgSrcArray[i] = [imgElement.src, i]; // 存储图片src鍜屽師濮嬬储寮?
}

// 对图片src数组从左到右从上到下排序
imgSrcArray.sort(function(a, b) {
    if (a[1] === b[1]) {
        return a[0].localeCompare(b[0]);
    } else {
        return a[1] - b[1];
    }
});


let imgfd = document.getElementById('imgfd');
let imgfdSrc = imgfd.src;
let currentImgIndex = imgSrcArray.findIndex(function(item) {
    return item[0] === imgfdSrc;
});

if (currentImgIndex === imgSrcArray.length - 1) {
    currentImgIndex = 0;
} else {
    currentImgIndex++; // 点击下一张时就按顺序切换
}

imgfd.src = imgSrcArray[currentImgIndex][0]; // 将src璧嬪€肩粰imgfd元素


document.getElementById('imgfdb-fk-tu-dang').textContent = currentImgIndex + 1;

imgfdjzpd(); // 调用你的图片切换函数


}
*/

/*function isImageLoaded(imagfdeUrl) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(true);
    img.onerror = () => reject(false);
    img.src = imagfdeUrl;
  });
}

        //console.log("不存在定时器")
    }else{
        //console.log("存在定时器")
        clearInterval(imgfdtime);
    }

    imgfdtime=setInterval(function(){
        // 使用示例
        const imagfdeUrl = imgfdu;
        isImageLoaded(imagfdeUrl)
          .then(() => {

            document.getElementById("imgfdloading").style.display="none";//加载完成隐藏加载动画
            document.getElementById("imgfd").style.display="block";//显示大图
            clearInterval(imgfdtime);
          })
          .catch(() => {
            //console.log("图片加载失败");
            clearInterval(imgfdtime);
          });
    },50);
}
*/

/*function imgfd(){


    var ele = window.event.srcElement.id;//获取图片id
    var lan = window.event.srcElement.lang;
    var tuele = ele.replace("imglib-tuid-"+lan+"-","");//获取当前位置数纯id
    var img = window.event.srcElement.src;
    document.getElementById("imgfd").src=img;//设置大图
    var userbtu = document.querySelector("#imgfd");
    userbtu.setAttribute("data-src",img);
    document.getElementById("imgfdb").style.display="flex";//显示大图容器

    lis = document.getElementById('imglib-'+lan).getElementsByTagName('div').length;//鑾峰彇璇ユ枃绔犲叡鏈夊少张图片
    document.getElementById("imgfdb-fk-tu-zs").innerText=lis;//设置总数显示

    document.getElementById("imgfdb-fk-tu-dang").innerText=parseInt(tuele)+1;
    document.getElementById("imgfd-xy").lang=lan;

    document.body.style.overflow = "hidden";
}

//图片大图容器关闭事件
function imgfdg(){
    event.stopPropagation();//绂佹冒泡
    document.getElementById("imgfdb").style.display="none";
    document.body.style.overflow = "auto";
    currentScale = 1;
}
*/

function videofd(){
    event.stopPropagation();
    var evt = window.event || {};
    var target = evt.target || evt.srcElement || {};
    var ele = target.id;
    var vbzt = document.getElementById(ele);
    var eleg=ele.replace("sh-content-videok-","");

    var eleid1=ele.replace("sh-content-videok-","sh-content-video-");
    if (document.getElementById(eleid1).lang == 1) {
        return;
    }


    var videoElement = document.getElementById(ele);
    var srcValue = videoElement.getAttribute("src");
    if (srcValue && srcValue.trim() !== "") {
      // 值存在且有效
    } else {
      warnpop("视频源无效");
      return;
    }


    if (vbzt.paused) {
       setActiveVideo(vbzt);
       vbzt.play();

       var newStr = ele.replace("sh-content-videok-", "");
       if (document.getElementById("sh-content-video-videobfb-"+newStr)) {
           document.getElementById("sh-content-video-videobfb-"+newStr).style.display="none";
       }
    }else{
        var eleid=ele.replace("sh-content-videok-","sh-content-video-");
    if (target.lang != 0) {

        var wbvidek=document.getElementById(eleid).className;
        var wbvth=wbvidek.replace(" videofdb","");
        document.getElementById(eleid).className = wbvth;//设置外部的class还原
        document.getElementById(eleid).style = "";//设置外部的style还原
        document.getElementById("sh-content-videog-"+eleg).style="display:none";
        document.getElementById(eleid).lang = "0";
        document.getElementById(ele).lang = "0";//设置外部的style还原
        var videoum = document.getElementById(ele);
        videoum.muted = true;
        videoum.volume=0;

        document.getElementById("sh-menu").style.zIndex = 1;
        document.getElementById("musicbc").style.zIndex = 99;

        document.getElementById(ele).controls = false;
        setTimeout(function(){
            document.getElementById(ele).play();
        },200);

    }else{

        document.getElementById(eleid).className += ' videofdb';
        document.getElementById(eleid).style="display: flex;";
        document.getElementById("sh-content-videog-"+eleg).style="display:flex";
        document.getElementById(eleid).lang = "1";
        document.getElementById(ele).lang = "1";//设置外部的style还原
        var videoum = document.getElementById(ele);
        videoum.muted = false;
        videoum.volume=0.5;
        setActiveVideo(videoum);
        videoum.play();

        document.getElementById("sh-menu").style.zIndex = 0;
        document.getElementById("musicbc").style.zIndex = 0;

        document.body.style.overflow = "hidden";
        var elementy = document.getElementById("sh-content-videok-"+eleg); // 获取id涓?23鐨勫厓绱?
        if (elementy) {
            var dataYbs = elementy.getAttribute('data-ybs');
            //console.log(dataYbs)
            if (dataYbs === '1') {
                document.getElementById("sh-content-videok-"+eleg).setAttribute('style', 'max-width: 90%;max-height: 90%;border-radius: 8px;');
            }
        }

        document.getElementById(ele).controls = true;

        document.getElementById(ele).setAttribute("disablePictureInPicture", "true");

        document.getElementById(ele).setAttribute("controlslist", "nodownload noremoteplayback noplaybackrate");
        setTimeout(function(){
            document.getElementById(ele).play();
        },200);
    }


    var newStr = ele.replace("sh-content-videok-", "");
    if(document.getElementById("sh-video-span-"+newStr).style.display == "none"){
        document.getElementById("sh-video-span-"+newStr).style.display="block";
    }else{
        document.getElementById("sh-video-span-"+newStr).style.display="none";
    }

    if (document.getElementById("sh-content-video-videobfb-"+newStr)) {
        document.getElementById("sh-content-video-videobfb-"+newStr).style.display="none";
    }


    }


}

function videofdgb(){
    event.stopPropagation();
    var evt = window.event || {};
    var target = evt.target || evt.srcElement || {};
    var ele = target.id;
    var ele = ele.replace("sh-content-videog-","sh-content-video-");
    var eleid=ele.replace("sh-content-video-","sh-content-videok-");
    var ele2=ele.replace("sh-content-video-","");


    var videoElement = document.getElementById(eleid);
    var srcValue = videoElement.getAttribute("src");
    if (srcValue && srcValue.trim() !== "") {
      // 值存在且有效
      var videosrcx="1";
    } else {
      var videosrcx="0";
    }

    var elementy = document.getElementById("sh-content-videok-"+ele2); // 获取id涓?23鐨勫厓绱?
        if (elementy) {
            var dataYbs = elementy.getAttribute('data-ybs');
            //console.log(dataYbs)
            if (dataYbs === '1') {
                document.getElementById("sh-content-videok-"+ele2).setAttribute('style', '');
            }
        }

    var wbvidek=document.getElementById(ele).className;
    var wbvth=wbvidek.replace(" videofdb","");
    document.getElementById(ele).className = wbvth;//设置外部的class还原
    document.getElementById(ele).style= "";//设置外部的style还原
    document.getElementById("sh-content-videog-"+ele2).style="display:none";
    document.getElementById(eleid).lang = "0";
    document.getElementById(ele).lang = "0";//设置外部的style还原
    var videoum = document.getElementById(eleid);
    videoum.muted = true;
    videoum.volume=0;


    var newStr = ele.replace("sh-content-video-", "");
    if(document.getElementById("sh-video-span-"+newStr).style.display == "none"){
        document.getElementById("sh-video-span-"+newStr).style.display="block";
        document.getElementById(eleid).controls = false;
        setTimeout(function(){
            if (videosrcx == 1) {
                document.getElementById(eleid).play();
            }
        },200);
    }else{
        document.getElementById("sh-video-span-"+newStr).style.display="none";
        document.getElementById(eleid).controls = true;
        setTimeout(function(){
           if (videosrcx == 1) {
                document.getElementById(eleid).play();
            }
        },200);
    }
    document.body.style.overflow = "auto";
}


// 设置评论框高度自适应
function autoResizeTextarea(element) {
  element.style.height = 'auto';
  element.style.height = `${element.scrollHeight}px`;
}
// 获取Textarea元素
var textarea = document.getElementById('bletext');
if(textarea){
  textarea.addEventListener('input', function() {
      autoResizeTextarea(this);
  });
}
function myjtbl(){
    if (document.getElementById("bletext").value != "") {

        document.getElementById('sh-pinglun').style = "box-shadow: inset 0px 0px 0px 1px #07c160;";
        document.getElementById('sh-pinglun-fs-right-fs').style = "background: var(--theme);color:#ffffff";
    }else{
        document.getElementById('sh-pinglun').style = "box-shadow: inset 0px 0px 0px 0px #07c160";
        document.getElementById('sh-pinglun-fs-right-fs').style = "background: var(--backbg);color:#576b95";
    }
}

function quanwenan(){
    var evt = window.event || {};
    var target = evt.target || evt.srcElement || {};
    var ele = target.id;
    var elelang = target.lang;
    var quanwid=ele.replace("sh-content-quanwenan-","");

    if(elelang == 0){
        var dqdcla = document.getElementById("sh-content-qwdid-"+quanwid).className;//获取当前class
        var re = new RegExp("wzndhycyc","g");
        var Newdqdcla = dqdcla.replace(re, "");
        document.getElementById("sh-content-qwdid-"+quanwid).className=Newdqdcla;
        document.getElementById("sh-content-quanwenan-"+quanwid).lang=1;
        document.getElementById("sh-content-quanwenan-"+quanwid).innerText="收起";
    }else if(elelang == 1){
        document.getElementById("sh-content-qwdid-"+quanwid).className +="wzndhycyc";
        document.getElementById("sh-content-quanwenan-"+quanwid).lang=0;
        document.getElementById("sh-content-quanwenan-"+quanwid).innerText="全文";
    }

}

/*document.addEventListener('gesturestart', function(e) {
  e.preventDefault();
});

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

if (document.getElementById('day')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('day').addEventListener('click', function() {
    var day=document.getElementById("day").lang;
    var body = document.querySelector('body');
    if (day == 1) {

        document.getElementById("day").lang="0";
        document.getElementById("day-i").className="iconfont icon-yueliang";
        document.cookie = "dark_theme=dark-theme";

    }else if (day == 0){

        document.getElementById("day").lang="1";
        document.getElementById("day-i").className="iconfont icon-ai250";
        document.cookie = "dark_theme=root";
    }
  });
}

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}

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
    Sanqi.lazyLoad();
}
function applyArticleCardTone(){
    var cards = document.querySelectorAll('.sh-long-article-card[data-cover-tone]:not([data-tone-ready])');
    cards.forEach(function(card){
        var img = card.querySelector('img');
        if(!img) return;
        var src = img.getAttribute('data-src') || img.getAttribute('src');
        if(!src || src.indexOf('thumbnail.svg') !== -1) return;
        card.setAttribute('data-tone-ready', '1');

        var toneImg = new Image();
        toneImg.crossOrigin = 'anonymous';
        toneImg.onload = function(){
            try {
                var canvas = document.createElement('canvas');
                var size = 24;
                canvas.width = size;
                canvas.height = size;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(toneImg, 0, 0, size, size);
                var data = ctx.getImageData(0, 0, size, size).data;
                var r = 0;
                var g = 0;
                var b = 0;
                var count = 0;
                for(var i = 0; i < data.length; i += 16){
                    if(data[i + 3] < 40) continue;
                    r += data[i];
                    g += data[i + 1];
                    b += data[i + 2];
                    count++;
                }
                if(!count) return;
                r = Math.round(r / count);
                g = Math.round(g / count);
                b = Math.round(b / count);
                var darkR = Math.max(16, Math.round(r * 0.42));
                var darkG = Math.max(16, Math.round(g * 0.42));
                var darkB = Math.max(16, Math.round(b * 0.42));
                var deepR = Math.max(8, Math.round(r * 0.22));
                var deepG = Math.max(8, Math.round(g * 0.22));
                var deepB = Math.max(8, Math.round(b * 0.22));
                card.style.background = 'linear-gradient(90deg, rgb(' + darkR + ',' + darkG + ',' + darkB + '), rgb(' + deepR + ',' + deepG + ',' + deepB + '))';
            } catch (e) {
            }
        };
        toneImg.onerror = function(){
            card.removeAttribute('data-tone-ready');
        };
        toneImg.src = src;
    });
}

document.addEventListener('DOMContentLoaded', applyArticleCardTone);
window.addEventListener('load', applyArticleCardTone);

loaddemand();//璋冪敤涓€娆℃噿鍔犺浇
applyArticleCardTone();
