# Banner Slider Component

A reusable component that displays banners in a slider format based on target pages.

## Usage

### Basic Usage (Auto-detect current page)
```blade
<x-banner-slider />
```

### With Specific Position
```blade
<x-banner-slider position="homepage_banner" />
```

### With Specific Page Identifier
```blade
<x-banner-slider pageIdentifier="products" />
```

### With Both Position and Page
```blade
<x-banner-slider 
    position="header_banner" 
    pageIdentifier="home" 
/>
```

## Features

- **Automatic Page Detection**: Automatically detects the current route and shows banners configured for that page
- **Slider Support**: If multiple banners are found, they are displayed in a Swiper slider
- **Single Banner**: If only one banner exists, it's displayed without slider controls
- **View Tracking**: Automatically tracks banner views
- **Click Tracking**: Tracks banner link clicks
- **Target Pages**: Respects the `target_pages` configuration from admin panel
- **Show on All Pages**: Respects the `show_on_all_pages` setting

## How It Works

1. The component queries active banners based on:
   - Current route name (mapped to page identifiers)
   - Optional `position` filter
   - `target_pages` array or `show_on_all_pages` flag

2. If multiple banners are found:
   - Displays them in a Swiper slider
   - Auto-plays with 5-second delay
   - Includes navigation arrows and pagination dots
   - Tracks views when slides change

3. If single banner is found:
   - Displays it directly without slider
   - Tracks view on page load

## Page Identifier Mapping

The component automatically maps Laravel route names to page identifiers:

| Route Name | Page Identifier |
|------------|----------------|
| `home` | `home` |
| `about` | `about` |
| `products` | `products` |
| `products.show` | `products.show` |
| `products.category` | `products.category` |
| `clinics` | `clinics` |
| `clinics.show` | `clinics.show` |
| `blogs` | `blogs` |
| `blogs.show` | `blogs.show` |
| ... and more | ... |

## Example: Adding to a Page

### Homepage
```blade
<!-- In resources/views/frontend/pages/home/index.blade.php -->
<x-banner-slider position="homepage_banner" />
```

### Products Page
```blade
<!-- In resources/views/frontend/pages/products/index.blade.php -->
<x-banner-slider />
```

### Custom Position
```blade
<!-- In any page -->
<x-banner-slider position="sidebar_banner" />
```

## Notes

- The component uses Swiper.js (already included in the project)
- Banners are ordered by `priority` (descending) then `created_at` (descending)
- Only active banners within their scheduled date range are shown
- The component respects the positioning and styling configured in the admin panel

