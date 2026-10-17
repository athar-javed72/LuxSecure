import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const outDir = path.join(root, 'storage', 'app', 'qa-screenshots');
const base = process.env.QA_BASE_URL || 'http://127.0.0.1:8000';
const proofPath = path.join(outDir, 'proof.png');

fs.mkdirSync(outDir, { recursive: true });

const results = [];
const consoleByStep = {};

function slugify(p) {
  return p.replace(/^\//, '').replace(/\//g, '__') || 'home';
}

async function shot(page, name, width) {
  await page.setViewportSize({ width, height: width === 375 ? 812 : 900 });
  await page.waitForTimeout(400);
  const file = path.join(outDir, `${name}-${width}.png`);
  await page.screenshot({ path: file, fullPage: true });
  return file;
}

async function withConsole(page, step, fn) {
  const logs = [];
  const onConsole = (msg) => {
    if (['error', 'warning'].includes(msg.type())) {
      logs.push(`[${msg.type()}] ${msg.text()}`);
    }
  };
  const onPageError = (err) => logs.push(`[pageerror] ${err.message}`);
  page.on('console', onConsole);
  page.on('pageerror', onPageError);
  try {
    await fn();
    results.push({ step, status: 'PASS', error: null, console: logs });
  } catch (e) {
    results.push({ step, status: 'FAIL', error: String(e.message || e), console: logs });
  } finally {
    page.off('console', onConsole);
    page.off('pageerror', onPageError);
    consoleByStep[step] = logs;
  }
}

async function login(page, email, password) {
  await page.goto(`${base}/login`, { waitUntil: 'networkidle' });
  const form = page.locator('form').filter({ has: page.locator('input[name="email"]') }).first();
  await form.locator('input[name="email"]').fill(email);
  await form.locator('input[name="password"]').fill(password);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
    form.locator('button[type="submit"], button:has-text("Login")').first().click(),
  ]);
  if (page.url().includes('two-factor')) {
    const otpPath = path.join(outDir, 'admin-otp.txt');
    if (!fs.existsSync(otpPath)) throw new Error('2FA challenge but missing admin-otp.txt');
    const code = fs.readFileSync(otpPath, 'utf8').trim();
    await page.locator('input[name="code"], input[name="one_time_password"]').first().fill(code);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
      page.locator('button[type="submit"], button:has-text("Verify")').first().click(),
    ]);
    if (page.url().includes('two-factor')) {
      throw new Error('2FA verify failed');
    }
  }
}

const screenshotPaths = [];

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext();
const page = await context.newPage();

const publicPages = [
  ['/', 'home'],
  ['/properties', 'properties'],
  ['/properties/blue-area-boutique-hotel', 'hotel'],
  ['/login', 'login'],
  ['/contact', 'contact'],
];

for (const [urlPath, name] of publicPages) {
  await page.goto(`${base}${urlPath}`, { waitUntil: 'networkidle' });
  for (const w of [1440, 375]) {
    screenshotPaths.push(await shot(page, name, w));
  }
}

await login(page, 'admin@luxsecure.com', 'password');

const adminPages = [
  ['/admin', 'admin'],
  ['/admin/roles', 'admin-roles'],
  ['/admin/listings', 'admin-listings'],
  ['/admin/payments', 'admin-payments'],
];

for (const [urlPath, name] of adminPages) {
  await page.goto(`${base}${urlPath}`, { waitUntil: 'networkidle' });
  if (urlPath === '/admin/roles') {
    const matrix = page.locator('a[href*="matrix"], a:has-text("matrix"), a:has-text("Permissions")').first();
    if (await matrix.count()) {
      await matrix.click();
      await page.waitForLoadState('networkidle');
    } else {
      // try first role edit/matrix link
      const any = page.locator('a[href*="/admin/roles/"]').first();
      if (await any.count()) {
        await any.click();
        await page.waitForLoadState('networkidle');
      }
    }
    // Prefer explicit matrix URL if still on index
    if (/\/admin\/roles\/?$/.test(page.url())) {
      const href = await page.locator('a[href*="/permissions"]').first().getAttribute('href').catch(() => null);
      if (href) {
        await page.goto(href.startsWith('http') ? href : `${base}${href}`, { waitUntil: 'networkidle' });
      }
    }
  }
  for (const w of [1440, 375]) {
    screenshotPaths.push(await shot(page, name === 'admin-roles' ? 'admin-permission-matrix' : name, w));
  }
}

// —— Booking flow ——
await withConsole(page, 'open hotel listing', async () => {
  await page.goto(`${base}/properties/blue-area-boutique-hotel`, { waitUntil: 'networkidle' });
  if (!page.url().includes('blue-area-boutique-hotel')) {
    throw new Error(`Unexpected URL ${page.url()}`);
  }
  const book = page.locator('a:has-text("Book"), a[href*="book"]').first();
  if (!(await book.count())) throw new Error('Book link not found');
  await book.click();
  await page.waitForLoadState('networkidle');
});

