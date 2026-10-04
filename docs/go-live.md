# Yayına alma rehberi

serhandemirel.com'u gerçek sunucuda açmak için sırayla yapılacaklar. Çoğu adımı WordPress yönetim panelindeki **Araçlar → Site setup** ekranı da kontrol eder: yeşil olan tamam, sarı olanın altında ne yapılacağı yazar.

## 1. Sunucu

- PHP 8.1+ ve MySQL 8 / MariaDB 10.6+ olan bir WordPress hostingi, HTTPS (Let's Encrypt yeterli).
- Bu repodaki iki klasörü sunucuya yükleyin:
  - `wp-content/themes/serhandemirel` (içindeki `languages/` ve derlenmiş `assets/css/tailwind.css` dahil; `node_modules` gerekmez)
  - `wp-content/plugins/serhandemirel-core`
- **Görünüm → Temalar**: Serhan Demirel temasını etkinleştirin. **Eklentiler**: Serhan Demirel Core'u etkinleştirin. Etkinleştirme, örnek uzmanlık kartlarını, markaları, hizmet terimlerini, 5 hizmet sayfası taslağını ve bir "About" taslağını oluşturur.

## 2. Diller

1. **Eklentiler → Yeni ekle**: "Polylang" arayın, kurun, etkinleştirin. Kurulum sihirbazı açılırsa atlayabilirsiniz.
2. **Araçlar → Site setup → Set up languages** düğmesine basın (ya da sunucuda `wp sdc setup`). Bu düğme:
   - İngilizce (varsayılan, site kökünde), Türkçe, Fransızca, Felemenkçe, Almanca ve İtalyanca dillerini ekler; adresler `/tr/`, `/fr/`, `/nl/`, `/de/`, `/it/` olur,
   - WordPress'in bu dillerdeki çevirilerini indirir,
   - Polylang'in kendi tarayıcı dili yönlendirmesini kapatır (bunu tema yapıyor: yalnızca ilk ziyarette, yalnızca ana sayfada, botlara dokunmadan),
   - dili olmayan içeriği İngilizceye atar ve kalıcı bağlantıları "Yazı adı" yapar.
3. Diğer dillerdeki içerik için: **Diller → Çeviriler** değil, her içeriğin yanındaki **+** simgesiyle çeviri oluşturun (hizmet sayfaları, projeler, uzmanlık kartları, yazılar, About). Tema metinleri (`languages/*.mo`) zaten çevrili.
4. **Görünüm → Serhan Demirel** ayarlarındaki metinler her dil için ayrı kaydedilir; üstteki dil seçiciyle her dili kontrol edin.

## 3. Profil ve içerik

- **Profile**: fotoğraf (kare, en az 400×400), kısa bio, uzun bio, konuşulan diller, okul ve sertifikalar, profil bağlantıları (LinkedIn adresini kontrol edin). Dile göre alanlar her dilde ayrı doldurulur; boş kalan varsayılan dildeki metni kullanır.
- **Services**: 5 taslağı okuyun, size uymayan cümleleri değiştirin, isterseniz "Starting price" girin, **Yayımla**. Ardından her birinin çevirisini oluşturun.
- **Sayfalar → About**: şablonu "Profile (About)". Metni gözden geçirip yayımlayın.
- **Görünüm → Menüler**: isterseniz "primary" konumuna Services (`/services/`) ve About bağlantılarını ekleyin. Menü yoksa tema ana sayfa bölümlerini otomatik gösterir; menü kurarsanız her dil için ayrı menü atanır.

## 4. Arama motorları ve AI

- **Ayarlar → Okuma**: "Arama motorlarının siteyi dizine eklemesini engelle" işaretini kaldırın.
- Yapılandırılmış veri (JSON-LD) ve `/llms.txt` eklenti tarafından otomatik üretilir. Yoast, Rank Math, AIOSEO veya SEOPress kurarsanız eklentinin JSON-LD'si kendini kapatır (çakışma olmaz). Kontrol için: [Google Zengin Sonuç Testi](https://search.google.com/test/rich-results) ve [Schema Markup Validator](https://validator.schema.org/).
- `https://serhandemirel.com/llms.txt` adresini açıp metni okuyun.
- Google Search Console ve Bing Webmaster Tools'a siteyi ekleyin, `wp-sitemap.xml` site haritasını gönderin.
- AI tarayıcıları (GPTBot, ClaudeBot, PerplexityBot, Google-Extended) varsayılan olarak engellenmez. Görünürlük hedefi için böyle kalması önerilir.

## 5. İletişim formu

- Bir SMTP eklentisi kurun (WP Mail SMTP veya FluentSMTP) ve test e-postası gönderin. Form mesajları ayrıca **Messages** altında saklanır.

## 6. Son kontrol

- Gizli pencerede ana sayfayı farklı tarayıcı dilleriyle açın: ilk ziyarette ilgili dile geçmeli, dil seçiciden İngilizceyi seçtikten sonra bir daha yönlendirmemeli.
- Mobilde menüyü ve dil seçiciyi deneyin.
- Her dilde bir hizmet sayfası, About ve bir proje açın; sayfa kaynağında `hreflang` ve `x-default` satırlarını görün.
