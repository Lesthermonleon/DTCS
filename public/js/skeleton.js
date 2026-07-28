/**
 * DITC HMS — Skeleton Loading System
 *
 * Usage:
 *   SkeletonLoader.show('#my-skeleton');
 *   SkeletonLoader.hide('#my-skeleton', '#my-content');
 *   SkeletonLoader.autoReveal(200);  // auto-reveal all on DOMContentLoaded
 */
(function (global) {
    'use strict';

    /* ── Internal helpers ── */
    function qs(selector, ctx) {
        return (ctx || document).querySelector(selector);
    }
    function qsAll(selector, ctx) {
        return Array.from((ctx || document).querySelectorAll(selector));
    }

    /* ── SkeletonLoader public API ── */
    var SkeletonLoader = {

        /**
         * Show a skeleton and hide its corresponding content.
         * @param {string|Element} skeletonEl
         * @param {string|Element} [contentEl]
         */
        show: function (skeletonEl, contentEl) {
            var sk = typeof skeletonEl === 'string' ? qs(skeletonEl) : skeletonEl;
            var ct = contentEl
                ? (typeof contentEl === 'string' ? qs(contentEl) : contentEl)
                : null;

            if (sk) {
                sk.classList.remove('sk-hidden');
                sk.setAttribute('aria-hidden', 'false');
                sk.setAttribute('aria-busy', 'true');
            }
            if (ct) {
                ct.classList.remove('sk-loaded');
                ct.classList.add('sk-hidden');
            }
        },

        /**
         * Hide a skeleton and reveal its content.
         * @param {string|Element} skeletonEl
         * @param {string|Element} [contentEl]
         */
        hide: function (skeletonEl, contentEl) {
            var sk = typeof skeletonEl === 'string' ? qs(skeletonEl) : skeletonEl;
            var ct = contentEl
                ? (typeof contentEl === 'string' ? qs(contentEl) : contentEl)
                : null;

            if (sk) {
                sk.classList.add('sk-hidden');
                sk.setAttribute('aria-hidden', 'true');
                sk.setAttribute('aria-busy', 'false');
            }
            if (ct) {
                ct.classList.remove('sk-hidden');
                ct.classList.add('sk-loaded');
            }
        },

        /**
         * Toggle skeleton visibility.
         */
        toggle: function (skeletonEl, contentEl, show) {
            if (show) {
                this.show(skeletonEl, contentEl);
            } else {
                this.hide(skeletonEl, contentEl);
            }
        },

        /**
         * Automatically reveal all [data-skeleton] / [data-skeleton-content] pairs
         * after `delay` milliseconds. Useful for simulating async on page load.
         *
         * Pairs are matched by a shared `data-skeleton-id` attribute.
         *
         * @param {number} [delay=0]
         */
        autoReveal: function (delay) {
            var self = this;
            setTimeout(function () {
                // Reveal any standalone content panels
                qsAll('.sk-content, .sk-content-flex').forEach(function (el) {
                    if (!el.classList.contains('sk-loaded')) {
                        el.classList.add('sk-loaded');
                    }
                });

                // Hide paired skeletons
                qsAll('[data-skeleton]').forEach(function (skEl) {
                    var id = skEl.dataset.skeleton;
                    var ctEl = qs('[data-skeleton-content="' + id + '"]');
                    self.hide(skEl, ctEl);
                });
            }, delay || 0);
        },

        /**
         * Fetch-aware wrapper — shows skeleton before fetch, hides after.
         *
         * @param {string} url
         * @param {string|Element} skeletonEl
         * @param {string|Element} contentEl
         * @param {function} renderCallback  fn(data) — populates contentEl with response data
         * @param {object} [fetchOptions]
         */
        fetchAndReveal: function (url, skeletonEl, contentEl, renderCallback, fetchOptions) {
            var self = this;
            self.show(skeletonEl, contentEl);

            fetch(url, fetchOptions || {})
                .then(function (res) {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(function (data) {
                    if (typeof renderCallback === 'function') {
                        renderCallback(data);
                    }
                    self.hide(skeletonEl, contentEl);
                })
                .catch(function (err) {
                    console.error('[SkeletonLoader] Fetch error:', err);
                    self.hide(skeletonEl, contentEl);
                });
        },

        /**
         * Generate an inline table skeleton dynamically.
         * Useful for AJAX table refreshes.
         *
         * @param {Element} tableBody     — <tbody> element to inject skeletons into
         * @param {number}  [rows=5]      — number of skeleton rows
         * @param {number}  [cols=4]      — number of columns
         */
        injectTableRows: function (tableBody, rows, cols) {
            rows = rows || 5;
            cols = cols || 4;
            tableBody.innerHTML = '';
            for (var r = 0; r < rows; r++) {
                var tr = document.createElement('tr');
                for (var c = 0; c < cols; c++) {
                    var td = document.createElement('td');
                    var div = document.createElement('div');
                    div.className = 'sk sk-md sk-w-' + (c === 0 ? '50' : c === cols - 1 ? '25' : '75');
                    td.appendChild(div);
                    tr.appendChild(td);
                }
                tableBody.appendChild(tr);
            }
        },

        /**
         * Replace a container's content with a paragraph-style text skeleton.
         */
        injectTextBlock: function (container, lines) {
            lines = lines || 4;
            var widths = ['100', '75', '100', '50', '66', '100', '33'];
            var html = '';
            for (var i = 0; i < lines; i++) {
                var w = widths[i % widths.length];
                html += '<div class="sk sk-md sk-w-' + w + '" style="margin-bottom:.6rem;"></div>';
            }
            container.innerHTML = html;
        }
    };

    /* ── Auto-init on DOMContentLoaded ── */
    document.addEventListener('DOMContentLoaded', function () {
        // Any element with [data-sk-auto-reveal="N"] auto-reveals after N ms
        qsAll('[data-sk-auto-reveal]').forEach(function (el) {
            var delay = parseInt(el.dataset.skAutoReveal, 10) || 300;
            SkeletonLoader.autoReveal(delay);
        });
    });

    /* ── Export ── */
    global.SkeletonLoader = SkeletonLoader;

}(window));
