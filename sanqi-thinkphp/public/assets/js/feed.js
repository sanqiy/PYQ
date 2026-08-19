/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
// Feed loading and timeline video observation.

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



//视频管理器：同一时间只播放一个视频 + 离开可视区域自动暂停
var activeVideo = null; // 当前正在播放的视频元素

// 暂停当前活跃视频
function pauseActiveVideo() {
    if (activeVideo && !activeVideo.paused) {
        activeVideo.pause();
    }
}

// 设置当前活跃视频（暂停上一个）
function setActiveVideo(video) {
    if (activeVideo && activeVideo !== video && !activeVideo.paused) {
        activeVideo.pause();
    }
    activeVideo = video;
}

// IntersectionObserver：视频离开可视区域时自动暂停
var _videoObserver = null;
var _observedVideos = new WeakSet();

function getVideoObserver() {
    if (!_videoObserver && 'IntersectionObserver' in window) {
        _videoObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                var video = entry.target;
                // 离开可视区域 且 正在播放 且 不是全屏放大状态
                if (!entry.isIntersecting && !video.paused) {
                    var container = video.closest('.sh-video');
                    var isExpanded = container && container.classList.contains('videofdb');
                    if (!isExpanded) {
                        video.pause();
                        if (activeVideo === video) activeVideo = null;
                    }
                }
            });
        }, { threshold: 0.1 });
    }
    return _videoObserver;
}

function initVideoObserver() {
    var observer = getVideoObserver();
    if (!observer) return;
    document.querySelectorAll('video[name="sh-videokid"], .homecontent-right-tw-video video').forEach(function(v) {
        if (!_observedVideos.has(v)) {
            observer.observe(v);
            _observedVideos.add(v);
        }
    });
}

// 页面加载后初始化视频观察器
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVideoObserver);
} else {
    initVideoObserver();
}

// 动态加载新内容后重新观察（适配触底加载）
function observeNewVideos() {
    initVideoObserver();
}


//监听屏幕上下滚动

function hqgd(){
    var footerZt = Sanqi.id("footer-text-zt");
    var footerHqgd = Sanqi.id("footer-text-hqgd");
    if (!footerZt || !footerHqgd) {
        return;
    }
    if (footerZt.innerText == "- \u6ca1\u6709\u66f4\u591a\u5566 -") {
        return;
    }
    if (footerZt.innerText == "\u6b63\u5728\u52a0\u8f7d..") {
        return;
    }
    if ((parseInt(footerHqgd.innerText, 10) || 0) <= 0) {
        footerHqgd.innerText = document.querySelectorAll("#sh-nrbk .sh-content").length;
    }

    var lastId = footerHqgd.getAttribute('data-last-id') || '';
    if (!lastId) {
        var lastPost = document.querySelector("#sh-nrbk .sh-content:last-child");
        lastId = lastPost ? (lastPost.getAttribute('data-post-id') || '') : '';
    }
    var page = String(footerHqgd.innerText);
    var urlParams = new URLSearchParams(window.location.search);
    var soValue = urlParams.get('so');
    var tagValue = urlParams.get('tag');

    footerZt.style.animation = "colorChange 0.8s infinite";
    footerZt.innerText = "\u6b63\u5728\u52a0\u8f7d..";

    Sanqi.apiPost('/api/load-more', {
        last_id: lastId,
        page: page,
        so: soValue || '',
        tag: tagValue || ''
    }, function(res){
        footerZt.removeAttribute("style");
        if (!res) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            footerZt.innerText = "\u52a0\u8f7d\u66f4\u591a..";
            return;
        }

        var code = res.code || '';
        var data = res.data || {};
        if (code != '200' || !data) {
            errorpop(res.msg || "\u52a0\u8f7d\u5931\u8d25");
            footerZt.innerText = "\u52a0\u8f7d\u66f4\u591a..";
            return;
        }

        var html = data.html || '';
        var hasMore = data.hasMore || false;
        var newOffset = data.offset || 0;
        var nextCursor = data.next_cursor || '';
        if (!html) {
            footerZt.innerText = "- \u6ca1\u6709\u66f4\u591a\u5566 -";
            return;
        }

        $("#sh-nrbk").append(html);
        footerZt.innerText = hasMore ? "\u52a0\u8f7d\u66f4\u591a.." : "- \u6ca1\u6709\u66f4\u591a\u5566 -";
        loaddemand();
        applyArticleCardTone();
        observeNewVideos();
        footerHqgd.innerText = newOffset;
        if (nextCursor) {
            footerHqgd.setAttribute('data-last-id', nextCursor);
        }
    }, function(res){
        footerZt.removeAttribute("style");
        errorpop((res && res.msg) || "\u52a0\u8f7d\u5931\u8d25");
        footerZt.innerText = "\u52a0\u8f7d\u66f4\u591a..";
    });
}















//获取更多的评论
