/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
var input = document.getElementById("bletext");
var rangeIndex=null;//光标地位
//监听失焦
input.onblur = function(){
  rangeIndex = this.selectionStart;//获取失焦时光标的地位
}
//插入函数
function biaoqzj(){
    var evt = window.event || {};
    var target = evt.target || evt.srcElement || {};
    var ele = target.alt;
  if(rangeIndex){
    let oldVaue = input.value;
    input.value = oldVaue.slice(0,rangeIndex)+ele+oldVaue.slice(rangeIndex);
    rangeIndex = rangeIndex+ele.toString().length;
  }else{
    //input.value+=ele;
    //rangeIndex=input.value.length;
    let oldVaue = input.value;
    input.value = oldVaue.slice(0,rangeIndex)+ele+oldVaue.slice(rangeIndex);
    rangeIndex = rangeIndex+ele.toString().length;
  }
  input.focus();
  input.setSelectionRange(rangeIndex,rangeIndex);//从新定位光标
}



//设为广告事件
function swgg(){
document.getElementById("sh-cont-gg").style.display="flex";
}


//不设为广告事件
function qxgg(){
document.getElementById("sh-cont-gg").style.display="none";
}


//设置音乐类型事件
function yy(){
if(document.getElementById("sh-cont-article")) document.getElementById("sh-cont-article").style.display="none";
document.getElementById("sh-cont-img").style.display="none";//隐藏图片上传按钮
document.getElementById("sh-cont-yy").style.display="flex";//显示音乐
document.getElementById("filsp").style.display="none";//隐藏视频上传按钮
document.getElementById("sh-cont-sp").style.display="none";//隐藏视频链接输入框
document.getElementById("sh-cont-spfm").style.display="none";//隐藏视频封面输入框
document.getElementById("sh-content-video").style.display="none";//隐藏视频
document.getElementById("sh-content-video").src="";//删除视频预览
document.getElementById("files").value=null;//删除视频文件
document.getElementById("video_cover_data").value="";//删除视频封面数据
}


//设置图文类型事件
function tw(){
if(document.getElementById("sh-cont-article")) document.getElementById("sh-cont-article").style.display="none";
document.getElementById("sh-cont-img").style.display="flex";//显示图片上传按钮
document.getElementById("sh-cont-yy").style.display="none";//隐藏音乐
document.getElementById("filsp").style.display="none";//隐藏视频上传按钮
document.getElementById("sh-cont-sp").style.display="none";//隐藏视频链接输入框
document.getElementById("sh-cont-spfm").style.display="none";//隐藏视频封面输入框
document.getElementById("sh-content-video").style.display="none";//隐藏视频
document.getElementById("sh-content-video").src="";//删除视频预览
document.getElementById("files").value=null;//删除视频文件
document.getElementById("video_cover_data").value="";//删除视频封面数据
}


//设置视频类型事件
function sp(){
if(document.getElementById("sh-cont-article")) document.getElementById("sh-cont-article").style.display="none";
document.getElementById("sh-cont-img").style.display="none";//隐藏图片上传按钮
document.getElementById("sh-cont-yy").style.display="none";//隐藏音乐
document.getElementById("filsp").style.display="flex";//显示视频上传按钮
document.getElementById("sh-cont-sp").style.display="flex";//显示视频链接输入框
document.getElementById("sh-cont-spfm").style.display="flex";//显示视频封面输入框
}


//视频预览
function wz(){
if(document.getElementById("sh-cont-img")) document.getElementById("sh-cont-img").style.display="none";
if(document.getElementById("sh-cont-yy")) document.getElementById("sh-cont-yy").style.display="none";
if(document.getElementById("filsp")) document.getElementById("filsp").style.display="none";
if(document.getElementById("sh-cont-sp")) document.getElementById("sh-cont-sp").style.display="none";
if(document.getElementById("sh-cont-spfm")) document.getElementById("sh-cont-spfm").style.display="none";
if(document.getElementById("sh-content-video")) {
    document.getElementById("sh-content-video").style.display="none";
    document.getElementById("sh-content-video").src="";
}
if(document.getElementById("files")) document.getElementById("files").value=null;
if(document.getElementById("video_cover_data")) document.getElementById("video_cover_data").value="";
if(document.getElementById("sh-cont-article")) document.getElementById("sh-cont-article").style.display="flex";
if(document.getElementById("bletext")) document.getElementById("bletext").placeholder="支持 Markdown 语法，写正文...";
}

