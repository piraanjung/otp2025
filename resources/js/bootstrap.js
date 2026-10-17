window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * --------------------------------------------------------------------------
 * Laravel Reverb Configuration (WebSocket)
 * --------------------------------------------------------------------------
 * ตั้งค่าส่วนนี้เพื่อให้ Frontend รับข้อมูลจาก ESP32 แบบ Real-time
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'reverb',
    // key: process.env.MIX_REVERB_APP_KEY, // ถ้าใช้ env ต้องขึ้นต้นด้วย MIX_ แต่ใส่ hardcode ไปก่อนเพื่อให้ชัวร์
    key: 'wcbzsiztpvbn2vjytxqa',         // ค่า Default ของ Reverb (ถ้าแก้ใน .env ให้แก้ตรงนี้ตาม)
    wsHost: window.location.hostname, // ให้ใช้ IP ของเครื่องที่เปิดเว็บ (เช่น 192.168.x.x)
    wsPort: 8080,           // Port มาตรฐานของ Reverb
    wssPort: 8080,
    forceTLS: false,        // ปิด SSL เพราะเราเทส Local
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});

console.log('✅ Reverb Connected on Port 8080');

/**
 * บรรทัดข้างล่างนี้ของเดิมมัน import './echo';
 * เรา comment ปิดไว้ครับ เพราะเราตั้งค่าข้างบนนี้จบแล้ว
 */
// import './echo';