await withConsole(page, 'pick dates and see price quote', async () => {
  const checkIn = new Date();
  checkIn.setDate(checkIn.getDate() + 21);
  const checkOut = new Date(checkIn);
  checkOut.setDate(checkOut.getDate() + 2);
  const fmt = (d) => d.toISOString().slice(0, 10);

  const inVal = fmt(checkIn);
  const outVal = fmt(checkOut);
  await page.evaluate(({ inVal, outVal }) => {
    const root = document.querySelector('[x-data="bookingQuote"]');
    const alpine = root && window.Alpine ? window.Alpine.$data(root) : null;
    if (alpine) {
      alpine.checkIn = inVal;
      alpine.checkOut = outVal;
      if (typeof alpine.fetchQuote === 'function') alpine.fetchQuote();
    }
    const cin = document.querySelector('#check_in, input[name="check_in"]');
    const cout = document.querySelector('#check_out, input[name="check_out"]');
    if (cin) { cin.value = inVal; cin.dispatchEvent(new Event('input', { bubbles: true })); cin.dispatchEvent(new Event('change', { bubbles: true })); }
    if (cout) { cout.value = outVal; cout.dispatchEvent(new Event('input', { bubbles: true })); cout.dispatchEvent(new Event('change', { bubbles: true })); }
  }, { inVal, outVal });
  await page.waitForTimeout(1500);

  const body = await page.textContent('body');
  if (!/Total|night|quote|PKR|,\d{3}/i.test(body || '')) {
    // wait for quote endpoint
    await page.waitForTimeout(1500);
  }
  const text = await page.textContent('body');
  if (!/(Total|night\(s\)|rooms? left)/i.test(text || '')) {
    throw new Error('Price quote not visible after selecting dates');
  }
  screenshotPaths.push(await shot(page, 'booking-quote', 1440));
});

await withConsole(page, 'create booking', async () => {
  const submit = page.locator('form[action*="bookings"] button[type="submit"]');
  await page.waitForFunction(() => {
    const btn = document.querySelector('form[action*="bookings"] button[type="submit"]');
    return !!btn && !btn.disabled;
  }, null, { timeout: 15000 });
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle', timeout: 20000 }).catch(() => {}),
    submit.click(),
  ]);
  const url = page.url();
  if (!/bookings\/\d+/.test(url)) {
    const err = await page.locator('.text-fs-danger, .text-red-600, [role="alert"]').first().textContent().catch(() => '');
    throw new Error(`Booking not created. URL=${url} ${err || ''}`);
  }
  screenshotPaths.push(await shot(page, 'booking-created', 1440));
});

await withConsole(page, 'upload bank proof', async () => {
  const gateway = page.locator('select[name="gateway"], input[name="gateway"][value="bank_transfer"]');
  if (await page.locator('select[name="gateway"]').count()) {
    await page.locator('select[name="gateway"]').selectOption('bank_transfer');
  }
  const ref = page.locator('input[name="bank_reference"]');
  if (await ref.count()) {
    await ref.first().fill(`QA-REF-${Date.now()}`);
  } else {
    throw new Error('bank_reference input not found');
  }
  const file = page.locator('input[type="file"][name="proof"]');
  if (!(await file.count())) throw new Error('Proof file input not found');
  if (!fs.existsSync(proofPath)) throw new Error(`Missing ${proofPath}`);
  await file.setInputFiles(proofPath);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle', timeout: 20000 }).catch(() => {}),
    page.locator('form[action*="payments"] button[type="submit"], button:has-text("Pay"), button:has-text("Submit"), button:has-text("Upload")').first().click(),
  ]);
  const body = await page.textContent('body');
  if (!/(proof|awaiting|success|pending|initiated)/i.test(body || '')) {
    throw new Error('No success/pending confirmation after proof upload');
  }
  screenshotPaths.push(await shot(page, 'booking-proof', 1440));
});

await withConsole(page, 'view My bookings', async () => {
  await page.goto(`${base}/bookings/mine`, { waitUntil: 'networkidle' });
  const body = await page.textContent('body');
  if (!/Booking|Blue Area|pending|paid/i.test(body || '')) {
    throw new Error('My bookings page empty or unexpected');
  }
  screenshotPaths.push(await shot(page, 'my-bookings', 1440));
});

await withConsole(page, 'cancel and view refund preview', async () => {
  if (!/bookings\/\d+/.test(page.url())) {
    const link = page.locator('a[href*="/bookings/"]').filter({ hasNotText: 'mine' }).first();
    if (await link.count()) {
      await link.click();
      await page.waitForLoadState('networkidle');
    } else {
      await page.goto(`${base}/bookings/mine`, { waitUntil: 'networkidle' });
      await page.locator('a[href*="/bookings/"]').first().click();
      await page.waitForLoadState('networkidle');
    }
  }
  const bodyBefore = await page.textContent('body');
  const hasPreview = /Refund preview/i.test(bodyBefore || '');
  if (!hasPreview) {
    throw new Error('Refund preview not visible before cancel');
  }
  const cancelBtn = page.locator('form[action*="cancel"] button[type="submit"]').first();
  if (await cancelBtn.count()) {
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle', timeout: 15000 }).catch(() => {}),
      cancelBtn.click(),
    ]);
  }
  const body = await page.textContent('body');
  if (!/cancelled|canceled|Refund preview/i.test(body || '')) {
    throw new Error('Cancel confirmation not found after submit');
  }
  screenshotPaths.push(await shot(page, 'booking-cancel-refund', 1440));
});

await browser.close();

const report = { base, screenshotPaths, bookingSteps: results, consoleByStep };
fs.writeFileSync(path.join(outDir, 'qa-report.json'), JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
const failed = results.filter((r) => r.status === 'FAIL');
process.exit(failed.length ? 1 : 0);