function setImageInputMode(mode){
    var linkBox = document.getElementById("sh-cont-imgul");
    var uploadBox = document.getElementById("cupload-7");
    var switchBtn = document.getElementById("tpscfs");
    var switchIcon = document.getElementById("twlimg");
    if(!linkBox || !uploadBox || !switchBtn || !switchIcon) return;

    if(mode === "link"){
        linkBox.style.display = "flex";
        uploadBox.style.display = "none";
        switchBtn.lang = "1";
        switchBtn.style.background = "var(--themetm)";
        switchIcon.className = "iconfont icon-qiehuan ri-sxfbbqls";
        switchBtn.title = "当前：外链图片";
    }else{
        linkBox.style.display = "none";
        uploadBox.style.display = "block";
        switchBtn.lang = "0";
        switchBtn.style.background = "var(--fgxys)";
        switchIcon.className = "iconfont icon-qiehuan ri-sxfbbq";
        switchBtn.title = "当前：上传图片";
    }
}

document.querySelector('#files').onchange = function (){
  if(this.files.length){
    let file = this.files[0];
    let reader = new FileReader();
    //新建 FileReader 对象
    reader.onload = function(){
      // 当 FileReader 读取文件时候，读取的结果会放在 FileReader.result 属性中
      //console.log(this.result)
      var videoEl = document.querySelector('#sh-content-video');
      videoEl.src = this.result;//将获取到的视频文件设置到video标签
      document.getElementById("sh-content-video").style.display="flex";//显示video
      document.getElementById("filsp").style.display="none";//隐藏上传按钮
      document.getElementById("sh-cont-sp").style.display="none";//隐藏视频链接输入框

      // 自动截图视频帧作为封面
      videoEl.onloadeddata = function() {
          var coverInput = document.getElementById('sppfm');
          if (coverInput && coverInput.value.trim() !== '') return;
          videoEl.currentTime = Math.min(1, videoEl.duration / 2);
      };
      videoEl.onseeked = function() {
          var canvas = document.createElement('canvas');
          canvas.width = videoEl.videoWidth;
          canvas.height = videoEl.videoHeight;
          var ctx = canvas.getContext('2d');
          ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
          var dataUrl = canvas.toDataURL('image/jpeg', 0.8);
          document.getElementById('video_cover_data').value = dataUrl;
          videoEl.poster = dataUrl;
      };
    };
    // 设置以什么方式读取文件，这里以base64方式
    reader.readAsDataURL(file);
   }
}


function myScript() {
if (document.getElementById("spp").value == "") {
    document.getElementById("filsp").style.display="flex";//隐藏上传按钮
}else{
    document.getElementById("filsp").style.display="none";//隐藏上传按钮
    document.getElementById("sh-content-video").src="";//删除视频预览
    document.getElementById("files").value=null;//删除视频文件
}
// 抖音链接自动解析
clearTimeout(window._dyParseTimer);
window._dyParseTimer = setTimeout(function(){ parseDouyinLink(); }, 600);
}

function parseDouyinLink() {
var sppInput = document.getElementById("spp");
if (!sppInput) return;
var val = sppInput.value.trim();
if (!val) return;

// 检测是否为抖音链接
var dyPattern = /https?:\/\/[^\s]*(?:douyin|iesdouyin)[^\s]*/i;
if (!dyPattern.test(val)) return;

// 提取链接
var match = val.match(dyPattern);
if (!match) return;
var dyUrl = match[0];

// 显示解析状态
var sppfmInput = document.getElementById("sppfm");
var videoEl = document.getElementById("sh-content-video");
sppInput.style.opacity = "0.6";
sppInput.disabled = true;

fetch("/api/douyin/parse", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-CSRF-Token": myallkeyVar || ""
    },
    body: "url=" + encodeURIComponent(dyUrl) + "&allkey=" + encodeURIComponent(myallkeyVar || "")
})
.then(function(res){ return res.json(); })
.then(function(result){
    sppInput.style.opacity = "1";
    sppInput.disabled = false;
    if (result.code === 0 && result.data) {
        var d = result.data;
        if (d.video) {
            sppInput.value = d.video;
            if (videoEl) {
                videoEl.src = d.video;
                videoEl.style.display = "block";
                document.getElementById("filsp").style.display = "none";
            }
        }
        if (d.cover && sppfmInput) {
            sppfmInput.value = d.cover;
        }
        if (!d.cover && d.video && videoEl) {
            videoEl.currentTime = Math.min(1, videoEl.duration || 1);
            videoEl.onseeked = function() {
                var canvas = document.createElement("canvas");
                canvas.width = videoEl.videoWidth;
                canvas.height = videoEl.videoHeight;
                var ctx = canvas.getContext("2d");
                ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
                var dataUrl = canvas.toDataURL("image/jpeg", 0.8);
                if (sppfmInput && !sppfmInput.value) sppfmInput.value = dataUrl;
                document.getElementById("video_cover_data").value = dataUrl;
                videoEl.poster = dataUrl;
                videoEl.onseeked = null;
            };
        }
    } else {
        alert(result.msg || "抖音解析失败");
    }
})
.catch(function(){
    sppInput.style.opacity = "1";
    sppInput.disabled = false;
    alert("抖音解析请求失败，请检查网络");
});
}


