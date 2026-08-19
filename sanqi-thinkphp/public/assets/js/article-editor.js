/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
(function (window, document) {
    'use strict';

    var previewTimer = null;
    var autosaveTimer = null;
    var dirty = false;
    var previewVisible = false;
    var autosaveBusy = false;

    function id(name) {
        return document.getElementById(name);
    }

    function isArticleMode() {
        var typeInput = id('lxmk');
        return !!(typeInput && typeInput.value === '4');
    }

    function textValue(name) {
        var el = id(name);
        return el ? el.value : '';
    }

    function setText(name, value) {
        var el = id(name);
        if (!el) return;
        el.value = value == null ? '' : String(value);
        var event;
        try {
            event = new Event('input', { bubbles: true });
            el.dispatchEvent(event);
        } catch (e) {}
    }

    function setStatus(text) {
        var el = id('articleAutosaveStatus');
        if (el) el.textContent = text;
    }

    function responseOk(res) {
        return res && (res.code === 200 || res.code === '200');
    }

    function apiGet(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            var res = null;
            try { res = JSON.parse(xhr.responseText); } catch (e) {}
            if (callback) callback(res || {});
        };
        xhr.send();
    }

    function serializeForm() {
        var form = id('edit-form');
        if (!form) return {};
        var data = {};
        Array.prototype.slice.call(form.elements).forEach(function (el) {
            if (!el.name || el.disabled || el.type === 'file') return;
            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
            var value = el.value == null ? '' : String(el.value);
            if (value.indexOf('data:image/') === 0 && value.length > 120000) return;
            if (data[el.name] === undefined) {
                data[el.name] = value;
            } else if (Array.isArray(data[el.name])) {
                data[el.name].push(value);
            } else {
                data[el.name] = [data[el.name], value];
            }
        });
        return data;
    }

    function applyFormData(data) {
        if (!data || typeof data !== 'object') return;
        Object.keys(data).forEach(function (name) {
            var els = document.querySelectorAll('[name="' + cssEscape(name) + '"]');
            if (!els.length) return;
            var values = Array.isArray(data[name]) ? data[name].map(String) : [String(data[name] == null ? '' : data[name])];
            Array.prototype.slice.call(els).forEach(function (el, index) {
                if (el.type === 'file') return;
                if (el.type === 'radio' || el.type === 'checkbox') {
                    el.checked = values.indexOf(el.value) !== -1;
                } else {
                    el.value = values[Math.min(index, values.length - 1)] || '';
                }
                try { el.dispatchEvent(new Event('input', { bubbles: true })); } catch (e) {}
                try { el.dispatchEvent(new Event('change', { bubbles: true })); } catch (e) {}
            });
        });
        dirty = true;
        updatePreviewSoon();
    }

    function cssEscape(value) {
        if (window.CSS && CSS.escape) return CSS.escape(value);
        return String(value).replace(/"/g, '\\"');
    }

    function updatePreviewSoon() {
        if (!previewVisible || !isArticleMode()) return;
        clearTimeout(previewTimer);
        previewTimer = setTimeout(updatePreview, 350);
    }

    function updatePreview() {
        var body = id('articlePreviewBody');
        var status = id('articlePreviewStatus');
        if (!body || !window.Sanqi || !Sanqi.apiPost) return;
        if (status) status.textContent = '预览更新中...';
        Sanqi.apiPost('/api/article/markdown-preview', { text: textValue('bletext') }, function (res) {
            if (!responseOk(res)) {
                if (status) status.textContent = (res && res.msg) || '预览失败';
                return;
            }
            body.innerHTML = (res.data && res.data.html) || '';
            if (status) status.textContent = '已更新';
        }, function () {
            if (status) status.textContent = '预览失败';
        });
    }

    function togglePreview() {
        var panel = id('articlePreviewPanel');
        if (!panel) return;
        previewVisible = panel.style.display === 'none' || panel.style.display === '';
        panel.style.display = previewVisible ? 'block' : 'none';
        if (previewVisible) updatePreview();
    }

    function markDirty() {
        dirty = true;
        updatePreviewSoon();
    }

    function autosaveNow(force) {
        if (autosaveBusy || (!dirty && !force)) return;
        if (!window.Sanqi || !Sanqi.apiPost) return;
        var draftKey = textValue('article_draft_key');
        if (!draftKey) return;

        var payload = JSON.stringify(serializeForm());
        if (payload.length > 290000) {
            setStatus('内容较大，已跳过本次自动保存');
            return;
        }

        autosaveBusy = true;
        setStatus('自动保存中...');
        Sanqi.apiPost('/api/article/autosave-draft', {
            draft_key: draftKey,
            cid: textValue('edwzicd'),
            title: textValue('article_title') || textValue('bletext').slice(0, 40),
            payload: payload
        }, function (res) {
            autosaveBusy = false;
            if (responseOk(res)) {
                if (res.data && res.data.enabled === false) {
                    setStatus(res.msg || '自动保存未启用');
                    return;
                }
                dirty = false;
                var now = new Date();
                setStatus('已自动保存 ' + pad(now.getHours()) + ':' + pad(now.getMinutes()));
            } else {
                setStatus((res && res.msg) || '自动保存失败');
            }
        }, function () {
            autosaveBusy = false;
            setStatus('自动保存失败');
        });
    }

    function pad(num) {
        return num < 10 ? '0' + num : String(num);
    }

    function loadDraftVersions() {
        var list = id('articleDraftVersions');
        var draftKey = textValue('article_draft_key');
        if (!list || !draftKey) return;
        list.style.display = 'block';
        list.innerHTML = '<div class="sh-article-draft-item"><span>加载中...</span></div>';
        apiGet('/api/article/draft-versions?draft_key=' + encodeURIComponent(draftKey), function (res) {
            var versions = responseOk(res) && res.data ? (res.data.versions || []) : [];
            if (!versions.length) {
                list.innerHTML = '<div class="sh-article-draft-item"><span>暂无自动保存版本</span></div>';
                return;
            }
            list.innerHTML = versions.map(function (item, index) {
                return '<div class="sh-article-draft-item" data-draft-index="' + index + '">' +
                    '<span title="' + html(item.title || '未命名草稿') + '">' + html(item.title || '未命名草稿') + '</span>' +
                    '<small>' + html(item.created_at || '') + '</small>' +
                    '<button type="button">恢复</button>' +
                    '</div>';
            }).join('');
            Array.prototype.slice.call(list.querySelectorAll('.sh-article-draft-item')).forEach(function (row) {
                var index = parseInt(row.getAttribute('data-draft-index'), 10);
                var btn = row.querySelector('button');
                if (btn) {
                    btn.onclick = function () {
                        if (window.confirm && !window.confirm('恢复这个自动保存版本？当前未保存内容会被覆盖。')) return;
                        applyFormData(versions[index].form_data || {});
                        setStatus('已恢复自动保存版本');
                    };
                }
            });
        });
    }

    function html(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function currentCoverSource() {
        var uploadInput = document.querySelector('input[name="article_cover_image[]"]');
        if (uploadInput && uploadInput.value) return { type: 'upload', value: uploadInput.value, input: uploadInput };
        var url = textValue('article_cover');
        return url ? { type: 'url', value: url, input: id('article_cover') } : null;
    }

    function loadImage(src, callback, errorCallback) {
        var img = new Image();
        img.onload = function () { callback(img); };
        img.onerror = function () { if (errorCallback) errorCallback(); };
        if (src.indexOf('data:') !== 0) img.crossOrigin = 'anonymous';
        img.src = src;
    }

    function cropCover() {
        var source = currentCoverSource();
        if (!source) {
            if (window.warnpop) warnpop('请先上传或填写封面图');
            return;
        }
        loadImage(source.value, function (img) {
            var ratio = 16 / 10;
            var srcW = img.naturalWidth || img.width;
            var srcH = img.naturalHeight || img.height;
            var cropW = srcW;
            var cropH = Math.round(cropW / ratio);
            if (cropH > srcH) {
                cropH = srcH;
                cropW = Math.round(cropH * ratio);
            }
            var sx = Math.max(0, Math.round((srcW - cropW) / 2));
            var sy = Math.max(0, Math.round((srcH - cropH) / 2));
            var canvas = document.createElement('canvas');
            canvas.width = 1280;
            canvas.height = 800;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, sx, sy, cropW, cropH, 0, 0, canvas.width, canvas.height);
            try {
                var cropped = canvas.toDataURL('image/jpeg', 0.88);
                if (source.type === 'upload' && source.input) {
                    source.input.value = cropped;
                } else {
                    ensureCoverUploadInput(cropped);
                }
                updateCoverPreview(cropped);
                setCoverColorFromCanvas(canvas);
                if (window.successpop) successpop('封面已裁剪');
                dirty = true;
                autosaveNow(false);
            } catch (e) {
                if (window.warnpop) warnpop('外链图片无法在浏览器内裁剪，请改用上传封面');
            }
        }, function () {
            if (window.warnpop) warnpop('封面图加载失败');
        });
    }

    function updateCoverPreview(src) {
        var box = id('article-cover-upload');
        if (!box) return;
        var img = box.querySelector('.cupload-image-preview');
        if (img) {
            img.src = src;
            img.setAttribute('data-src', src);
        }
    }

    function ensureCoverUploadInput(src) {
        var input = document.querySelector('input[name="article_cover_image[]"]');
        if (input) {
            input.value = src;
            return input;
        }
        var box = id('article-cover-upload');
        if (!box) return null;
        if (typeof window.setArticleCoverMode === 'function') {
            window.setArticleCoverMode('upload');
        }
        var list = box.querySelector('.cupload-image-list') || box;
        var holder = document.createElement('li');
        holder.className = 'cupload-image-box';
        holder.style.cssText = 'width:128px;height:88px;position:relative;display:inline-block;margin-left:15px;';
        var img = document.createElement('img');
        img.className = 'cupload-image-preview';
        img.src = src;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;pointer-events:none;';
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'article_cover_image[]';
        input.value = src;
        holder.appendChild(img);
        holder.appendChild(input);
        list.appendChild(holder);
        return input;
    }

    function pickCoverColor() {
        var source = currentCoverSource();
        if (!source) {
            if (window.warnpop) warnpop('请先上传或填写封面图');
            return;
        }
        loadImage(source.value, function (img) {
            var canvas = document.createElement('canvas');
            canvas.width = 80;
            canvas.height = 50;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            try {
                setCoverColorFromCanvas(canvas);
                if (window.successpop) successpop('已提取封面主色');
            } catch (e) {
                if (window.warnpop) warnpop('外链图片无法提取主色，请改用上传封面或手动选择');
            }
        }, function () {
            if (window.warnpop) warnpop('封面图加载失败');
        });
    }

    function setCoverColorFromCanvas(canvas) {
        var ctx = canvas.getContext('2d');
        var img = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var r = 0, g = 0, b = 0, count = 0;
        for (var i = 0; i < img.data.length; i += 16) {
            if (img.data[i + 3] < 180) continue;
            r += img.data[i];
            g += img.data[i + 1];
            b += img.data[i + 2];
            count++;
        }
        if (!count) return;
        var color = '#' + hex(Math.round(r / count)) + hex(Math.round(g / count)) + hex(Math.round(b / count));
        setText('cover_color', color);
        setText('cover_color_picker', color);
    }

    function hex(num) {
        var s = Math.max(0, Math.min(255, num)).toString(16);
        return s.length === 1 ? '0' + s : s;
    }

    function init() {
        var form = id('edit-form');
        if (!form) return;
        form.addEventListener('input', markDirty, true);
        form.addEventListener('change', markDirty, true);
        if (id('articleDraftPanel')) {
            autosaveTimer = setInterval(function () { autosaveNow(false); }, 30000);
            window.addEventListener('beforeunload', function () {
                if (dirty) autosaveNow(true);
                if (autosaveTimer) clearInterval(autosaveTimer);
            });
            setTimeout(function () { autosaveNow(true); }, 5000);
        }
    }

    window.ArticleEditor = {
        togglePreview: togglePreview,
        cropCover: cropCover,
        pickCoverColor: pickCoverColor,
        loadDraftVersions: loadDraftVersions,
        autosaveNow: autosaveNow
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);
