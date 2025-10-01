# Section Header Component

A reusable responsive section header component for Laravel Blade templates.

## Usage

```blade
<x-frontend.components.section-header
    title="Your Section Title"
    description="Your section description"
    button-text="Button Text"
    button-url="{{ route('your.route') }}"
    button-icon="fas fa-icon-name"
    button-color="bg-blue-600 hover:bg-blue-700"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | The main heading text |
| `description` | string | `''` | The description text below the title |
| `button-text` | string | `'View All'` | The button text |
| `button-url` | string | `'#'` | The button URL/link |
| `button-icon` | string | `'fas fa-arrow-right'` | FontAwesome icon class |
| `button-color` | string | `'bg-blue-600 hover:bg-blue-700'` | Button color classes |
| `title-color` | string | `'text-gray-900'` | Title text color |
| `description-color` | string | `'text-gray-600'` | Description text color |

## Examples

### Basic Usage
```blade
<x-frontend.components.section-header
    title="Products"
    description="Browse our product catalog"
    button-text="View All Products"
    button-url="{{ route('products') }}"
/>
```

### Custom Styling
```blade
<x-frontend.components.section-header
    title="Special Offers"
    description="Limited time deals"
    button-text="Shop Now"
    button-url="{{ route('offers') }}"
    button-icon="fas fa-shopping-cart"
    button-color="bg-red-600 hover:bg-red-700"
    title-color="text-red-900"
    description-color="text-red-600"
/>
```

### Without Button
```blade
<x-frontend.components.section-header
    title="About Us"
    description="Learn more about our company"
    button-text=""
    button-url=""
/>
```

## Features

- **Responsive Design**: Automatically adapts to different screen sizes
- **Mobile-First**: Optimized for mobile devices with proper touch targets
- **Customizable**: All colors, icons, and text can be customized
- **Accessible**: Proper semantic HTML structure
- **Consistent**: Ensures consistent styling across all sections

## Responsive Behavior

- **Mobile (< 640px)**: Stacked layout with centered text
- **Small screens (640px+)**: Slightly larger text and spacing
- **Large screens (1024px+)**: Horizontal layout with left-aligned text