//表情列表开关
function shkgbqkg(){
if (document.getElementById("biaoqing").style.display == "none") {
    document.getElementById("biaoqing").style.display="grid";//显示表情
    document.getElementById("bqkg").style="background: var(--themetm)";
    document.getElementById("bqkgimg").className="iconfont icon-biaoqing ri-sxfbbqls";
}else{
    document.getElementById("biaoqing").style.display="none";//隐藏表情
    document.getElementById("bqkg").style="background: var(--fgxys)";
    document.getElementById("bqkgimg").className="iconfont icon-biaoqing ri-sxfbbq";
}
}



//广告开关
function ggkg(){
if (document.getElementById("ggkg").lang == "0") {
    document.getElementById("ggkg").lang="1";
    document.getElementById("ggkg").style="background: var(--themetm)";
    document.getElementById("ggkgimg").className="iconfont icon-ljgg ri-sxfbbqls";
    document.getElementById("ggmk").value="1";
    document.getElementById("sh-cont-gg").style.display="flex";//显示广告链接输入框
    document.getElementById("ggkg").title="取消广告";
}else{
    document.getElementById("ggkg").lang="0";
    document.getElementById("ggkg").style="background: var(--fgxys)";
    document.getElementById("ggkgimg").className="iconfont icon-ljgg ri-sxfbbq";
    document.getElementById("ggmk").value="0";
    document.getElementById("sh-cont-gg").style.display="none";//隐藏广告链接输入框
    document.getElementById("ggkg").title="设为广告";
}
}



//允许评论开关
function yxplkgz(){
if (document.getElementById("yxplkgz").lang == "1") {
    document.getElementById("yxplmkz").value="0";
    document.getElementById("yxplkgz").lang="0";
    document.getElementById("yxplkgz").style="background: var(--fgxys)";
    document.getElementById("yxplkgimg").className="iconfont icon-pinglun-yx ri-sxfbbq";
    document.getElementById("yxplkgz").title="禁止评论";
}else{
    document.getElementById("yxplmkz").value="1";
    document.getElementById("yxplkgz").lang="1";
    document.getElementById("yxplkgz").style="background: var(--themetm)";
    document.getElementById("yxplkgimg").className="iconfont icon-pinglun-yx ri-sxfbbqls";
    document.getElementById("yxplkgz").title="允许评论";
}
}


//匿名开关
function nmkgz(){
if (document.getElementById("nmkgz").lang == "1") {
    document.getElementById("nmmkz").value="0";
    document.getElementById("nmkgz").lang="0";
    document.getElementById("nmkgz").style="background: var(--fgxys)";
    document.getElementById("nmkgimg").className="iconfont icon-anonymous ri-sxfbbq";
    document.getElementById("nmkgz").title="匿名发布";
}else{
    document.getElementById("nmmkz").value="1";
    document.getElementById("nmkgz").lang="1";
    document.getElementById("nmkgz").style="background: var(--themetm)";
    document.getElementById("nmkgimg").className="iconfont icon-anonymous ri-sxfbbqls";
    document.getElementById("nmkgz").title="取消匿名";
}
}



