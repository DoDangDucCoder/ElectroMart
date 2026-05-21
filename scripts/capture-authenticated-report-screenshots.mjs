import { spawn } from 'node:child_process';
import { mkdir, writeFile } from 'node:fs/promises';
import { Buffer } from 'node:buffer';

const base = 'http://127.0.0.1:8000';
const edge = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const outDir = 'docs/report/screenshots-auth-current';
const port = 9444;

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function waitJson(path, timeout = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeout) {
    try {
      const res = await fetch(`http://127.0.0.1:${port}${path}`);
      if (res.ok) return await res.json();
    } catch {}
    await sleep(300);
  }
  throw new Error(`CDP not ready: ${path}`);
}

function getSetCookies(headers) {
  if (typeof headers.getSetCookie === 'function') return headers.getSetCookie();
  const raw = headers.get('set-cookie');
  return raw ? [raw] : [];
}

async function http(path, options = {}, jar = new Map()) {
  const headers = new Headers(options.headers || {});
  if (jar.size) headers.set('cookie', [...jar].map(([k, v]) => `${k}=${v}`).join('; '));
  const res = await fetch(`${base}${path}`, { ...options, headers, redirect: 'manual' });
  for (const line of getSetCookies(res.headers)) {
    const [pair] = line.split(';');
    const eq = pair.indexOf('=');
    if (eq > 0) jar.set(pair.slice(0, eq), pair.slice(eq + 1));
  }
  return { res, text: await res.text() };
}

const tokenOf = (html) => {
  const m = html.match(/name="_token" value="([^"]+)"/);
  if (!m) throw new Error('CSRF token not found');
  return m[1];
};

async function loginAdmin() {
  const jar = new Map();
  const page = await http('/admin', {}, jar);
  await http('/auth-login', {
    method: 'POST',
    headers: { 'content-type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ _token: tokenOf(page.text), email: 'admin@shop.com', password: 'admin123' }),
  }, jar);
  return jar;
}

async function loginCustomer() {
  const jar = new Map();
  const page = await http('/dang-nhap', {}, jar);
  await http('/login-customer', {
    method: 'POST',
    headers: { 'content-type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ _token: tokenOf(page.text), email_account: 'report.customer@example.com', password_account: 'password123' }),
  }, jar);
  return jar;
}

async function connect(wsUrl) {
  const ws = new WebSocket(wsUrl);
  await new Promise((resolve, reject) => {
    const timer = setTimeout(() => reject(new Error('WebSocket open timeout')), 10000);
    ws.addEventListener('open', () => { clearTimeout(timer); resolve(); }, { once: true });
    ws.addEventListener('error', reject, { once: true });
  });
  let id = 0;
  const send = (method, params = {}) => new Promise((resolve, reject) => {
    const msgId = ++id;
    const timer = setTimeout(() => {
      ws.removeEventListener('message', onMessage);
      reject(new Error(`CDP timeout: ${method}`));
    }, 20000);
    function onMessage(event) {
      const data = JSON.parse(event.data);
      if (data.id !== msgId) return;
      clearTimeout(timer);
      ws.removeEventListener('message', onMessage);
      data.error ? reject(new Error(data.error.message)) : resolve(data.result);
    }
    ws.addEventListener('message', onMessage);
    ws.send(JSON.stringify({ id: msgId, method, params }));
  });
  return { ws, send };
}

async function capture(jar, path, filename) {
  const tab = await fetch(`http://127.0.0.1:${port}/json/new?${encodeURIComponent(base + path)}`, { method: 'PUT' }).then((r) => r.json());
  const { ws, send } = await connect(tab.webSocketDebuggerUrl);
  try {
    await send('Network.enable');
    await send('Page.enable');
    await send('Network.setCookies', {
      cookies: [...jar].map(([name, value]) => ({ name, value, url: base, domain: '127.0.0.1', path: '/' })),
    });
    await send('Emulation.setDeviceMetricsOverride', { width: 1366, height: 900, deviceScaleFactor: 1, mobile: false });
    await send('Page.navigate', { url: `${base}${path}` });
    await sleep(2500);
    const shot = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: false });
    await writeFile(`${outDir}/${filename}`, Buffer.from(shot.data, 'base64'));
  } finally {
    ws.close();
    await fetch(`http://127.0.0.1:${port}/json/close/${tab.id}`).catch(() => {});
  }
}

await mkdir(outDir, { recursive: true });
const browser = spawn(edge, [
  '--headless=new',
  '--disable-gpu',
  '--disable-software-rasterizer',
  '--no-sandbox',
  '--disable-dev-shm-usage',
  '--hide-scrollbars',
  '--window-size=1366,900',
  `--remote-debugging-port=${port}`,
  'about:blank',
], { stdio: 'ignore' });

try {
  await waitJson('/json/version');
  const admin = await loginAdmin();
  const customer = await loginCustomer();

  const adminPages = [
    ['/dashboard', '01-admin-dashboard.png'],
    ['/admin/products-management', '02-admin-quan-ly-san-pham.png'],
    ['/admin/orders-management', '03-admin-quan-ly-don-hang.png'],
    ['/admin/customers', '04-admin-quan-ly-khach-hang.png'],
    ['/admin/categories-management', '05-admin-quan-ly-danh-muc.png'],
    ['/admin/inventory', '06-admin-quan-ly-ton-kho.png'],
    ['/admin/reviews', '07-admin-duyet-danh-gia.png'],
    ['/admin/returns', '08-admin-quan-ly-tra-hang.png'],
    ['/admin/transactions', '09-admin-giao-dich-thanh-toan.png'],
    ['/admin/contact-messages', '10-admin-lien-he-khach-hang.png'],
    ['/chat-inbox', '11-admin-hop-thoai-ho-tro.png'],
    ['/users', '12-admin-quan-ly-nguoi-dung.png'],
    ['/admin/settings', '13-admin-cai-dat-he-thong.png'],
  ];

  const customerPages = [
    ['/account', '14-customer-tong-quan-tai-khoan.png'],
    ['/account/profile', '15-customer-ho-so-ca-nhan.png'],
    ['/account/orders', '16-customer-quan-ly-don-hang.png'],
    ['/account/purchase-history', '17-customer-lich-su-mua-hang.png'],
    ['/account/addresses', '18-customer-dia-chi-giao-hang.png'],
    ['/account/wishlist', '19-customer-san-pham-yeu-thich.png'],
    ['/account/vouchers', '20-customer-ma-giam-gia.png'],
    ['/account/payment-methods', '21-customer-phuong-thuc-thanh-toan.png'],
    ['/account/reviews', '22-customer-danh-gia-san-pham.png'],
    ['/account/returns', '23-customer-yeu-cau-tra-hang.png'],
    ['/account/invoices', '24-customer-hoa-don-vat.png'],
    ['/account/recently-viewed', '25-customer-san-pham-da-xem.png'],
    ['/account/change-password', '26-customer-doi-mat-khau.png'],
  ];

  for (const [path, file] of adminPages) await capture(admin, path, file);
  for (const [path, file] of customerPages) await capture(customer, path, file);
} finally {
  browser.kill();
}
