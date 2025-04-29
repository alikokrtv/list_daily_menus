# Daily Menu Manager

A comprehensive WordPress plugin for managing and displaying daily menus with multiple locations, style customization, and Excel import functionality.

## Features

- **Menu Management**: Easily create, edit, and delete daily menus
- **Multiple Locations**: Support for different menu locations (restaurants, cafeterias, etc.)
- **Style Customization**: Create and apply custom styles to menu displays
- **Excel Import**: Import menus in bulk from Excel/CSV files
- **Shortcode Support**: Display menus anywhere with customizable shortcodes
- **Special Menu Highlighting**: Highlight special menus with animations
- **Date Navigation**: Browse through menus with intuitive navigation controls
- **Responsive Design**: Looks great on all devices

## Installation

1. Upload the plugin files to the `/wp-content/plugins/daily-menu-manager` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Menu Manager menu in the admin sidebar to configure and manage your menus.

## Usage

### Basic Shortcode

To display menus on your site, use the shortcode:

```
[daily_menu]
```

### Shortcode with Parameters

Customize the menu display with these parameters:

```
[daily_menu location="Restaurant" style_id="2" date="current" navigation="1"]
```

- `location`: Specify which location's menus to display
- `style_id`: Use a specific style preset (by ID)
- `date`: Show menus for 'all' dates, 'current' date only, or a specific date
- `navigation`: Override navigation button display ('1' to show, '0' to hide)

### Excel Import Format

When importing menus from Excel, the file should have these columns:

1. Date (in your configured format, e.g., DD/MM/YYYY)
2. Menu items (comma separated)
3. Special menu flag (1 for special, 0 for regular)

Example:
```
01/05/2025,Soup, Main Course, Side Dish, Dessert,0
02/05/2025,Weekend Special Menu,1
```

## Admin Pages

- **Manage Menus**: Create and manage daily menus for each location
- **Import Excel**: Import menus in bulk from Excel/CSV files
- **Styling**: Create and manage style presets for menu displays
- **Locations**: Manage different menu locations
- **Settings**: Configure plugin settings

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Support

For support or feature requests, please visit [alikokdeneysel.online](https://alikokdeneysel.online)

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by alikok