//文章类型切换按钮事件
function lxqhkg(){

var dqlx = document.getElementById("lxmk").value;//获取当前文章类型
if (dqlx == 1) {
    //当前是图文 设为视频类型
    document.getElementById("lxqhimg").className="iconfont icon-shipinbofang ri-sxfbbqls";//设置图片标志为视频
    document.getElementById("tpscfs").style.display="none";
    sp();
    document.getElementById("lxmk").value="2";
    document.getElementById("lxqh").title="文章类型:视频";
}else if (dqlx == 2) {
    ////当前是视频 设为音乐类型
    document.getElementById("lxqhimg").className="iconfont icon-yinle_2 ri-sxfbbqls";//设置图片标志为音乐
    document.getElementById("tpscfs").style.display="none";
    yy();
    document.getElementById("lxmk").value="3";
    document.getElementById("lxqh").title="文章类型:音乐";
}else if (dqlx == 3) {
    ////当前是音乐 设为图文类型
    document.getElementById("lxqhimg").className="iconfont icon-tupian ri-sxfbbqls";//设置图片标志为视频
    document.getElementById("tpscfs").style.display="flex";
    tw();
    document.getElementById("lxmk").value="1";
    document.getElementById("lxqh").title="文章类型:图文";
}

}



function tpscfs(){
if(document.getElementById("tpscfs").lang == "0"){
    document.getElementById("sh-cont-imgul").style.display="flex";
    document.getElementById("cupload-7").style.display="none";
    document.getElementById("twlimg").className="iconfont icon-qiehuan ri-sxfbbqls";//切换图标颜色为绿色
    document.getElementById("tpscfs").style="background: var(--themetm)";//设背景绿色
    document.getElementById("tpscfs").lang = "1";
}else{
    document.getElementById("sh-cont-imgul").style.display="none";
    document.getElementById("cupload-7").style.display="block";
    document.getElementById("twlimg").className="iconfont icon-qiehuan ri-sxfbbq";//切换图标颜色为灰色
    document.getElementById("tpscfs").style="background: var(--fgxys)";//设背景灰色
    document.getElementById("tpscfs").lang = "0";
}
}



//获取当前ip地址
function hqdqip(){

loadpop("正在定位，请稍后...",'ok');
// （1）创建异步对象
    var ajaxObj = new XMLHttpRequest();
    // （2）设置请求的参数。包括：请求的方法、请求的url。
    ajaxObj.open('get', '/api/ip-location?ip=ok');
    // （3）发送请求
    ajaxObj.send();
    //（4）注册事件。 onreadystatechange事件，状态改变时就会调用。
    ajaxObj.onreadystatechange = function () {
        // 为了保证 数据 完整返回，我们一般会判断 两个值
        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
            if (ajaxObj.responseText == "") {
                errorpop("未获取到数据");
                return;
            }
            // 5.在注册的事件中 获取 返回的 内容 并修改页面的显示
            //console.log('数据返回成功');
            // 数据是保存在 异步对象的 属性中
            //console.log(ajaxObj.responseText);
            var obj = JSON.parse(ajaxObj.responseText); //由JSON字符串转换为JSON对象
            //console.log(obj);
            if (obj && typeof obj === 'object' && Array.isArray(obj) && obj.length > 0) obj = obj[0];
            var code=obj.code;//取状态码
            var d=obj.data||{};
            var region=d.region;//取省份
            var city=d.city;//取市级
            var addr=d.addr;//取附加信息

            if (code == 200 && (region || city || addr)) {
                   var dz=[region, city].filter(Boolean).join('·') || addr;//合成地址
                   document.getElementById("dwxx").value=dz;//设置地址显示
                   successpop("定位获取成功");
            }else{
                document.getElementById("dwxx").value="定位失败";//设置地址显示
                warnpop("定位获取失败");
            }
        }
    }

}



//表单发布事件
function dosubmit(){

        //判断广告是否填写
    if (document.getElementById("ggkg").lang == "1") {
        if (document.getElementById("gglj").value == "") {
            warnpop("请填写广告链接");
            return false;
        }
    }


    //判断视频是否填写
    if (document.getElementById("lxmk")) {
        if(document.getElementById("lxmk").value == "2"){
        if (document.getElementById('spp').value == "") {
            if (document.getElementById("files").files.length > 0) {
            }else{
                warnpop("请填写视频链接或上传视频");
                return false;
            }
        }
    }
    }



    //判断音乐是否填写
    if (document.getElementById("lxmk")) {
        if(document.getElementById("lxmk").value == "3"){
      if(document.getElementById("music").value == ""){
        warnpop("请填写音乐ID或链接");
        return false;
      }
      if(document.getElementById("musicm").value == ""){
        warnpop("请填写歌名");
        return false;
      }
      if(document.getElementById("musics").value == ""){
        warnpop("请填写歌手");
        return false;
      }
    }
    }




    var text=document.getElementById("bletext").value;//获取内容框
    if (text != "") {
        loadpop("正在发布，请稍后...",'ok');
    }

    document.getElementById("allkey").value=myallkeyVar;
}



