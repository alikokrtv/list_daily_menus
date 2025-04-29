# Daily Menu Manager / Günlük Menü Yöneticisi

A comprehensive WordPress plugin for managing and displaying daily menus with multiple locations, style customization, and Excel import functionality.

Kapsamlı özelliklere sahip, çoklu konum desteği, özelleştirilebilir stil, Excel içe aktarma ve rol tabanlı erişim kontrolü sunan WordPress günlük menü yönetim eklentisi.

## Features / Özellikler

- **Menu Management / Menü Yönetimi**: Easily create, edit, and delete daily menus / Günlük menüleri kolayca oluşturun, düzenleyin ve silin
- **Multiple Locations / Çoklu Konumlar**: Support for different menu locations (restaurants, cafeterias, etc.) / Farklı menü konumları için destek (restoranlar, yemekhane şubeleri vb.)
- **Style Customization / Stil Özelleştirme**: Create and apply custom styles to menu displays / Menü görünümünü özel stillerle özelleştirin
- **Background Images / Arka Plan Görselleri**: Add custom background images to your menu displays / Menü görünümüne özel arka plan görselleri ekleyin
- **Excel Import / Excel İçe Aktarma**: Import menus in bulk from Excel/CSV files / Excel/CSV dosyalarından toplu olarak menü içe aktarın
- **Shortcode Support / Kısayol Desteği**: Display menus anywhere with customizable shortcodes / Özelleştirilebilir kısayollar ile menüleri istediğiniz yerde görüntüleyin
- **Role-Based Access / Rol Tabanlı Erişim**: Control which user roles can view specific menus / Hangi kullanıcı rollerinin belirli menüleri görebileceğini kontrol edin
- **Special Menu Highlighting / Özel Menü Vurgulama**: Highlight special menus with animations / Özel menüleri animasyonlarla vurgulayın
- **Live Preview / Canlı Önizleme**: Preview your menus while editing / Düzenlerken menülerinizi canlı olarak önizleyin
- **Date Navigation / Tarih Navigasyonu**: Browse through menus with intuitive navigation controls / Sezgisel gezinme kontrolleriyle menüler arasında gezinin
- **Multilingual Support / Çok Dilli Destek**: Available in English and Turkish / İngilizce ve Türkçe dil desteği
- **Responsive Design / Duyarlı Tasarım**: Looks great on all devices / Tüm cihazlarda harika görünür

## Installation / Kurulum

1. Upload the plugin files to the `/wp-content/plugins/daily-menu-manager` directory, or install the plugin through the WordPress plugins screen. / Eklenti dosyalarını `/wp-content/plugins/daily-menu-manager` dizinine yükleyin veya WordPress eklenti ekranından eklentiyi yüklerin.
2. Activate the plugin through the 'Plugins' screen in WordPress. / WordPress'te 'Eklentiler' ekranından eklentiyi etkinleştirin.
3. Use the Menu Manager menu in the admin sidebar to configure and manage your menus. / Admin kenar çubuğundaki Menü Yöneticisi menüsünü kullanarak menülerinizi yapılandırın ve yönetin.

## Usage / Kullanım

### Basic Shortcode / Temel Kısayol

To display menus on your site, use the shortcode: / Sitenizde menüleri görüntülemek için kısayolu kullanın:

```
[daily_menu]
```

### Shortcode with Parameters / Parametreli Kısayol

Customize the menu display with these parameters: / Menü görünümünü bu parametrelerle özelleştirin:

```
[daily_menu location="Restaurant" style_id="2" date="current" navigation="1" roles="administrator,editor"]
```

- `location`: Specify which location's menus to display / Hangi konumdaki menülerin görüntülenmesi gerektiğini belirtir (restoranlar, şubeler vb.)
- `style_id`: Use a specific style preset (by ID) / Belirli bir stil önayarını kullanır (ID ile)
- `date`: Show menus for 'all' dates, 'current' date only, or a specific date / 'tüm' tarihler, 'geçerli' tarih veya belirli bir tarih için menüleri gösterir
- `navigation`: Override navigation button display ('1' to show, '0' to hide) / Gezinme düğmelerinin görünümünü geçersiz kılar ('1' göstermek, '0' gizlemek için)
- `roles`: Restrict menu visibility to specific user roles (comma-separated) / Menü görünürlüğünü belirli kullanıcı rolleriyle sınırlar (virgülle ayrılmış)

### Excel Import Format / Excel İçe Aktarma Formatı

When importing menus from Excel, the file should have these columns: / Excel'den menü içe aktarma yaparken, dosyanın şu sütunlara sahip olması gerekir:

1. Date (in your configured format, e.g., DD/MM/YYYY) / Tarih (yapılandırılmış formatınızda, örneğin, GG/AA/YYYY)
2. Menu items (comma separated) / Menü öğeleri (virgülle ayrılmış)
3. Special menu flag (1 for special, 0 for regular) / Özel menü bayrağı (1: özel, 0: normal)

Example / Örnek:
```
01/05/2025,Çorba, Ana Yemek, Yan Yemek, Tatlı,0
02/05/2025,Hafta Sonu Özel Menü,1
```

## Admin Pages / Yönetici Sayfaları

- **Manage Menus / Menüleri Yönet**: Create and manage daily menus for each location / Her konum için günlük menüleri oluşturun ve yönetin
- **Import Excel / Excel İçe Aktar**: Import menus in bulk from Excel/CSV files / Excel/CSV dosyalarından toplu olarak menü içe aktarın
- **Styling / Stil Yönetimi**: Create and manage style presets for menu displays / Menü görünümleri için stil önayarları oluşturun ve yönetin
- **Locations / Konumlar**: Manage different menu locations / Farklı menü konumlarını yönetin
- **Settings / Ayarlar**: Configure plugin settings / Eklenti ayarlarını yapılandırın

## Requirements / Gereksinimler

- WordPress 5.0 or higher / WordPress 5.0 veya üzeri
- PHP 7.0 or higher / PHP 7.0 veya üzeri

## Support / Destek

For support or feature requests, please visit [alikokdeneysel.online](alikokrtv@gmail.com) / Destek veya özellik istekleri için lütfen [alikokdeneysel.online](alikokrtv@gmail.com) adresini ziyaret edin

## License / Lisans

This plugin is licensed under the GPL v2 or later. / Bu eklenti GPL v2 veya sonrası altında lisanslanmıştır.

## Credits / Geliştirenler

Developed by alikok / alikok tarafından geliştirilmiştir
