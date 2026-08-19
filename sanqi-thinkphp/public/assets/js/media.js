/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
(function (window, document) {
    'use strict';

    var Sanqi = window.Sanqi || {};
    var articleTimer = null;
    var topTimer = null;
    var randomRetrying = false;

    function id(name) {
        return Sanqi.id ? Sanqi.id(name) : document.getElementById(name);
    }

    function qs(selector, scope) {
        return Sanqi.qs ? Sanqi.qs(selector, scope) : (scope || document).querySelector(selector);
    }

    function safePlay(audio, onError) {
        if (!audio || typeof audio.play !== 'function') return null;
        var result = audio.play();
        if (result && typeof result.catch === 'function') {
            result.catch(function () {
                if (onError) onError();
            });
        }
        return result;
    }

    function installPlayGuard() {
        if (window.__SANQI_MEDIA_PLAY_GUARD__) return;
        window.__SANQI_MEDIA_PLAY_GUARD__ = true;
        ['HTMLAudioElement', 'HTMLVideoElement'].forEach(function (name) {
            var ctor = window[name];
            if (!ctor || !ctor.prototype || !ctor.prototype.play) return;
            var originalPlay = ctor.prototype.play;
            ctor.prototype.play = function () {
                var result = originalPlay.apply(this, arguments);
                if (result && typeof result.catch === 'function') {
                    result.catch(function () {});
                }
                return result;
            };
        });
    }

    function ensureArticleMusicPlayer() {
        var musicbc = id('musicbc');
        if (!musicbc) {
            musicbc = document.createElement('div');
            musicbc.id = 'musicbc';
            musicbc.className = 'musicbc';
            musicbc.lang = '0';
            musicbc.style.display = 'none';
            document.body.appendChild(musicbc);
        }
        if (!id('musicplay')) {
            var audio = document.createElement('audio');
            audio.id = 'musicplay';
            audio.lang = '0';
            audio.className = '';
            audio.style.display = 'none';
            audio.controls = true;
            musicbc.appendChild(audio);
        }
        if (!id('yszt')) {
            var cover = document.createElement('img');
            cover.id = 'yszt';
            cover.src = '/assets/img/musicba.jpg';
            cover.lang = '0';
            cover.style.display = 'none';
            musicbc.appendChild(cover);
        }
        if (!id('ming')) {
            var bg = document.createElement('img');
            bg.id = 'ming';
            bg.src = '/assets/img/musicba.jpg';
            bg.style.display = 'none';
            musicbc.appendChild(bg);
        }
        if (!id('sh-musiccz-zb')) {
            var icon = document.createElement('i');
            icon.id = 'sh-musiccz-zb';
            icon.className = 'iconfont icon-jixu ri-z-sx';
            icon.style.display = 'none';
            musicbc.appendChild(icon);
        }
        return musicbc;
    }

    function getArticleMusicCid() {
        var evt = window.event || null;
        var target = evt ? (evt.currentTarget || evt.target || evt.srcElement) : null;
        if (target && target.id && target.id.indexOf('sh-aud-left-plak-') === 0) {
            return target.id.replace('sh-aud-left-plak-', '');
        }
        if (target && target.lang && target.lang !== '0') {
            return target.lang;
        }
        if (target && target.closest) {
            var icon = target.closest('[id^="sh-aud-left-plak-"]');
            if (icon) return icon.id.replace('sh-aud-left-plak-', '');
            var wrap = target.closest('[lang]');
            if (wrap && wrap.lang && wrap.lang !== '0') return wrap.lang;
        }
        return '';
    }

    function setArticleIcon(cid, playing) {
        var icon = id('sh-aud-left-plak-' + cid);
        if (!icon) return;
        icon.lang = playing ? '1' : '0';
        icon.className = playing ? 'iconfont icon-iconstop' : 'iconfont icon-jixu';
    }

    function setFloatIcon(playing) {
        var icon = id('sh-musiccz-zb');
        if (icon) icon.className = playing ? 'iconfont icon-iconstop ri-z-sx' : 'iconfont icon-jixu ri-z-sx';
    }

    function showFloatPlayer() {
        var box = id('musicbc');
        if (!box) return;
        box.style.right = box.lang == '1' ? '10px' : '-80px';
    }

    function resetArticleMusicState() {
        var audio = id('musicplay');
        var cid = audio ? audio.className : '';
        if (cid) setArticleIcon(cid, false);
        if (audio) audio.lang = '0';
        setFloatIcon(false);
        if (articleTimer) clearInterval(articleTimer);
    }

    function watchArticleAudio(audio) {
        if (articleTimer) clearInterval(articleTimer);
        articleTimer = setInterval(function () {
            if (!audio || audio.paused) {
                resetArticleMusicState();
            }
        }, 500);
    }

    function pauseTopMusic() {
        var topAudio = id('sh-main-top-musicplay-b');
        if (topAudio) topAudio.pause();
        resetTopMusicState();
    }

    function startArticleAudio(cid, url, cover) {
        ensureArticleMusicPlayer();
        var audio = id('musicplay');
        if (!audio) return;

        pauseTopMusic();
        if (audio.className && audio.className !== cid) {
            setArticleIcon(audio.className, false);
        }
        if (audio.className !== cid || audio.src !== url) {
            audio.src = url;
            audio.className = cid;
        }
        audio.lang = '1';
        var coverImg = id('yszt');
        var bgImg = id('ming');
        if (coverImg) {
            coverImg.src = cover || '/assets/img/musicba.jpg';
            coverImg.dataset.src = coverImg.src;
        }
        if (bgImg) {
            bgImg.src = cover || '/assets/img/musicba.jpg';
            bgImg.dataset.src = bgImg.src;
        }
        setArticleIcon(cid, true);
        setFloatIcon(true);
        showFloatPlayer();
        safePlay(audio, function () {
            resetArticleMusicState();
            if (typeof warnpop === 'function') warnpop('\u97f3\u4e50\u64ad\u653e\u5931\u8d25');
        });
        watchArticleAudio(audio);
    }

    function audbf() {
        var cid = getArticleMusicCid();
        if (!cid) {
            if (typeof warnpop === 'function') warnpop('\u672a\u83b7\u53d6\u5230\u97f3\u4e50\u4fe1\u606f');
            return;
        }
        var source = id('musicurl-' + cid);
        var audio = id('musicplay');
        if (audio && audio.className === cid && audio.lang == '1') {
            audio.pause();
            resetArticleMusicState();
            return;
        }
        if (!source || !source.lang) {
            if (typeof warnpop === 'function') warnpop('\u97f3\u4e50\u5730\u5740\u4e0d\u5b58\u5728');
            return;
        }
        startArticleAudio(cid, source.lang, source.className);
    }

    function bfpy() {
        ensureArticleMusicPlayer();
        var audio = id('musicplay');
        if (!audio || !audio.className) return;
        if (audio.lang == '1' && !audio.paused) {
            audio.pause();
            resetArticleMusicState();
            return;
        }
        startArticleAudio(audio.className, audio.src, (id('yszt') || {}).src);
    }

    function bfpg() {
        ensureArticleMusicPlayer();
        var audio = id('musicplay');
        if (audio) {
            audio.pause();
            audio.removeAttribute('src');
            audio.className = '';
            audio.lang = '0';
        }
        resetArticleMusicState();
        var box = id('musicbc');
        if (box) box.style.right = box.lang == '1' ? '-200px' : '-800px';
    }

    function mbpy() {
        var box = id('musicbc');
        var cover = id('yszt');
        if (!box || !cover || box.lang == '1') return;
        if (cover.lang == '0') {
            box.style.transform = 'translateX(-80px)';
            cover.lang = '1';
        } else {
            box.style.transform = 'translateX(0px)';
            cover.lang = '0';
        }
    }

    function resetTopMusicState() {
        var btn = id('sh-main-top-mu');
        var loading = id('sh-main-top-mucisjd');
        var wrap = id('sh-main-top-g-m');
        if (loading) loading.style.display = 'none';
        if (wrap) wrap.style = 'background: rgb(215 215 215 / 75%);';
        if (btn) {
            btn.lang = '0';
            var state = btn.getAttribute('data-bfzt');
            if (state == 'bbz') {
                btn.className = 'iconfont icon-jixu ri-z-sx';
                btn.setAttribute('data-bfzt', 'bb');
            } else if (state == 'zbbh') {
                btn.className = 'iconfont icon-jixu ri-z-sxh';
                btn.setAttribute('data-bfzt', 'bbh');
            } else if (btn.className.indexOf('icon-iconstop') !== -1) {
                btn.className = btn.className.replace('icon-iconstop', 'icon-jixu');
            }
        }
        if (topTimer) clearInterval(topTimer);
    }

    function topMusicPlay(audio) {
        return safePlay(audio, function () {
            resetTopMusicState();
            if (!randomRetrying) {
                randomRetrying = true;
                setTimeout(function () {
                    sjsyyy();
                    randomRetrying = false;
                }, 200);
            }
        });
    }

    function setTopMusicPlayingState(audio) {
        var btn = id('sh-main-top-mu');
        var loading = id('sh-main-top-mucisjd');
        var wrap = id('sh-main-top-g-m');
        topMusicPlay(audio);
        if (btn) btn.lang = '1';
        if (loading) loading.style.display = 'block';
        if (wrap) wrap.style = 'background: rgb(255,255,255,0);';
        if (btn) {
            var state = btn.getAttribute('data-bfzt');
            if (state == 'bb') {
                btn.className = 'iconfont icon-iconstop ri-z-sx';
                btn.setAttribute('data-bfzt', 'bbz');
            } else if (state == 'bbh') {
                btn.className = 'iconfont icon-iconstop ri-z-sxh';
                btn.setAttribute('data-bfzt', 'zbbh');
            }
        }
        if (topTimer) clearInterval(topTimer);
        topTimer = setInterval(function () {
            if (!audio || audio.paused) resetTopMusicState();
        }, 500);
    }

    function syaudbf() {
        var btn = id('sh-main-top-mu');
        var audio = id('sh-main-top-musicplay-b');
        if (!btn || !audio) return;
        if (!audio.src && audio.dataset.src) audio.src = audio.dataset.src;
        if (btn.lang == '0') {
            var articleAudio = id('musicplay');
            if (articleAudio) articleAudio.pause();
            resetArticleMusicState();
            setTopMusicPlayingState(audio);
        } else {
            audio.pause();
            resetTopMusicState();
        }
    }

    function sjsyyy() {
        if (!Sanqi.apiPost) return;
        Sanqi.apiPost('/api/music/random', { q: 1 }, function (obj) {
            if (!obj || !obj.code) {
                if (typeof errorpop === 'function') errorpop('\u672a\u83b7\u53d6\u5230\u6570\u636e');
                return;
            }
            if (obj.code != 200 && obj.code != '200') {
                if (typeof errorpop === 'function') errorpop('\u83b7\u53d6\u6b4c\u66f2\u5931\u8d25');
                return;
            }
            var audio = id('sh-main-top-musicplay-b');
            var wrap = id('sh-main-top-g-m');
            if (!audio || !wrap) {
                if (typeof warnpop === 'function') warnpop('\u97f3\u4e50\u64ad\u653e\u5668\u672a\u52a0\u8f7d');
                return;
            }
            audio.pause();
            resetTopMusicState();
            var d = obj.data || {};
            wrap.lang = d.mum || '';
            audio.src = d.muurl || '';
            setTopMusicPlayingState(audio);
        }, function (res) {
            if (typeof errorpop === 'function') errorpop((res && res.msg) || '\u83b7\u53d6\u6b4c\u66f2\u5931\u8d25');
        });
    }

    function initTopMusicErrorHandler() {
        var audio = id('sh-main-top-musicplay-b');
        if (!audio || audio.__sanqiMusicBound) return;
        audio.__sanqiMusicBound = true;
        audio.addEventListener('error', function () {
            resetTopMusicState();
            if (!randomRetrying) {
                randomRetrying = true;
                setTimeout(function () {
                    sjsyyy();
                    randomRetrying = false;
                }, 200);
            }
        });
        audio.addEventListener('ended', function () {
            resetTopMusicState();
            sjsyyy();
        });
    }

    function initFloatDrag() {
        var draggable = id('yszt');
        var box = id('musicbc');
        if (!draggable || !box || box.lang != '1' || box.__sanqiDragBound) return;
        box.__sanqiDragBound = true;
        var isDragging = false;
        var offset = { x: 0, y: 0 };

        function startDragging(e) {
            isDragging = true;
            var rect = draggable.getBoundingClientRect();
            var clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
            var clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
            offset.x = clientX - rect.left;
            offset.y = clientY - rect.top;
        }

        function drag(e) {
            if (!isDragging) return;
            var clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
            var y = clientY - offset.y;
            var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
            box.style.top = Math.max(0, Math.min(y, screenHeight - box.offsetHeight)) + 'px';
        }

        function stopDragging() {
            isDragging = false;
        }

        draggable.addEventListener('mousedown', startDragging);
        draggable.addEventListener('touchstart', startDragging);
        document.addEventListener('mousemove', drag);
        document.addEventListener('touchmove', drag);
        document.addEventListener('mouseup', stopDragging);
        document.addEventListener('touchend', stopDragging);
    }

    installPlayGuard();
    document.addEventListener('DOMContentLoaded', function () {
        initTopMusicErrorHandler();
        initFloatDrag();
    });

    window.ensureArticleMusicPlayer = ensureArticleMusicPlayer;
    window.audbf = audbf;
    window.bfpy = bfpy;
    window.bfpg = bfpg;
    window.mbpy = mbpy;
    window.resetTopMusicState = resetTopMusicState;
    window.topMusicPlay = topMusicPlay;
    window.setTopMusicPlayingState = setTopMusicPlayingState;
    window.syaudbf = syaudbf;
    window.sjsyyy = sjsyyy;
})(window, document);
