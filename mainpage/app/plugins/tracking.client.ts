/**
 * tracking.client.ts
 *
 * Client-only plugin: injects GTM, GA4, Facebook Pixel, and TikTok Pixel
 * based on vendor marketing settings from the storefront API.
 *
 * Runs once on app load. Scripts are injected asynchronously.
 * user_id is forwarded to GA4 when the user is logged in.
 */

import { useStorefrontStore } from '~/stores/useStorefrontStore'
import { useAuthStore } from '~/stores/useAuthStore'

export default defineNuxtPlugin(async () => {
    const storefrontStore = useStorefrontStore()
    const authStore = useAuthStore()

    // Load storefront (includes marketing settings) if not already loaded
    if (!storefrontStore.storefrontData) {
        try {
            await storefrontStore.fetchStorefront()
        } catch (_) {
            return // Non-critical — fail silently
        }
    }

    const m = storefrontStore.marketing
    if (!m) return

    const userId = authStore.user?.id ? String(authStore.user.id) : undefined

    // ─── 1. Google Tag Manager ────────────────────────────────────────────────
    const gtmId = m.gtmId
    const isGtmActive = m.isGtmActive !== false // Default to true if not set
    
    if (gtmId && isGtmActive) {
        // Initialize dataLayer before GTM loads
        ; (window as any).dataLayer = (window as any).dataLayer || []
            ; (window as any).dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' })

        // Inject GTM <script async> in <head>
        const gtmScript = document.createElement('script')
        gtmScript.async = true
        gtmScript.src = `https://www.googletagmanager.com/gtm.js?id=${gtmId}`
        document.head.appendChild(gtmScript)

        // GTM noscript <iframe> at top of <body>
        const ns = document.createElement('noscript')
        const iframe = document.createElement('iframe')
        iframe.src = `https://www.googletagmanager.com/ns.html?id=${gtmId}`
        iframe.setAttribute('height', '0')
        iframe.setAttribute('width', '0')
        iframe.style.cssText = 'display:none;visibility:hidden'
        ns.appendChild(iframe)
        if (document.body.firstChild) {
            document.body.insertBefore(ns, document.body.firstChild)
        } else {
            document.body.appendChild(ns)
        }
    }

    // ─── 2. Google Analytics 4 (GA4) ─────────────────────────────────────────
    const gaMeasurementId = m.gaMeasurementId
    const isGa4Active = m.isGa4Active !== false // Default to true
    
    if (gaMeasurementId && isGa4Active) {
        const gaScript = document.createElement('script')
        gaScript.async = true
        gaScript.src = `https://www.googletagmanager.com/gtag/js?id=${gaMeasurementId}`
        document.head.appendChild(gaScript)

        const gaInline = document.createElement('script')
        gaInline.textContent = [
            'window.dataLayer = window.dataLayer || [];',
            'function gtag(){dataLayer.push(arguments);}',
            "gtag('js', new Date());",
            // Configure GA4 with user_id if logged in
            userId
                ? `gtag('config', '${gaMeasurementId}', { 'user_id': '${userId}' });`
                : `gtag('config', '${gaMeasurementId}');`,
        ].join('\n')
        document.head.appendChild(gaInline)
    }

    // ─── 3. Facebook Pixel ───────────────────────────────────────────────────
    const fbPixelId = m.fbPixelId
    const isFbPixelActive = m.isFbPixelActive !== false // Default to true
    
    if (fbPixelId && isFbPixelActive) {
        const fbInline = document.createElement('script')
        fbInline.textContent = `
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window,document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '${fbPixelId}'${userId ? `, { external_id: '${userId}' }` : ''});
      ${m.fbTestEventId ? `fbq('set', 'testEventCode', '${m.fbTestEventId}');` : ''}
      fbq('track', 'PageView');
    `
        document.head.appendChild(fbInline)

        // Noscript fallback image
        const ns = document.createElement('noscript')
        const img = document.createElement('img')
        img.height = 1
        img.width = 1
        img.style.display = 'none'
        img.src = `https://www.facebook.com/tr?id=${fbPixelId}&ev=PageView&noscript=1`
        ns.appendChild(img)
        if (document.body) {
            document.body.appendChild(ns)
        } else {
            // Unlikely in Nuxt plugins, but fallback to prevent dropping tracking
            document.head.appendChild(ns)
        }
    }

    // ─── 4. TikTok Pixel ─────────────────────────────────────────────────────
    const tiktokPixelId = m.tiktokPixelId
    const isTiktokPixelActive = m.isTiktokPixelActive !== false // Default to true
    
    if (tiktokPixelId && isTiktokPixelActive) {
        const ttInline = document.createElement('script')
        ttInline.textContent = `
      !function (w, d, t) {
        w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
        ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],
        ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
        for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
        ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},
        ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
        ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
        var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;
        var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
        ttq.load('${tiktokPixelId}');
        ttq.page();
        ${m.tiktokTestEventCode ? `ttq.instance('${tiktokPixelId}').setTestEventCode('${m.tiktokTestEventCode}');` : ''}
        ${userId ? `ttq.identify({ external_id: '${userId}' });` : ''}
      }(window, document, 'ttq');
    `
        document.head.appendChild(ttInline)
    }

    // Expose marketing config globally for composables to read
    ; (window as any)._marketingConfig = m
})