//设置评论框高度自适应
var textarea = document.getElementById("bletext");
textarea.addEventListener('input', (e) => {
textarea.style.height = '120px';
textarea.style.height = e.target.scrollHeight + 'px';
});





//音乐平台选择
function selectMusicPlatform(el){
    var items = document.querySelectorAll('.music-platform-item');
    for(var i=0;i<items.length;i++){items[i].classList.remove('music-platform-active');}
    el.classList.add('music-platform-active');
    document.getElementById('musicplatform').value = el.getAttribute('data-val');
    // 切换平台时清空已填信息，方便重新识别
    document.getElementById('music').value = '';
    document.getElementById('musicm').value = '';
    document.getElementById('musics').value = '';
    document.getElementById('musict').value = '';
}

//音乐输入框内容改变检测（多平台）
if (document.getElementById('music')) {
var musicTimer;
function musicTryParse(){
    clearTimeout(musicTimer);
    musicTimer = setTimeout(function () {
        var val = document.getElementById("music").value.trim();
        if (!val) return;
        var platform = (document.getElementById("musicplatform") || {}).value || 'netease';
        // 网易云和酷我rid需要纯数字，QQ和酷狗可以是字母数字混合
        if (platform === 'netease' && !/^\d+$/.test(val)) return;
        if (platform === 'kuwo' && !/^\d+$/.test(val) && !/play_detail\/\d+/.test(val)) return;
        musicAutoParse(platform, val);
    }, 300);
}
document.getElementById('music').addEventListener('input', musicTryParse);
// 切换平台时如果已有输入内容，立即触发解析
if (document.getElementById('musicplatform')) {
    document.getElementById('musicplatform').addEventListener('change', function(){
        var val = document.getElementById("music").value.trim();
        if (val) musicTryParse();
    });
}
}
function musicAutoParse(platform, val){
    var apiMap = {
        netease: '/api/music/netease',
        qq: '/api/music/qq',
        kugou: '/api/music/kugou',
        kuwo: '/api/music/kuwo'
    };
    var url = apiMap[platform] || apiMap.netease;
    Sanqi.apiPost(url, { id: val }, function(res){
        if (!res) return;
        if (res.code == '200' || res.code == 200) {
            var data = res.data;
            if (!data.name) return;
            document.getElementById("musicm").value = data.name || '';
            document.getElementById("musics").value = data.artist || '';
            document.getElementById("musict").value = data.cover || '';
            // 更新URL为API返回的解析后的URL
            if (data.url) {
                document.getElementById("music").value = data.url;
            }
        }
    });
}




function loaddemand(){
    Sanqi.lazyLoad();
}

