// dataLayer events for Google Tag Manager, plus the campaign cookie the
// contact form stores with each message.
(function () {
    var lang = (window.sdTracking && sdTracking.lang) || document.documentElement.lang.slice(0, 2);
    window.dataLayer = window.dataLayer || [];

    window.sdTrack = function (event, data) {
        var payload = { event: event, language: lang };
        for (var key in data || {}) payload[key] = data[key];
        window.dataLayer.push(payload);
    };

    // Remember the campaign a visitor arrived from for 30 days.
    var params = new URLSearchParams(window.location.search);
    if (params.get('utm_source')) {
        var utm = JSON.stringify({ source: params.get('utm_source'), campaign: params.get('utm_campaign') || '' });
        document.cookie = 'sd_utm=' + encodeURIComponent(utm) + ';path=/;max-age=' + 60 * 60 * 24 * 30 + ';SameSite=Lax';
    }

    document.addEventListener('click', function (e) {
        var link = e.target.closest && e.target.closest('a[href]');
        if (!link) return;
        var href = link.getAttribute('href');
        if (href.indexOf('https://wa.me/') === 0) {
            window.sdTrack('sd_whatsapp_click', { link_location: link.dataset.location || '' });
        } else if (href.indexOf('mailto:') === 0) {
            window.sdTrack('sd_email_click', { link_location: link.dataset.location || '' });
        }
    });
})();
