# İçerik modeli

serhandemirel.com'da hangi içerik türlerinin ve alanların olduğunu, hangilerinin eklenmesinin planlandığını anlatır. Bu dosya kodun spesifikasyonudur: `serhandemirel-core` eklentisinde bir tür ya da alan eklendiğinde veya değiştirildiğinde aynı PR'da burası da güncellenir.

- Alanların tek tanım yeri: [`includes/fields.php`](../wp-content/plugins/serhandemirel-core/includes/fields.php). Meta anahtarları `_sd_<alan>` biçiminde.
- Hangi içeriğin ne zaman yazılacağı bu dosyada değil, [İçerik Yol Haritası](https://claude.ai/code/artifact/67e2ea58-54af-4f9e-a697-ad2c1449dc7e) belgesinde takip edilir.
- "Ortak" = Polylang'de tüm dillerde aynı kalır. "Çevrilir" = yeni çeviriye bir kez kopyalanır, sonra her dilde ayrı düzenlenir.

## Şu an kodda olanlar

| Tür | Slug | Destekler | Çeviri |
|---|---|---|---|
| Projects | `sd_project` | başlık, editör, öne çıkan görsel (1200×900), sıralama, revizyonlar; `/work/`; taksonomiler `sd_industry`, `sd_service` | Her dilde ayrı kopya |
| Expertise | `sd_expertise` | başlık, sıralama | Her dilde ayrı kopya |
| Brands | `sd_brand` | başlık, sıralama | Tek kayıt |
| Messages | `sd_message` | başlık, editör; yalnızca iletişim formu oluşturur | Tek kayıt |
| Insights | `post` (normal yazılar) | WordPress varsayılanları | Her dilde ayrı kopya |

| Tür | Ortak alanlar | Çevrilen alanlar |
|---|---|---|
| Projects | client (text), year (number), url (url), brand (sd_brand), gallery (galeri), featured (checkbox) | location (text), summary (textarea), metric (text) |
| Expertise | accent (select), icon (select) | description (textarea), tags (text) |
| Brands | logo_white (image), logo_color (image), url (url), visible (checkbox) | |
| Insights | featured (checkbox) | read_time (number) |
| Messages | status, name, email, lang, source_url, utm_source, utm_campaign (tek kayıt) | |
| Sayfa, yazı, proje | disable_tracking (checkbox), extra_head_code (kod, `unfiltered_html` gerekir) | |

## Planlanan (henüz kodda yok)

Aşağıdaki öneriler 4 Ekim 2026'da SEO, GEO, AEO ve AI görünürlüğü hedefiyle hazırlandı. Bir madde koda girdiğinde yukarıdaki "Şu an kodda olanlar" bölümüne taşınır.

### Neden bu alanlar? (2026 için kısa ilke listesi)

Google'ın AI Overviews'u, ChatGPT, Perplexity ve Claude gibi yapay zekâ yanıt motorları bir sayfayı kaynak gösterirken şunlara bakıyor:

1. **Varlık netliği (entity):** "Serhan Demirel kim, ne yapar, nerede, hangi markalarla çalıştı?" Bu sorulara tutarlı ve makine tarafından okunabilir cevaplar gerekiyor. Bunun için Person ve Organization şeması ile `sameAs` profil bağlantıları kullanılıyor.
2. **Önce cevap:** Her sayfanın başında 40–60 kelimelik net bir özet ya da tanım olmalı. LLM'ler en çok bu kısmı alıntılıyor.
3. **Ölçülebilir, kaynaklı gerçekler:** "+24% dönüşüm", tarih, süre ve kaynak gibi bilgiler. Sayılar ve kaynaklar alıntılanma olasılığını belirgin şekilde artırıyor.
4. **Soru–cevap blokları:** FAQ içeriği hâlâ AEO için çok değerli. Not: Google, 2023'ten beri FAQ zengin sonucunu çoğu siteye göstermiyor. Yine de içerik hem yanıt motorlarına hem de LLM'lere doğrudan besleniyor.
5. **Tazelik ve yazar güvenilirliği (E-E-A-T):** Görünür "son güncelleme" tarihi, gerçek yazar bilgisi ve ilgili deneyim.
6. **Yapılandırılmış veri (JSON-LD):** Alanlar ayrı ayrı tutulursa tema bunlardan otomatik şema üretebilir. Şu an temada JSON-LD yok, bu önerilerin çoğu bunu besliyor.

---

### 1. Mevcut türler için eklenmesi önerilen alanlar

#### Projects (`sd_project`)
| Alan | Tip | Dil | Neden |
|---|---|---|---|
| tldr (tek paragraf özet) | textarea | Çevrilir | Önce cevap; LLM alıntısı için |
| challenge / approach / outcome | textarea ×3 | Çevrilir | Vaka çalışması yapısı; AI bu bölümleri ayrı ayrı alıntılayabiliyor |
| metrics (etiket, değer, dönem) | tekrarlayıcı | Etiket çevrilir, değer ortak | Tek "metric" yerine birden fazla ölçülebilir sonuç |
| services_used | sd_service ilişkisi | Ortak | Proje ile hizmet sayfası arasında iç bağlantı |
| tools (ör. GA4, HubSpot, Shopify) | text / etiket | Ortak | Varlık ilişkisi ("X aracında uzman") |
| start_date / end_date | date | Ortak | Tazelik ve süre bilgisi |
| testimonial | sd_testimonial ilişkisi | Ortak | Kanıt; sayfada alıntı olarak görünür |
| video_url | url | Ortak | VideoObject şeması |
| updated_note | date | Ortak | Görünür "son güncelleme" |

#### Expertise (`sd_expertise`)
| Alan | Tip | Dil | Neden |
|---|---|---|---|
| service_page | sd_service ilişkisi | Ortak | Kart şu an hiçbir yere gitmiyor; hizmet sayfasına bağlanmalı |

#### Brands (`sd_brand`)
| Alan | Tip | Dil | Neden |
|---|---|---|---|
| legal_name | text | Ortak | Organization şemasında doğru isim |
| same_as (LinkedIn, Wikipedia/Wikidata) | url listesi | Ortak | AI, markayı doğru varlıkla eşleştirir |
| industry | sd_industry terimi | Ortak | "Fintech markalarıyla çalıştı" gibi çıkarımlar için |
| relationship (müşteri / işveren / partner) | select | Ortak | Bağlamı netleştirir |

#### Insights (normal yazılar, `post`)
| Alan | Tip | Dil | Neden |
|---|---|---|---|
| key_takeaways (3–5 madde) | tekrarlayıcı | Çevrilir | Önce cevap; AI Overviews'ta en çok alıntılanan blok |
| target_question | text | Çevrilir | Yazının cevapladığı asıl soru; başlık ve H2 planı için rehber |
| faq (soru, cevap) | tekrarlayıcı | Çevrilir | AEO; FAQPage şeması |
| sources (başlık, url) | tekrarlayıcı | Ortak | Kaynak gösterilen içerik daha güvenilir sayılıyor |
| reviewed_date | date | Ortak | Tazelik sinyali (`dateModified`) |
| related_service | sd_service ilişkisi | Ortak | İç bağlantı ve konu kümesi (topical cluster) |
| (mevcut) read_time, featured | | | Kalsın |

#### Messages (`sd_message`)
| Alan | Tip | Neden |
|---|---|---|
| service_interest | select (hizmetler) | Hangi hizmet talep görüyor |
| budget_range | select | Lead nitelemesi |
| found_via (Google / LinkedIn / **ChatGPT veya başka bir AI** / tavsiye / diğer) | select | **AI görünürlüğünü ölçmenin en basit yolu.** Formda tek bir soru yeterli |
| referrer | url | chatgpt.com, perplexity.ai, gemini.google.com gibi yönlendirenleri otomatik yakalar |
| consent | checkbox + tarih | KVKK/GDPR kaydı |

---

### 2. Yeni içerik türü önerileri (öncelik sırasıyla)

#### ① Services (`sd_service`) : en yüksek öncelik
Şu an "Services" yalnızca projeleri filtrelemek için kullanılan bir taksonomi. Her hizmetin kendi sayfası olursa "B2B SEO danışmanı", "Shopify CRO uzmanı" gibi aramalarda ve AI önerilerinde görünür olmanın ana yolu bu sayfalar olur. Mevcut taksonomi filtre olarak kalabilir, her terim bir hizmet sayfasına bağlanır.

| Alan | Tip | Dil |
|---|---|---|
| definition (40–60 kelime "Bu hizmet nedir?") | textarea | Çevrilir |
| who_for (kimler için) | textarea | Çevrilir |
| deliverables (çıktılar) | tekrarlayıcı | Çevrilir |
| process (adım başlığı, açıklama) | tekrarlayıcı | Çevrilir |
| duration / engagement_model | text / select | Çevrilir / Ortak |
| price_from + currency | number + select | Ortak |
| area_served (ülke ya da "Remote / Worldwide") | text | Ortak |
| faq | tekrarlayıcı | Çevrilir |
| related_projects / related_insights | ilişki | Ortak |
| service_term | sd_service terimi | Ortak |

Şema: `Service` (provider = Person, areaServed, offers) + `FAQPage`.

#### ② FAQ (`sd_faq`)
Merkezi bir soru bankası. Aynı soru hizmet, proje ve ana sayfada tekrar kullanılabilir; tek yerde güncellenir.

| Alan | Tip | Dil |
|---|---|---|
| (başlık = soru) | | Çevrilir |
| short_answer (1–2 cümle, doğrudan cevap) | textarea | Çevrilir |
| long_answer | editör | Çevrilir |
| topics | sd_service ilişkisi | Ortak |
| show_on_home | checkbox | Ortak |

#### ③ Testimonials (`sd_testimonial`)
Kanıt, E-E-A-T ve AI'nın "müşteriler ne diyor" sorusuna verdiği cevaplar için.

| Alan | Tip | Dil |
|---|---|---|
| quote | textarea | Çevrilir (orijinal dil işaretlenir) |
| person_name, person_title | text | Ortak |
| brand | sd_brand ilişkisi | Ortak |
| project | sd_project ilişkisi | Ortak |
| photo | image | Ortak |
| source_url (LinkedIn önerisi vb.) | url | Ortak |
| date | date | Ortak |

Not: Google, sitenin kendisi hakkındaki yorumlar için yıldız göstermiyor. Amaç yıldız değil, güven ve alıntılanabilir kanıt.

#### ④ Appearances / Press (`sd_appearance`)
Konuşmalar, podcastler, röportajlar, ödüller ve başka sitelerde yayınlanan yazılar. AI'nın bir kişiyi "otorite" olarak tanımasında dış kaynaklar çok güçlü bir sinyal.

| Alan | Tip | Dil |
|---|---|---|
| kind (talk / podcast / interview / award / guest post) | select | Ortak |
| outlet_or_event | text | Ortak |
| date | date | Ortak |
| url, video_url | url | Ortak |
| summary | textarea | Çevrilir |
| topics | sd_service ilişkisi | Ortak |

#### ⑤ (İsteğe bağlı) Glossary (`sd_term`)
"CRO nedir?", "Lead scoring nedir?" gibi kısa tanım sayfaları. AEO ve LLM tanım cevapları için çok etkili, ama düzenli içerik üretimi gerektiriyor. Alanlar: short_definition, long_explanation, related_terms, related_service. Şema: `DefinedTerm`.

#### ⑥ (İsteğe bağlı) Resources (`sd_resource`)
Şablon, kontrol listesi ya da araç gibi indirilebilir içerikler. Lead toplamak ve backlink kazanmak için. Alanlar: file, format, gated (form arkasında mı), related_service.

---

### 3. İçerik türü değil, site ayarı olarak eklenmeli

**Kişi / varlık profili (Person)**, eklenti ayarlarında tek kayıt olarak tutulur. GEO için en kritik parça bu:
- full_name, alternate_names (yazım farkları), job_title (çevrilir), short_bio (çevrilir, 50 kelime), long_bio (çevrilir)
- headshot, location (şehir/ülke), languages_spoken
- knows_about (uzmanlık konuları listesi)
- same_as: LinkedIn, X, GitHub, Medium, Wikidata vb.
- works_for / alumni_of / credentials (sertifikalar)

Tema bundan her sayfaya tek bir JSON-LD grafiği basar: Person + WebSite + o sayfanın türü (Service, Article, CreativeWork, FAQPage).

**Diğer teknik maddeler** (alan değil, ama aynı hedefe hizmet ediyor):
- `llms.txt`: hizmetler, öne çıkan projeler ve yazılardan otomatik üretilen özet dosya.
- AI tarayıcı politikası: GPTBot, ClaudeBot, PerplexityBot ve Google-Extended için izin verilip verilmeyeceği bilinçli seçilmeli. Görünürlük isteniyorsa açık kalmalı.
- Görünür "son güncelleme" tarihi ve `dateModified`.
- hreflang Polylang ile zaten var. Her dilde tldr ve definition alanları gerçekten çevrilmeli, makine çevirisi bırakılmamalı.

---

### Önerilen sıra
1. Person profili ve JSON-LD altyapısı
2. Services türü ve Expertise kartlarının hizmet sayfalarına bağlanması
3. Mevcut türlere eklenecek alanlar (Projects, Insights, Brands, Messages)
4. FAQ ve Testimonials
5. Appearances; ihtiyaç olursa Glossary ve Resources
