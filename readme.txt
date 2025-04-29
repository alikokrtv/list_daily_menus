=== Daily Menu Manager / Günlük Menü Yöneticisi ===
Contributors: alikok
Tags: menu, food, restaurant, daily menu, cafeteria, excel import
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A comprehensive WordPress plugin for managing and displaying daily menus with multiple locations, style customization, and Excel import functionality.

== Description ==

Daily Menu Manager is a feature-rich WordPress plugin designed for restaurants, cafeterias, and food service businesses to manage and display their daily menus.

**English Description**

A comprehensive WordPress plugin for managing and displaying daily menus with multiple locations, style customization, and Excel import functionality.

**Türkçe Açıklama**

Kapsamlı özelliklere sahip, çoklu konum desteği, özelleştirilebilir stil, Excel içe aktarma ve rol tabanlı erişim kontrolü sunan WordPress günlük menü yönetim eklentisi.

= Features / Özellikler =

* **Menu Management / Menü Yönetimi**: Easily create, edit, and delete daily menus / Günlük menüleri kolayca oluşturun, düzenleyin ve silin
* **Multiple Locations / Çoklu Konumlar**: Support for different menu locations (restaurants, cafeterias, etc.) / Farklı menü konumları için destek (restoranlar, yemekhane şubeleri vb.)
* **Style Customization / Stil Özelleştirme**: Create and apply custom styles to menu displays / Menü görünümünü özel stillerle özelleştirin
* **Background Images / Arka Plan Görselleri**: Add custom background images to your menu displays / Menü görünümüne özel arka plan görselleri ekleyin
* **Excel Import / Excel İçe Aktarma**: Import menus in bulk from Excel/CSV files / Excel/CSV dosyalarından toplu olarak menü içe aktarın
* **Shortcode Support / Kısayol Desteği**: Display menus anywhere with customizable shortcodes / Özelleştirilebilir kısayollar ile menüleri istediğiniz yerde görüntüleyin
* **Role-Based Access / Rol Tabanlı Erişim**: Control which user roles can view specific menus / Hangi kullanıcı rollerinin belirli menüleri görebileceğini kontrol edin
* **Special Menu Highlighting / Özel Menü Vurgulama**: Highlight special menus with animations / Özel menüleri animasyonlarla vurgulayın
* **Live Preview / Canlı Önizleme**: Preview your menus while editing / Düzenlerken menülerinizi canlı olarak önizleyin
* **Date Navigation / Tarih Navigasyonu**: Browse through menus with intuitive navigation controls / Sezgisel gezinme kontrolleriyle menüler arasında gezinin
* **Multilingual Support / Çok Dilli Destek**: Available in English and Turkish / İngilizce ve Türkçe dil desteği
* **Responsive Design / Duyarlı Tasarım**: Looks great on all devices / Tüm cihazlarda harika görünür

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/daily-menu-manager` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Menu Manager menu in the admin sidebar to configure and manage your menus.

== Usage ==

= Basic Shortcode =

To display menus on your site, use the shortcode:

`[daily_menu]`

= Shortcode with Parameters =

Customize the menu display with these parameters:

`[daily_menu location="Restaurant" style_id="2" date="current" navigation="1" roles="administrator,editor"]`

* `location`: Specify which location's menus to display
* `style_id`: Use a specific style preset (by ID)
* `date`: Show menus for 'all' dates, 'current' date only, or a specific date
* `navigation`: Override navigation button display ('1' to show, '0' to hide)
* `roles`: Restrict menu visibility to specific user roles (comma-separated)

= Excel Import Format =

When importing menus from Excel, the file should have these columns:

1. Date (in your configured format, e.g., DD/MM/YYYY)
2. Menu items (comma separated)
3. Special menu flag (1 for special, 0 for regular)

Example:
`01/05/2025,Soup, Main Course, Side Dish, Dessert,0
02/05/2025,Weekend Special Menu,1`

== Frequently Asked Questions ==

= Can I display different menus for different locations? =

Yes, you can create different menus for each location and display them using the location parameter in the shortcode.

= Can I restrict who can see certain menus? =

Yes, you can use role-based access control to restrict menu visibility to specific WordPress user roles.

= Can I import menus from Excel? =

Yes, you can import menus in bulk from Excel or CSV files using the Import Excel feature.

= Where can I get support? =

For support or feature requests, please contact alikokrtv@gmail.com

== Screenshots ==

1. Admin menu management screen
2. Style customization options
3. Excel import interface
4. Frontend menu display
5. Role-based access control

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release

== License ==

This plugin is licensed under the GPL v2 or later.

== Credits ==

Developed by alikok
