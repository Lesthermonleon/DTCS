/**
 * DITC HIMS — Rainbow Christmas Flashy Prank Theme
 * Client-Side Visual Effects Script (0% Backend/Functional Impact)
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initChristmasLights();
        initToggleButton();
        initClickFireworks();
    });

    // ── 1. CHRISTMAS STRING LIGHTS GENERATOR ──
    function initChristmasLights() {
        if (document.getElementById('prank-lights-top')) return;

        const colors = ['#ff0055', '#ffaa00', '#33cc33', '#00ccff', '#d500f9', '#ff4081', '#ffea00', '#00e676'];

        // TOP STRING LIGHTS
        const topContainer = document.createElement('div');
        topContainer.id = 'prank-lights-top';
        topContainer.className = 'prank-lights-string prank-lights-top';
        const topCount = Math.floor(window.innerWidth / 30);
        for (let i = 0; i < topCount; i++) {
            const bulb = createBulb(colors[i % colors.length]);
            topContainer.appendChild(bulb);
        }

        // LEFT STRING LIGHTS
        const leftContainer = document.createElement('div');
        leftContainer.id = 'prank-lights-left';
        leftContainer.className = 'prank-lights-string prank-lights-left';
        const leftCount = Math.floor(window.innerHeight / 35);
        for (let i = 0; i < leftCount; i++) {
            const bulb = createBulb(colors[(i + 2) % colors.length]);
            leftContainer.appendChild(bulb);
        }

        // RIGHT STRING LIGHTS
        const rightContainer = document.createElement('div');
        rightContainer.id = 'prank-lights-right';
        rightContainer.className = 'prank-lights-string prank-lights-right';
        const rightCount = Math.floor(window.innerHeight / 35);
        for (let i = 0; i < rightCount; i++) {
            const bulb = createBulb(colors[(i + 4) % colors.length]);
            rightContainer.appendChild(bulb);
        }

        document.body.appendChild(topContainer);
        document.body.appendChild(leftContainer);
        document.body.appendChild(rightContainer);

        // Restore saved toggle state
        const savedState = localStorage.getItem('hims_christmas_lights');
        if (savedState === 'off') {
            document.body.classList.add('prank-christmas-off');
        }
    }

    function createBulb(color) {
        const bulb = document.createElement('div');
        bulb.className = 'prank-light-bulb';
        bulb.style.setProperty('--bulb-color', color);
        bulb.style.animationDelay = (Math.random() * 1.8).toFixed(2) + 's';
        return bulb;
    }

    // ── 2. CHRISTMAS LIGHTS TOGGLE BUTTON ──
    function initToggleButton() {
        if (document.getElementById('prank-christmas-toggle-btn')) return;

        const btn = document.createElement('button');
        btn.id = 'prank-christmas-toggle-btn';
        btn.className = 'prank-christmas-toggle';
        btn.type = 'button';
        btn.title = 'Christmas Lights';
        btn.setAttribute('aria-label', 'Toggle Christmas Lights');
        btn.innerHTML = '<i class="bi bi-lightbulb-fill me-1"></i><span class="d-none d-sm-inline">Lights</span>';

        // Try inserting into topbar right actions if present, otherwise append fixed top-right
        const topbarRight = document.querySelector('.navbar .ms-auto, .app-header .ms-auto, .topbar .ms-auto');
        if (topbarRight) {
            topbarRight.insertBefore(btn, topbarRight.firstChild);
        } else {
            document.body.appendChild(btn);
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOff = document.body.classList.toggle('prank-christmas-off');
            localStorage.setItem('hims_christmas_lights', isOff ? 'off' : 'on');
        });
    }

    // ── 3. MOUSE CLICK FIREWORKS BURST SYSTEM ──
    function initClickFireworks() {
        document.addEventListener('click', function (e) {
            // Check prefers-reduced-motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            const x = e.clientX;
            const y = e.clientY;

            const burst = document.createElement('div');
            burst.className = 'prank-firework-burst';
            burst.style.left = x + 'px';
            burst.style.top = y + 'px';

            const colors = ['#ff0055', '#ff9900', '#ffee00', '#33cc33', '#00ccff', '#0044ff', '#9900ff', '#ff00cc'];
            const particleCount = 14;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'prank-particle';

                const angle = (i / particleCount) * 360 + (Math.random() * 15 - 7.5);
                const distance = Math.floor(Math.random() * 50 + 25);
                const color = colors[Math.floor(Math.random() * colors.length)];
                const size = Math.floor(Math.random() * 5 + 4);

                const rad = (angle * Math.PI) / 180;
                const tx = Math.cos(rad) * distance;
                const ty = Math.sin(rad) * distance;

                particle.style.setProperty('--tx', tx + 'px');
                particle.style.setProperty('--ty', ty + 'px');
                particle.style.setProperty('--particle-color', color);
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';

                burst.appendChild(particle);
            }

            document.body.appendChild(burst);

            // Self cleanup
            setTimeout(function () {
                if (burst && burst.parentNode) {
                    burst.parentNode.removeChild(burst);
                }
            }, 750);
        }, { passive: true });
    }
})();
