/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
(function (window, document) {
    'use strict';

    var Sanqi = window.Sanqi || {};

    function id(name) {
        return document.getElementById(name);
    }

    function qs(selector, scope) {
        return (scope || document).querySelector(selector);
    }

    function qsa(selector, scope) {
        return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
    }

    function csrfToken() {
        if (window.__SANQI_CSRF_TOKEN__) {
            return window.__SANQI_CSRF_TOKEN__;
        }
        if (window.myallkeyVar) {
            return window.myallkeyVar;
        }
        var meta = qs('meta[name="csrf-token"]');
        if (meta && meta.getAttribute('content')) {
            return meta.getAttribute('content');
        }
        var input = id('allkey');
        return input && input.value ? input.value : '';
    }

    function toBody(data) {
        if (typeof FormData !== 'undefined' && data instanceof FormData) {
            if (!data.has('allkey')) {
                data.append('allkey', csrfToken());
            }
            return data;
        }
        if (typeof URLSearchParams !== 'undefined' && data instanceof URLSearchParams) {
            if (!data.has('allkey')) {
                data.append('allkey', csrfToken());
            }
            return data.toString();
        }
        if (typeof data === 'string') {
            return data + (data && data.indexOf('allkey=') === -1 ? '&allkey=' + encodeURIComponent(csrfToken()) : '');
        }

        var params = new URLSearchParams();
        data = data || {};
        Object.keys(data).forEach(function (key) {
            if (data[key] !== undefined && data[key] !== null) {
                params.append(key, data[key]);
            }
        });
        if (!params.has('allkey')) {
            params.append('allkey', csrfToken());
        }
        return params.toString();
    }

    function apiPost(url, data, callback, errorCallback) {
        var body = toBody(data);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        if (!(typeof FormData !== 'undefined' && body instanceof FormData)) {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        var token = csrfToken();
        if (token) {
            xhr.setRequestHeader('X-CSRF-Token', token);
        }
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
                return;
            }
            var response = xhr.responseText;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {}
            if (response && typeof response === 'object' && Array.isArray(response) && response.length > 0) {
                response = response[0];
            }
            if (xhr.status >= 200 && xhr.status < 300) {
                if (callback) callback(response, xhr);
            } else if (errorCallback) {
                errorCallback(response, xhr);
            }
        };
        xhr.send(body);
        return xhr;
    }

    function openModal(name, display) {
        var el = typeof name === 'string' ? id(name) : name;
        if (el) {
            el.style.display = display || 'flex';
        }
        return el;
    }

    function closeModal(name) {
        var el = typeof name === 'string' ? id(name) : name;
        if (el) {
            el.style.display = 'none';
        }
        return el;
    }

    function isScrollAtBottom(offset) {
        offset = typeof offset === 'number' ? offset : 0;
        var documentHeight = Math.max(
            document.body.scrollHeight,
            document.documentElement.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.offsetHeight,
            document.body.clientHeight,
            document.documentElement.clientHeight
        );
        var windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
        return scrollTop + windowHeight + offset >= documentHeight;
    }

    var lazyObserver = null;
    function lazyLoad(root) {
        var images = qsa('img[data-src]', root || document).filter(function (img) {
            return img.getAttribute('data-src');
        });
        if (!images.length) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            images.forEach(function (img) {
                img.src = img.getAttribute('data-src');
                img.removeAttribute('data-src');
            });
            return;
        }

        if (!lazyObserver) {
            lazyObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    var img = entry.target;
                    var src = img.getAttribute('data-src');
                    if (src) {
                        img.onerror = function () {
                            var fallback = this.getAttribute('data-fallback');
                            if (fallback && this.src.indexOf(fallback) === -1) {
                                this.src = fallback;
                                this.removeAttribute('data-src');
                                return;
                            }
                            this.onerror = null;
                            this.removeAttribute('data-src');
                            this.src = '/assets/img/img-load-error.svg';
                            this.classList.add('img-error');
                        };
                        img.src = src;
                        img.removeAttribute('data-src');
                    }
                    lazyObserver.unobserve(img);
                });
            }, { rootMargin: '160px 0px' });
        }

        images.forEach(function (img) {
            lazyObserver.observe(img);
        });
    }

    function setClass(name, className) {
        var el = id(name);
        if (el) {
            el.className = className;
        }
    }

    function setDisplay(name, display) {
        var el = id(name);
        if (el) {
            el.style.display = display;
        }
    }

    function scrollHeader(config) {
        config = config || {};
        var active = (document.documentElement.scrollTop || document.body.scrollTop) >= (config.threshold || 230);
        var head = id('sh-main-head-top');
        if (head) {
            head.style = active
                ? 'background:var(--dbztlys);backdrop-filter: saturate(180%) blur(20px);-webkit-backdrop-filter: saturate(180%) blur(20px)'
                : 'background:var(--dbztlysh)';
        }

        var classes = active ? (config.activeClasses || {}) : (config.normalClasses || {});
        Object.keys(classes).forEach(function (key) {
            setClass(key, classes[key]);
        });

        if (config.titleColorId) {
            var title = id(config.titleColorId);
            if (title) {
                title.style = active ? 'color: var(--iconhs);' : 'color: var(--iconbs);';
            }
        }

        var shMenu = id('sh-menu');
if (shMenu) {
    if (active) shMenu.classList.add('scrtop-show');
    else shMenu.classList.remove('scrtop-show');
}
        return active;
    }

    function readCookie(name) {
        var parts = ('; ' + document.cookie).split('; ' + name + '=');
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return '';
    }

    function writeCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
    }

    function deleteCookie(name, path) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=' + (path || '/') + '; SameSite=Lax';
    }

    function preferredTheme() {
        var cookieTheme = decodeURIComponent(readCookie('dark_theme') || '');
        if (cookieTheme === 'dark-theme') return 'dark';
        if (cookieTheme === 'root' || cookieTheme === 'light') return 'light';
        try {
            var stored = localStorage.getItem('sanqi_theme');
            if (stored === 'dark' || stored === 'light') return stored;
        } catch (e) {}
        return document.body && document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        var isDark = theme === 'dark';
        if (document.body) {
            document.body.classList.toggle('dark-theme', isDark);
        }
        var btn = id('day');
        var icon = id('day-i');
        if (btn) {
            btn.lang = isDark ? '0' : '1';
            btn.setAttribute('title', isDark ? '切换日间模式' : '切换夜间模式');
        }
        if (icon) {
            icon.className = isDark ? 'iconfont icon-yueliang' : 'iconfont icon-ai250';
        }
    }

    function setTheme(theme) {
        theme = theme === 'dark' ? 'dark' : 'light';
        applyTheme(theme);
        var currentPath = window.location.pathname || '/';
        if (currentPath !== '/') {
            deleteCookie('dark_theme', currentPath);
        }
        writeCookie('dark_theme', theme === 'dark' ? 'dark-theme' : 'root', 365);
        try {
            localStorage.setItem('sanqi_theme', theme);
        } catch (e) {}
        return theme;
    }

    function toggleTheme(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
            if (event.stopImmediatePropagation) {
                event.stopImmediatePropagation();
            }
        }
        return setTheme(document.body && document.body.classList.contains('dark-theme') ? 'light' : 'dark');
    }

    function initThemeToggle() {
        applyTheme(preferredTheme());
        var btn = id('day');
        if (!btn || btn.__sanqiThemeBound) {
            return;
        }
        btn.__sanqiThemeBound = true;
        btn.addEventListener('click', function (event) {
            toggleTheme(event);
        });
    }

    Sanqi.id = id;
    Sanqi.qs = qs;
    Sanqi.qsa = qsa;
    Sanqi.csrfToken = csrfToken;
    Sanqi.apiPost = apiPost;
    Sanqi.openModal = openModal;
    Sanqi.closeModal = closeModal;
    Sanqi.isScrollAtBottom = isScrollAtBottom;
    Sanqi.lazyLoad = lazyLoad;
    Sanqi.scrollHeader = scrollHeader;
    Sanqi.readCookie = readCookie;
    Sanqi.writeCookie = writeCookie;
    Sanqi.applyTheme = applyTheme;
    Sanqi.setTheme = setTheme;
    Sanqi.toggleTheme = toggleTheme;
    Sanqi.initThemeToggle = initThemeToggle;

    window.Sanqi = Sanqi;
    window.isScrollAtBottom = isScrollAtBottom;
    window.loaddemand = lazyLoad;
    window.dayModeToggle = toggleTheme;

    // 非懒加载图片加载失败时添加错误样式
    document.addEventListener('error', function (e) {
        var img = e.target;
        if (img.tagName === 'IMG' && !img.classList.contains('img-error') && !img.getAttribute('data-src')) {
            var fallback = img.getAttribute('data-fallback');
            if (fallback && img.src.indexOf(fallback) === -1) {
                img.src = fallback;
                return;
            }
            img.onerror = null;
            img.src = '/assets/img/img-load-error.svg';
            img.classList.add('img-error');
        }
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeToggle);
    } else {
        initThemeToggle();
    }
})(window, document);
