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
    var ele = window.event.srcElement.alt;//获取点击的表情alt
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
document.getElementById("sh-cont-img").style.display="none";//隐藏图片上传按钮
document.getElementById("sh-cont-yy").style.display="flex";//显示音乐
document.getElementById("filsp").style.display="none";//隐藏视频上传按钮
document.getElementById("sh-cont-sp").style.display="none";//隐藏视频链接输入框
document.getElementById("sh-cont-spfm").style.display="none";//隐藏视频封面输入框
document.getElementById("sh-content-video").style.display="none";//隐藏视频
document.getElementById("sh-content-video").src="";//删除视频预览
document.getElementById("files").value=null;//删除视频文件
}


//设置图文类型事件
function tw(){
document.getElementById("sh-cont-img").style.display="flex";//显示图片上传按钮
document.getElementById("sh-cont-yy").style.display="none";//隐藏音乐
document.getElementById("filsp").style.display="none";//隐藏视频上传按钮
document.getElementById("sh-cont-sp").style.display="none";//隐藏视频链接输入框
document.getElementById("sh-cont-spfm").style.display="none";//隐藏视频封面输入框
document.getElementById("sh-content-video").style.display="none";//隐藏视频
document.getElementById("sh-content-video").src="";//删除视频预览
document.getElementById("files").value=null;//删除视频文件
}


//设置视频类型事件
function sp(){
document.getElementById("sh-cont-img").style.display="none";//隐藏图片上传按钮
document.getElementById("sh-cont-yy").style.display="none";//隐藏音乐
document.getElementById("filsp").style.display="flex";//显示视频上传按钮
document.getElementById("sh-cont-sp").style.display="flex";//显示视频链接输入框
document.getElementById("sh-cont-spfm").style.display="flex";//显示视频封面输入框
}


//视频预览
document.querySelector('#files').onchange = function (){
  if(this.files.length){
    let file = this.files[0];
    let reader = new FileReader();
    //新建 FileReader 对象
    reader.onload = function(){
      // 当 FileReader 读取文件时候，读取的结果会放在 FileReader.result 属性中
      //console.log(this.result)
      document.querySelector('#sh-content-video').src = this.result;//将获取到的视频文件设置到video标签
      document.getElementById("sh-content-video").style.display="flex";//显示video
      document.getElementById("filsp").style.display="none";//隐藏上传按钮
      document.getElementById("sh-cont-sp").style.display="none";//隐藏视频链接输入框
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
    ajaxObj.open('get', './api/ip.php?ip=ok');
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
            var code=obj[0].code;//取状态码
            var region=obj[0].region;//取省份
            var city=obj[0].city;//取市级
            var addr=obj[0].addr;//取附加信息

            if (code == 200) {
                   var dz=region+'·'+city;//合成地址 省份加市级
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



document.oncontextmenu=new Function("event.returnValue=false"); //禁止右键



//设置评论框高度自适应
var textarea = document.getElementById("bletext");
textarea.addEventListener('input', (e) => {
textarea.style.height = '120px';
textarea.style.height = e.target.scrollHeight + 'px';
});





//音乐输入框内容改变检测
if (document.getElementById('music')) {
let timer; // 用于存储定时器的ID
const inputElement = document.getElementById('music'); // 获取 ID 为 "so" 的 input 元素
inputElement.addEventListener('input', function () {
    clearTimeout(timer); // 清除之前的定时器
    timer = setTimeout(function () {
        // 在1秒内没有再次改变，
        //实时检测输入框是否为纯数字
        var socn = document.getElementById("music").value;
        isNumeric = /^\d+$/.test(socn);
        if (isNumeric) {
          // 值是纯数字
          wyymusicjx(socn);
        } else {
          // 值不是纯数字
        }
    }, 200);
});
}
//解析网易云音乐id
function wyymusicjx(wyid){
    // 创建一个新的 XMLHttpRequest 对象
    var xhr = new XMLHttpRequest();
    // （1）创建异步对象
    var ajaxObj = new XMLHttpRequest();
    // （2）设置请求的参数。包括：请求的方法、请求的url。
    ajaxObj.open('get', 'http://api.qemao.com/api/wyymusic/?id='+wyid);
    // （3）发送请求
    ajaxObj.send();
    //（4）注册事件。 onreadystatechange事件，状态改变时就会调用。
    //如果要在数据完整请求回来的时候才调用，我们需要手动写一些判断的逻辑。
    ajaxObj.onreadystatechange = function () {
        // 为了保证 数据 完整返回，我们一般会判断 两个值
        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
            var obj = JSON.parse(ajaxObj.responseText); //由JSON字符串转换为JSON对象
            //console.log(obj)
            var title = obj[0].title;
            var author = obj[0].author;
            var url = obj[0].url;
            var pic = obj[0].pic;
            if (title == "" || title == null) {
                return;
            }
            document.getElementById("musicm").value=title;
            document.getElementById("musics").value=author;
            document.getElementById("musict").value=pic;
        }
    }
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
