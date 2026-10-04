{{--
  ไฮไลต์เมนูใน sidebar ตาม URL ที่เปิดอยู่ (ใช้กับ layout ที่เมนูเก่าใช้ @yield('nav-xxx') ซึ่งแต่ละ view ต้องตั้งเอง
  และหลายเมนูใช้ชื่อ section ซ้ำกัน จึงไฮไลต์ผิด/ไม่ไฮไลต์)

  กติกา:
  1) เลือกลิงก์ที่ path ตรงกับหน้าปัจจุบันที่สุด (ตรงเป๊ะ > เป็น prefix ที่ยาวที่สุด เช่น /invoice/12/zone_edit -> /invoice)
  2) ลิงก์ใน <form action> ที่เป็นปุ่มเมนู (เมนูที่ใช้ POST) นับด้วย
  3) เปิดกลุ่ม (collapse) ที่มีเมนูนั้นอยู่ และปิดกลุ่มอื่น
  4) ไม่แตะปุ่มพิเศษ (bg-warning / bg-info / ปุ่ม Log Out)
--}}
<script>
    (function () {
        var nav = document.getElementById('sidenav-collapse-main');
        if (!nav) { return; }

        function norm(p) {
            p = (p || '').split('?')[0].split('#')[0];
            return p.length > 1 ? p.replace(/\/+$/, '') : p;
        }
        function pathOf(url) {
            try {
                var u = new URL(url, location.href);
                return u.origin === location.origin ? norm(u.pathname) : null;
            } catch (e) { return null; }
        }

        var items = [];
        nav.querySelectorAll('a.nav-link[href]').forEach(function (a) {
            var h = a.getAttribute('href');
            if (!h || h.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(h)) { return; }
            var p = pathOf(h);
            if (p) { items.push({ el: a, path: p }); }
        });
        nav.querySelectorAll('form[action] button.nav-link').forEach(function (b) {
            var p = pathOf(b.closest('form').getAttribute('action'));
            if (p) { items.push({ el: b, path: p }); }
        });

        var here = norm(location.pathname);
        var best = null, bestScore = 0;
        items.forEach(function (it) {
            if (it.path === '/' || it.path === '' || /\/logout$/.test(it.path)) { return; }
            var s = 0;
            if (here === it.path) { s = 10000 + it.path.length; }
            else if (here.indexOf(it.path + '/') === 0) { s = it.path.length; }
            if (s > bestScore) { bestScore = s; best = it.el; }
        });
        if (!best) { return; }   // หน้าที่ไม่อยู่ในเมนู: คงสถานะเดิมไว้

        var special = '.bg-warning, .bg-info, .border-danger';
        nav.querySelectorAll('.nav-link.active').forEach(function (el) {
            if (!el.matches(special)) { el.classList.remove('active'); }
        });

        function toggler(collapse) { return collapse.id ? nav.querySelector('[href="#' + collapse.id + '"]') : null; }

        // ปิดกลุ่มที่ไม่เกี่ยว
        nav.querySelectorAll('.collapse.show').forEach(function (c) {
            if (c.contains(best)) { return; }
            c.classList.remove('show');
            var t = toggler(c);
            if (t) { t.classList.add('collapsed'); t.setAttribute('aria-expanded', 'false'); }
        });

        best.classList.add('active');

        // เปิดกลุ่มแม่ทุกชั้นของเมนูที่ตรงกัน
        var c = best.closest('.collapse');
        while (c) {
            c.classList.add('show');
            var t = toggler(c);
            if (t) {
                t.classList.remove('collapsed');
                t.setAttribute('aria-expanded', 'true');
                t.classList.add('active');
            }
            c = c.parentElement ? c.parentElement.closest('.collapse') : null;
        }
    })();
</script>
