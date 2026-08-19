/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
(function (window, document) {
    'use strict';

    function slug(text, index) {
        var base = String(text || '').trim().toLowerCase()
            .replace(/<[^>]+>/g, '')
            .replace(/[^\w\u4e00-\u9fa5-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        return 'toc-' + (base || 'section') + '-' + index;
    }

    function buildToc() {
        var article = document.querySelector('.sh-article-markdown');
        var toc = document.getElementById('articleToc');
        var list = document.getElementById('articleTocList');
        if (!article || !toc || !list) return;

        var headings = Array.prototype.slice.call(article.querySelectorAll('h1,h2,h3,h4,h5,h6')).filter(function (item) {
            return item.textContent.trim() !== '';
        });
        if (headings.length < 2) return;

        list.innerHTML = '';
        headings.forEach(function (heading, index) {
            if (!heading.id) heading.id = slug(heading.textContent, index + 1);
            var level = parseInt(heading.tagName.replace('H', ''), 10);
            var li = document.createElement('li');
            li.className = 'toc-l' + level;
            var a = document.createElement('a');
            a.href = '#' + heading.id;
            a.textContent = heading.textContent.trim();
            a.onclick = function (event) {
                event.preventDefault();
                heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.replaceState(null, '', '#' + heading.id);
            };
            li.appendChild(a);
            list.appendChild(li);
        });
        toc.style.display = 'block';
    }

    function updateProgress() {
        var bar = document.getElementById('articleReadProgress');
        var article = document.querySelector('.sh-article-detail');
        if (!bar || !article) return;

        var rect = article.getBoundingClientRect();
        var scrollable = Math.max(1, rect.height - window.innerHeight);
        var read = Math.min(scrollable, Math.max(0, -rect.top));
        var percent = Math.max(0, Math.min(100, read / scrollable * 100));
        bar.style.width = percent.toFixed(2) + '%';
    }

    function init() {
        buildToc();
        updateProgress();
        window.addEventListener('scroll', updateProgress, { passive: true });
        window.addEventListener('resize', updateProgress);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);
