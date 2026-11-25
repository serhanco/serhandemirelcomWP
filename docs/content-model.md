# İçerik modeli

serhandemirel.com'daki içerik türlerini ve alanları, kalan önerileri anlatır. Bu dosya kodun spesifikasyonudur: `serhandemirel` temasında bir tür ya da alan eklendiğinde veya değiştirildiğinde aynı PR'da burası da güncellenir.

- Alanların tek tanım yeri: [`inc/fields.php`](../wp-content/themes/serhandemirel/inc/fields.php). Meta anahtarları `_sd_<alan>` biçiminde. Sektör sayfası alanları terim meta'sında, [`inc/trust.php`](../wp-content/themes/serhandemirel/inc/trust.php) içindeki `sdc_industry_fields()`'ta.
- Hangi içeriğin ne zaman yazılacağı bu dosyada değil, [İçerik Yol Haritası](https://claude.ai/code/artifact/67e2ea58-54af-4f9e-a697-ad2c1449dc7e) belgesinde takip edilir. Alanların gerekçesi: [İçerik tipleri ve alan önerileri](https://claude.ai/artifact/7ydnFqvpi2yL8oK38sjg91) (25 Kasım 2026).
- "Ortak" = Polylang'de tüm dillerde aynı kalır. "Çevrilir" = yeni çeviriye bir kez kopyalanır, sonra her dilde ayrı düzenlenir. WPML için aynı ayrım `wpml-config.xml`'de.

## İlkeler (2026)

AI yanıt motorları (Google AI Overviews, ChatGPT, Perplexity, Claude) ve arama bir sayfayı kaynak gösterirken şunlara bakıyor; alanlar bunun için var:

1. **Varlık netliği:** kim, ne yapar, nerede, kiminle çalıştı. Person şeması, `sameAs`, tek unvan ("Digital Solutions Architect").
2. **Önce cevap:** sayfanın başında 40–60 kelimelik özet ya da tanım (`definition`, `short_definition`, yazı özeti, sektör `intro`).
3. **Ölçülebilir, kaynaklı gerçekler:** metrikler ve ölçüm kaynağı, `sources` listesi, metinde `[1]` işaretleri.
4. **Soru–cevap blokları:** her türde `faq`. Google FAQ zengin sonucunu 7 Mayıs 2026'da tamamen kaldırdı; `FAQPage` şeması zararsız, değeri artık AI cevapları ve sayfadaki içerik.
5. **E-E-A-T:** görünür yazar ve unvan, deneyim yılı, "From my work" deneyim kutusu, referanslar, sertifikalar, konuşmalar.
6. **Tarihler:** her sayfada görünür yayın tarihi; "Updated" yalnızca elle girilen `reviewed_date` yayın tarihinden sonraysa görünür, `update_note` ne değiştiğini söyler. JSON-LD'de `datePublished` / `dateModified` aynı alanlardan gelir.

## İçerik türleri

| Tür | Slug | Adres | Destekler | Çeviri |
|---|---|---|---|---|
| Projects | `sd_project` | `/work/` | başlık, editör, öne çıkan görsel (1200×900), sıralama, revizyonlar; taksonomiler `sd_industry`, `sd_service` | Her dilde ayrı kopya |
| Services | `sd_service_page` | `/services/` | başlık, editör, özet, öne çıkan görsel, sıralama, revizyonlar; taksonomi `sd_service` (aynı terim projelerle ortak, "Related work" buradan gelir) | Her dilde ayrı kopya |
| Insights | `post` | blog sayfası (`/insights/`) | WordPress varsayılanları | Her dilde ayrı kopya |
| Glossary | `sd_term` | `/glossary/` | başlık, editör, revizyonlar | Her dilde ayrı kopya |
| Talks, trainings & press | `sd_appearance` | `/speaking/` (tek kayıt sayfası yok, listedeki satırına yönlenir) | başlık, özet, görsel | Her dilde ayrı kopya |
| Testimonials | `sd_testimonial` | sayfası yok; hizmet, proje, sektör ve Hakkımda'da görünür | başlık | Her dilde ayrı kopya |
| Expertise | `sd_expertise` | ana sayfa kartları | başlık, sıralama | Her dilde ayrı kopya |
| Brands | `sd_brand` | ana sayfa, Hakkımda, sektör | başlık, sıralama; taksonomi `sd_industry` | Tek kayıt |
| Messages | `sd_message` | yalnızca iletişim formu oluşturur | başlık, editör | Tek kayıt |

**Sektörler (`sd_industry`)** taksonomisi aynı zamanda `/industry/<slug>/` sektör sayfasıdır (`taxonomy-sd_industry.php`). **SSS merkezi** ayrı tür değil, "FAQ" şablonlu bir sayfa (`template-faq.php`); soruları diğer türlerden toplar.

## Alanlar

### Her türde ortak alanlar ("Sources, updates and search" kutusu)
Yazı, sayfa, proje, hizmet ve terimde:

| Ortak | Çevrilir |
|---|---|
| reviewed_date (date; yayından sonraysa "Updated" ve `dateModified`), share_image (image; `og:image`), noindex (checkbox; `noindex,follow`, site haritası ve llms.txt dışında) | update_note (text; "What changed"), sources ("Başlık \| URL \| yayıncı" satırları; metinde `[1]` 1. kaynağa bağlanır), seo_title, seo_description |

SEO başlık ve açıklaması bir SEO eklentisi (Yoast, Rank Math, AIOSEO, SEOPress) etkinse ona bırakılır. Ayrıca sayfa, yazı, proje, hizmet ve terimde: disable_tracking, extra_head_code (`unfiltered_html` gerekir).

### Tür başına alanlar

| Tür | Ortak alanlar | Çevrilen alanlar |
|---|---|---|
| Projects | client, year, url, brand (sd_brand), gallery, featured, tools (virgülle), start_date, end_date, client_hidden (müşteri adı gizli: kart ve sayfada kilit, logo ve bağlantı gizlenir, şemada `sourceOrganization` yok), video_url | location ("Fintech / London"), summary, metric, metrics ("Değer \| ne ölçtüğü \| not" satırları), metrics_source (ölçüm kaynağı), role, challenge / approach / outcome, anon_name (gizli müşteri için ad, ör. "A European fintech"), testimonial + testimonial_author (eski alıntı; referans kaydı yoksa gösterilir) |
| Services | engagement_model (project / retainer / workshop / hourly), price_from, currency, area_served, booking_url ("Book a 15-min call"; boşsa profildeki) | definition (40–60 kelime), who_for ("A good fit if"), not_for ("Not a fit if", satır başına bir madde), deliverables, process ("Adım: açıklama"), duration, languages, faq |
| Insights | featured, related_service, format (guide / how-to / comparison / opinion / case note), experience_project, glossary_terms | read_time, target_question ("Answers: …"), takeaways, experience_note ("From my work"), faq |
| Glossary | example_project, related_terms, related_service | short_definition (25–40 kelime, alıntılanabilir "Definition" kutusu), aliases (satır başına bir diğer ad), example ("From the field"), faq |
| Talks & press | kind (talk / workshop / training / podcast / interview / press), host (sd_brand), withheld (kurum adı gizli), date, language, participants, url, video_url, slides_url, duration (dakika), related_service | event, location |
| Testimonials | person, company (sd_brand) ya da company_name, photo, linkedin, project, services, date, consent (işaretli değilse hiçbir yerde görünmez) | quote, role |
| Brands | logo_white, logo_color (isteğe bağlı), url, visible, relationship (client / employer / partner), period, legal_name, same_as | |
| Expertise | accent, icon, service_page | description, tags |
| Messages | status, name, email, lang, source_url, utm_source, utm_campaign, service (formun açıldığı hizmet), found_via (google / ai / linkedin / referral / event / other), referrer (ilk dış yönlendiren; chatgpt.com, perplexity.ai gibi AI kaynakları e-postada işaretlenir), consent_at | |

### Sektör sayfası alanları (terim meta'sı, Sektörler ekranında)
headline, intro (önce cevap), problems ("Sorun \| cevap \| hizmet slug'ı" satırları), faq, published_date, reviewed_date, update_note, seo_title, seo_description. Sayfada ayrıca sektörün projeleri, markaları ve referansları otomatik listelenir.

### Profil (Person)
İçerik türü değil, temadaki *Profile* ekranında tek kayıt (`sdc_profile`). Ortak: full_name, alternate_names, headshot, city, country, languages_spoken, same_as, works_for, works_for_url, alumni_of, credentials ("Ad \| veren \| yıl \| bağlantı"), memberships, career_start (yıl; "13+ years" buradan), booking_url. Dile göre: job_title (varsayılan "Digital Solutions Architect"), short_bio, long_bio, knows_about, stats ("Değer \| etiket"; boşsa otomatik), awards. `template-profile.php` ("Profile (About)") bunları Hakkımda'da gösterir.

### Site ayarları (Theme settings)
İçerikle ilgili yeni ayarlar: block_ai_training (AI eğitim tarayıcılarını engelle; varsayılan kapalı), faq_general_title + faq_general (SSS merkezinin "Working together" grubu), glossary_intro, speaking_intro, form_ask_source ("How did you find me?" sorusu; varsayılan açık), privacy_url (formdaki onay bağlantısı; boşsa WordPress gizlilik sayfası).

## Otomatik çıktılar

- **JSON-LD** (`inc/schema.php`; bir SEO eklentisi etkinse basılmaz): her sayfada Person (hasCredential, award, memberOf, subjectOf = konuşmalar, contactPoint) + WebSite + sayfa düğümü + BreadcrumbList. Yazılarda BlogPosting (alternativeHeadline, citation, mentions = sözlük terimleri, isBasedOn = deneyim projesi) + FAQPage; projelerde CreativeWork (dateCreated, temporalCoverage, referans `comment`); hizmetlerde Service + FAQPage; terimlerde DefinedTerm + FAQPage; sözlük arşivinde DefinedTermSet; konuşmalarda ItemList (Event / PodcastEpisode / Article, VideoObject); sektörde `about` + FAQPage; SSS sayfasında tüm sorularla FAQPage.
- **Meta etiketleri** (`inc/seo.php` + `functions.php`): description, Open Graph, Twitter, `article:published_time` / `modified_time` / `author`. Başlık `seo_title`'dan.
- **Site haritası:** noindex kayıtlar, konuşmalar ve referanslar dışarıda.
- **robots.txt:** AI arama ve cevap botları (OAI-SearchBot, ChatGPT-User, Claude-SearchBot, Claude-User, PerplexityBot, Perplexity-User) açıkça izinli; eğitim botları (GPTBot, ClaudeBot, Google-Extended, Applebot-Extended, CCBot, Meta-ExternalAgent, Bytespider, cohere) `block_ai_training` ayarına göre; sonunda llms.txt bağlantısı.
- **`/llms.txt`** (`inc/llms.php`): profil, hizmetler, sektörler, projeler, yazılar (cevapladığı soru ve tarih), sözlük, konuşmalar, Hakkımda ve SSS; noindex sayfalar hariç.
- **SSS merkezi** genel sorular, her hizmetin, sektörün, yazının ve terimin SSS'sini kaynağıyla toplar; her cevabın altında kendi sayfasına bağlantı var.

## Kalan öneriler (henüz kodda yok)

- **Merkezi soru bankası (`sd_faq`):** şimdilik yapılmadı; sorular kendi sayfalarında duruyor ve SSS merkezi onları topluyor. Aynı soru çok yerde tekrar ederse türe çevrilir.
- **Resources (`sd_resource`):** şablon ve kontrol listeleri; alanlar file, format, gated, related_service. İçerik hazır olunca.
- **Formda bütçe aralığı (`budget_range`):** lead nitelemesi için; istenirse eklenir.
- **Marka için Organization şeması:** `legal_name` ve `same_as` alanları var, şemaya henüz ayrı düğüm olarak basılmıyor.
- **Tekrarlayıcı alanlar:** deliverables, process, faq, metrics şimdilik satır bazlı metin; ihtiyaç olursa tekrarlayıcı alana çevrilir.
- **Dil:** hreflang Polylang ile var. Özet ve tanım alanları her dilde gerçekten çevrilmeli, makine çevirisi bırakılmamalı.
