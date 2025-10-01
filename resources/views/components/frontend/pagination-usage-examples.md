# Pagination Component Usage Examples

## Basic Usage

```blade
<x-pagination :paginator="$items" />
```

## Advanced Usage with Custom Options

```blade
<x-pagination 
    :paginator="$products" 
    container-class="mt-8"
    :show-info="true"
    :max-pages="5"
    :show-first-last="true"
    previous-text="Previous"
    next-text="Next"
    first-text="First"
    last-text="Last"
/>
```

## Available Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `paginator` | Paginator | null | The paginated data object |
| `containerClass` | String | 'mt-12' | CSS classes for the container |
| `showInfo` | Boolean | true | Show pagination info (e.g., "Showing 1 to 10 of 50 results") |
| `infoClass` | String | 'text-center mt-4 text-sm text-gray-500' | CSS classes for the info text |
| `previousText` | String | 'Previous' | Text for the previous button |
| `nextText` | String | 'Next' | Text for the next button |
| `maxPages` | Integer | null | Maximum number of page buttons to show |
| `showFirstLast` | Boolean | false | Show first and last page buttons |
| `firstText` | String | 'First' | Text for the first page button |
| `lastText` | String | 'Last' | Text for the last page button |

## Usage Examples for Different Modules

### Products Module
```blade
<x-pagination 
    :paginator="$products" 
    container-class="mt-12"
    :show-info="true"
    :max-pages="7"
/>
```

### Blog Posts Module
```blade
<x-pagination 
    :paginator="$posts" 
    container-class="mt-8"
    :show-info="true"
    :max-pages="5"
    :show-first-last="true"
    previous-text="Older"
    next-text="Newer"
/>
```

### Jobs Module
```blade
<x-pagination 
    :paginator="$jobs" 
    container-class="mt-6"
    :show-info="false"
    :max-pages="10"
/>
```

### Admin Dashboard
```blade
<x-pagination 
    :paginator="$users" 
    container-class="mt-4"
    :show-info="true"
    :max-pages="5"
    :show-first-last="true"
    info-class="text-center mt-2 text-xs text-gray-400"
/>
```

## Features

- **Responsive Design**: Works on all screen sizes
- **Smart Page Range**: Shows ellipsis (...) when there are many pages
- **Customizable Styling**: All CSS classes can be customized
- **Accessibility**: Proper ARIA labels and keyboard navigation
- **Icon Support**: Uses Font Awesome icons for navigation
- **Hover Effects**: Smooth transitions and scale effects
- **Disabled States**: Proper styling for disabled buttons
- **Flexible Configuration**: Many options to customize behavior

