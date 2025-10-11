# Input Component Usage Guide

## Overview
The `x-input` component is a comprehensive, flexible input component that can handle all types of form inputs with consistent styling and validation.

## Basic Usage

### Text Input
```blade
<x-input 
    type="text" 
    name="name" 
    label="Full Name" 
    placeholder="Enter your name"
    required 
/>
```

### Email Input
```blade
<x-input 
    type="email" 
    name="email" 
    label="Email Address" 
    placeholder="Enter your email"
    required 
/>
```

### Password Input
```blade
<x-input 
    type="password" 
    name="password" 
    label="Password" 
    placeholder="Enter your password"
    required 
/>
```

## Number and Date Inputs

### Number Input
```blade
<x-input 
    type="number" 
    name="age" 
    label="Age" 
    min="0" 
    max="120" 
    step="1"
/>
```

### Date Input
```blade
<x-input 
    type="date" 
    name="birth_date" 
    label="Birth Date" 
/>
```

### Time Input
```blade
<x-input 
    type="time" 
    name="appointment_time" 
    label="Appointment Time" 
/>
```

## Textarea
```blade
<x-input 
    type="textarea" 
    name="description" 
    label="Description" 
    rows="4"
    placeholder="Enter a description"
/>
```

## Select Dropdowns

### Simple Select
```blade
<x-input 
    type="select" 
    name="country" 
    label="Country" 
    :options="[
        'us' => 'United States',
        'ca' => 'Canada',
        'uk' => 'United Kingdom'
    ]"
    placeholder="Select a country"
/>
```

### Multiple Select
```blade
<x-input 
    type="select" 
    name="skills" 
    label="Skills" 
    :options="[
        'html' => 'HTML',
        'css' => 'CSS',
        'js' => 'JavaScript',
        'php' => 'PHP'
    ]"
    multiple
/>
```

### Grouped Options
```blade
<x-input 
    type="select" 
    name="category" 
    label="Category" 
    :options="[
        'Frontend' => [
            'html' => 'HTML',
            'css' => 'CSS',
            'js' => 'JavaScript'
        ],
        'Backend' => [
            'php' => 'PHP',
            'python' => 'Python'
        ]
    ]"
/>
```

## Checkboxes and Radio Buttons

### Single Checkbox
```blade
<x-input 
    type="checkbox" 
    name="newsletter" 
    label="Subscribe to newsletter" 
/>
```

### Checkbox Switch
```blade
<x-input 
    type="checkbox" 
    name="notifications" 
    label="Enable Notifications" 
    switch
    color="success"
/>
```

### Radio Buttons
```blade
<x-input 
    type="radio" 
    name="gender" 
    label="Gender" 
    :options="[
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other'
    ]"
    required
/>
```

### Inline Radio Buttons
```blade
<x-input 
    type="radio" 
    name="experience" 
    label="Experience Level" 
    :options="[
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced'
    ]"
    inline
/>
```

## File Uploads

### Single File
```blade
<x-input 
    type="file" 
    name="avatar" 
    label="Profile Picture" 
    accept="image/*"
    preview
/>
```

### Multiple Files
```blade
<x-input 
    type="file" 
    name="documents" 
    label="Documents" 
    accept=".pdf,.doc,.docx"
    multiple
/>
```

### Image Gallery with Preview
```blade
<x-input 
    type="file" 
    name="gallery" 
    label="Photo Gallery" 
    accept="image/*"
    multiple
    preview
/>
```

## Range Slider
```blade
<x-input 
    type="range" 
    name="satisfaction" 
    label="Satisfaction Level" 
    min="0" 
    max="10" 
    step="1"
/>
```

## Inputs with Icons
```blade
<x-input 
    type="text" 
    name="username" 
    label="Username" 
    icon="fas fa-user"
    placeholder="Enter username"
/>
```

## Different Sizes
```blade
<!-- Small -->
<x-input 
    type="text" 
    name="small_input" 
    label="Small Input" 
    size="sm"
/>

<!-- Medium (default) -->
<x-input 
    type="text" 
    name="medium_input" 
    label="Medium Input" 
    size="md"
/>

<!-- Large -->
<x-input 
    type="text" 
    name="large_input" 
    label="Large Input" 
    size="lg"
/>
```

## Floating Labels
```blade
<x-input 
    type="text" 
    name="floating_name" 
    label="Full Name" 
    floating
    placeholder="Enter your name"
/>
```

## Custom Styling
```blade
<x-input 
    type="text" 
    name="custom_input" 
    label="Custom Styled Input" 
    input-class="border-success"
    label-class="text-success fw-bold"
    group-class="mb-4 p-3 border rounded bg-light"
/>
```

## All Available Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | 'text' | Input type (text, email, password, number, etc.) |
| `name` | string | '' | Input name attribute |
| `id` | string | '' | Input ID (auto-generated if not provided) |
| `label` | string | '' | Label text |
| `value` | mixed | '' | Input value |
| `placeholder` | string | '' | Placeholder text |
| `required` | boolean | false | Whether input is required |
| `disabled` | boolean | false | Whether input is disabled |
| `readonly` | boolean | false | Whether input is readonly |
| `class` | string | '' | Additional CSS classes |
| `labelClass` | string | '' | Additional label CSS classes |
| `inputClass` | string | '' | Additional input CSS classes |
| `errorClass` | string | '' | Additional error CSS classes |
| `helpText` | string | '' | Help text below input |
| `options` | array | [] | Options for select, radio, checkbox groups |
| `multiple` | boolean | false | For select multiple |
| `accept` | string | '' | For file inputs |
| `min` | string | '' | Minimum value |
| `max` | string | '' | Maximum value |
| `step` | string | '' | Step value for number/range inputs |
| `rows` | integer | 3 | Rows for textarea |
| `cols` | string | '' | Columns for textarea |
| `autocomplete` | string | '' | Autocomplete attribute |
| `pattern` | string | '' | Pattern for validation |
| `size` | string | '' | Size for file inputs |
| `preview` | boolean | false | Enable file preview |
| `previewClass` | string | 'img-thumbnail' | Preview image CSS class |
| `previewStyle` | string | 'max-height: 200px;' | Preview image style |
| `groupClass` | string | 'mb-3' | Group wrapper CSS class |
| `wrapperClass` | string | '' | Additional wrapper CSS class |
| `icon` | string | '' | Icon class for input with icon |
| `iconPosition` | string | 'left' | Icon position (left or right) |
| `showLabel` | boolean | true | Whether to show label |
| `inline` | boolean | false | For radio/checkbox inline |
| `switch` | boolean | false | For checkbox as switch |
| `color` | string | 'primary' | For checkbox/radio color |
| `size` | string | 'md' | Input size (sm, md, lg) |
| `floating` | boolean | false | For floating labels |
| `validation` | boolean | true | Enable/disable validation display |

## Features

- **Automatic validation display** - Shows Laravel validation errors
- **Old value support** - Maintains form values on validation errors
- **File preview** - Automatic image preview for file uploads
- **Responsive design** - Works on all screen sizes
- **Accessibility** - Proper labels and ARIA attributes
- **Customizable styling** - Extensive customization options
- **Icon support** - Input groups with icons
- **Multiple input types** - Supports all HTML5 input types
- **Bootstrap integration** - Uses Bootstrap classes and styling


