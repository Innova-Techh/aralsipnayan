# SweetAlert Toast Component Documentation

## Overview
A reusable SweetAlert2 toast notification component with 3D effects and full responsive design for all device sizes.

## Installation

### 1. Include the component in your Blade file:
```blade
@include('components.sweetalert-config')
```

### 2. Make sure you have Vite assets loaded (for SweetAlert2):
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## Usage

### Basic Usage

The component provides several helper functions for showing toasts:

#### 1. **Warning Toast** (Purple - Default for "no answer" scenarios)
```javascript
showWarningToast('Please select an answer!');
showWarningToast('Please fill in all required fields!', 5000); // 5 seconds
```

#### 2. **Error Toast** (Red - For errors)
```javascript
showErrorToast('Something went wrong!');
showErrorToast('Failed to submit your answer.', 4000);
```

#### 3. **Success Toast** (Green - For success messages)
```javascript
showSuccessToast('Answer submitted successfully!');
showSuccessToast('Quiz completed!', 3000);
```

#### 4. **Info Toast** (Blue - For informational messages)
```javascript
showInfoToast('Loading next question...');
showInfoToast('Please wait...', 2000);
```

### Advanced Usage

Use the main function with custom parameters:

```javascript
window.showCustomToast(type, message, timer);

// Examples:
showCustomToast('warning', 'Custom warning message', 3000);
showCustomToast('success', 'Operation successful!', 2500);
showCustomToast('error', 'An error occurred', 4000);
showCustomToast('info', 'Did you know?', 3500);
```

## Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `type` | string | 'warning' | Toast type: 'warning', 'error', 'success', 'info' |
| `message` | string | '' | The message to display |
| `timer` | number | 3500 | Duration in milliseconds |

## Features

### 🎨 **3D Effects**
- Elevated shadow with depth
- Inset shadow for button press effect
- Hover animation with transform
- Smooth transitions

### 📱 **Responsive Design**
- **Desktop** (1920px+): 380px width
- **Laptop** (1024-1919px): 340px width
- **Tablet** (768-1023px): 320px width
- **Large Phone** (640-767px): 300px width
- **Medium Phone** (480-639px): 280px width
- **Small Phone** (<480px): 260px width

### 🎯 **Color Scheme** (Solid Colors)
- **Warning**: Purple (#9333EA)
- **Error**: Red (#DC2626)
- **Success**: Green (#059669)
- **Info**: Blue (#2563EB)

### ✨ **Animations**
- Slide in from right
- Fade in effect
- Timer progress bar
- Hover lift effect

## Example Implementation

### In Quiz Blade File:
```blade
<!-- Include at the bottom before closing body tag -->
@include('components.sweetalert-config')

<script>
    function submitAnswer() {
        let answer = getAnswer();
        
        if (!answer) {
            // Show warning toast
            showWarningToast('Please select an answer before submitting! 🎯');
            return;
        }
        
        // Submit answer...
        fetch('/submit', { ... })
            .then(response => {
                if (response.ok) {
                    showSuccessToast('Answer submitted successfully! ✓');
                } else {
                    showErrorToast('Failed to submit. Please try again.');
                }
            })
            .catch(error => {
                showErrorToast('Network error. Please check your connection.');
            });
    }
</script>
```

### In Form Validation:
```javascript
// Check if fill-in-the-blank is empty
if (!fillAnswer.value.trim()) {
    showWarningToast('Please type your answer in the text box! 📝');
    return;
}

// Check if multiple choice is selected
if (!selectedAnswer) {
    showWarningToast('Please select one of the answer choices! 🎯');
    return;
}
```

### In Other Scenarios:
```javascript
// Loading state
showInfoToast('Processing your request...');

// Success confirmation
showSuccessToast('Settings saved successfully!');

// Error handling
showErrorToast('Unable to save. Please try again.');

// Warning/Reminder
showWarningToast('You have 5 minutes remaining!');
```

## Browser Compatibility
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

## Notes
- Toast auto-closes after timer expires
- Hover to pause the timer
- Leave hover to resume timer
- Non-blocking UI
- Positioned at top-right corner
- Stacks multiple toasts vertically

## Troubleshooting

### Toast not showing?
1. Make sure `@vite(['resources/js/app.js'])` is included
2. Check that SweetAlert2 is imported in `app.js`
3. Verify `npm run build` or `npm run dev` is running
4. Check browser console for errors

### Styling issues?
1. Clear browser cache
2. Run `npm run build` again
3. Check for CSS conflicts

## License
Free to use within the AralSipnayan project.