loaddemand();//调用一次懒加载
var sanqiOldDosubmit = typeof dosubmit === "function" ? dosubmit : null;
function sanqiHideArticleBox(){
    if(document.getElementById("sh-cont-article")) document.getElementById("sh-cont-article").style.display="none";
    if(document.getElementById("bletext")) document.getElementById("bletext").placeholder="说点什么..";
}
function sanqiShowArticleBox(){
    wz();
    setArticleCoverMode(document.getElementById("article_cover_mode") && document.getElementById("article_cover_mode").value == "upload" ? "upload" : "link");
}
function sanqiRestoreImageMode(){
    var switchBtn = document.getElementById("tpscfs");
    if(switchBtn && switchBtn.lang == "1"){
        setImageInputMode("link");
    }else{
        setImageInputMode("upload");
    }
}
tpscfs = function(){
    var switchBtn = document.getElementById("tpscfs");
    if(!switchBtn) return;
    setImageInputMode(switchBtn.lang == "1" ? "upload" : "link");
};
function setArticleCoverMode(mode){
    var linkBox = document.getElementById("article-cover-link");
    var uploadBox = document.getElementById("article-cover-upload");
    var modeInput = document.getElementById("article_cover_mode");
    var switchBtn = document.getElementById("article_cover_switch");
    var switchIcon = document.getElementById("article_cover_switch_icon");
    if(!linkBox || !uploadBox || !modeInput || !switchBtn || !switchIcon) return;

    if(mode === "upload"){
        linkBox.style.display = "none";
        uploadBox.style.display = "block";
        modeInput.value = "upload";
        switchBtn.lang = "upload";
        switchBtn.title = "切换外链图片";
        switchBtn.style.background = "var(--themetm)";
        switchIcon.className = "iconfont icon-qiehuan ri-sxfbbqls";
    }else{
        linkBox.style.display = "block";
        uploadBox.style.display = "none";
        modeInput.value = "link";
        switchBtn.lang = "link";
        switchBtn.title = "切换上传图片";
        switchBtn.style.background = "var(--fgxys)";
        switchIcon.className = "iconfont icon-qiehuan ri-sxfbbq";
    }
}
function articleCoverToggle(){
    var modeInput = document.getElementById("article_cover_mode");
    setArticleCoverMode(modeInput && modeInput.value == "upload" ? "link" : "upload");
}
lxqhkg = function(){
    var typeInput = document.getElementById("lxmk");
    if(!typeInput) return;
    var dqlx = typeInput.value;
    if(dqlx == "1"){
        document.getElementById("lxqhimg").className="iconfont icon-shipinbofang ri-sxfbbqls";
        document.getElementById("tpscfs").style.display="none";
        sanqiHideArticleBox();
        sp();
        typeInput.value="2";
        document.getElementById("lxqh").title="文章类型:视频";
    }else if(dqlx == "2"){
        document.getElementById("lxqhimg").className="iconfont icon-yinle_2 ri-sxfbbqls";
        document.getElementById("tpscfs").style.display="none";
        sanqiHideArticleBox();
        yy();
        typeInput.value="3";
        document.getElementById("lxqh").title="文章类型:音乐";
    }else if(dqlx == "3"){
        document.getElementById("lxqhimg").className="iconfont icon-changwenben ri-sxfbbqls";
        document.getElementById("tpscfs").style.display="none";
        sanqiShowArticleBox();
        typeInput.value="4";
        document.getElementById("lxqh").title="文章类型:文章";
    }else{
        document.getElementById("lxqhimg").className="iconfont icon-tupian ri-sxfbbqls";
        document.getElementById("tpscfs").style.display="flex";
        tw();
        sanqiRestoreImageMode();
        sanqiHideArticleBox();
        typeInput.value="1";
        document.getElementById("lxqh").title="文章类型:图文";
    }
};
dosubmit = function(){
    var typeInput = document.getElementById("lxmk");
    if(typeInput && typeInput.value == "4"){
        if(document.getElementById("article_title") && document.getElementById("article_title").value.trim() == ""){
            warnpop("请填写文章标题");
            return false;
        }
        if(document.getElementById("bletext") && document.getElementById("bletext").value.trim() == ""){
            warnpop("请填写文章内容");
            return false;
        }
    }
    return sanqiOldDosubmit ? sanqiOldDosubmit() : true;
};
if(document.getElementById("lxmk") && document.getElementById("lxmk").value == "1"){
    setImageInputMode("upload");
}
if(document.getElementById("lxmk") && document.getElementById("lxmk").value == "4"){
    setArticleCoverMode(document.getElementById("article_cover_mode") && document.getElementById("article_cover_mode").value == "upload" ? "upload" : "link");
}

(function(){
    var form = document.getElementById("edit-form");
    if(!form || !window.Sanqi || !Sanqi.apiPost) return;

    var submitting = false;

    form.addEventListener("submit", function(event){
        event.preventDefault();
        if(submitting) return false;
        if(typeof dosubmit === "function" && dosubmit() === false) return false;
        if(typeof window.confirmPollChangeIfNeeded === "function" && window.confirmPollChangeIfNeeded() === false) return false;

        submitting = true;
        var formData = new FormData(form);
        Sanqi.apiPost(form.getAttribute("action") || "/api/article/save", formData, function(res){
            submitting = false;
            if(res.code == "200"){
                if(typeof successpop === "function") successpop(res.msg || "发布成功");
                var d = res.data || {};
            var redirect = d.redirect || (d.cid ? "/view/" + d.cid : "/home");
                setTimeout(function(){
                    window.location.href = redirect;
                }, 450);
                return;
            }
            if(typeof errorpop === "function") errorpop(res.msg || "发布失败");
        }, function(res){
            submitting = false;
            if(typeof errorpop === "function") errorpop(res.msg || "发布失败，请稍后重试");
        });
        return false;
    });
})();
