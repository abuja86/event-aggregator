# WP Event Aggregator - Pro Features

This document outlines all the pro features that have been enabled in the WP Event Aggregator plugin.

## Overview

All "pro" features are now enabled by default in this version. The `wpea_is_pro()` function has been modified to always return `true`, unlocking all premium functionality.

## Features Implemented

### 1. Week View Calendar Layout

Display events in a weekly calendar grid view with day-by-day organization.

**Usage:**
```
[wp_events layout="week"]
```

**Features:**
- 7-day week grid display
- Navigation between weeks
- Time-based event display
- Highlights current day
- Shows venue information
- Responsive design

### 2. Photo View (Gallery Layout)

Display events in a beautiful photo gallery grid with large featured images.

**Usage:**
```
[wp_events layout="photo" col="3"]
```

**Features:**
- Large photo-centric cards
- Overlay date display
- Hover effects
- Grid columns support (1-4)
- Responsive masonry layout

### 3. Map View with Location Markers

Interactive map display showing all events with location markers.

**Usage:**
```
[wp_events layout="map"]
```

**Features:**
- Interactive Leaflet.js map
- Location markers for each event
- Popup information windows
- Event sidebar with filtering
- Click event to zoom to location
- Automatic map bounds fitting

**Requirements:**
- Events must have latitude and longitude coordinates
- Leaflet.js library (loaded automatically)

### 4. Summary View (Compact List)

Compact list view ideal for sidebars and smaller spaces.

**Usage:**
```
[wp_events layout="summary"]
```

**Features:**
- Minimal compact design
- Date badge display
- Time and venue info
- Perfect for widget areas
- Clean typography

### 5. Custom Event Fields System

Add unlimited custom fields to your events with full admin interface.

**Features:**
- Custom field manager in admin
- Multiple field types:
  - Text
  - Textarea
  - Number
  - Email
  - URL
  - Date
  - Checkbox
  - Select dropdown
- Required field validation
- Database storage via Supabase
- Automatic meta box integration

**Access:**
Navigate to **WP Events → Custom Fields** in admin panel.

### 6. Elementor Widget Support

Drag-and-drop Elementor widget for easy event display.

**Features:**
- Full Elementor integration
- Live preview in Elementor editor
- All layout options
- Style controls
- Category filtering
- Custom styling options

**Usage:**
1. Edit page with Elementor
2. Search for "WP Events" widget
3. Drag to page
4. Configure options

### 7. Additional Calendar Widgets

Two new WordPress widgets for enhanced event display.

#### Upcoming Events Widget
- Shows next X upcoming events
- Thumbnail support
- Date and venue display
- Configurable count

#### Featured Events Widget
- Display featured events
- Random rotation
- Rich excerpt display
- Featured image support

**Usage:**
Navigate to **Appearance → Widgets** and add the widgets to your sidebars.

### 8. Venue & Organizer Pages

Dedicated post types for venues and organizers with custom fields.

**Venue Post Type:**
- Address management
- Geographic coordinates
- Phone and website
- Archive pages
- Individual venue pages

**Organizer Post Type:**
- Contact information
- Email and phone
- Website links
- Archive pages
- Individual organizer pages

**Access:**
Navigate to **WP Events → Venues** or **WP Events → Organizers**

### 9. Location Search Functionality

Search events by location with AJAX-powered search.

**Shortcode:**
```
[wpea_venue_search]
```

**Features:**
- Real-time location search
- City and venue filtering
- AJAX-powered results
- No page reload required

### 10. Filterbar for Events

Comprehensive filtering system for event archives.

**Features:**
- Category filter
- Date range filter (start/end)
- Location filter
- Keyword search
- Apply and reset functionality
- Clean, intuitive interface

**Usage:**
Include `wpea-filterbar.php` template in your theme or use it programmatically.

### 11. Calendar Shortcode Options

Enhanced shortcode with all new view options.

**Complete Shortcode Examples:**

Basic usage:
```
[wp_events]
```

Week view:
```
[wp_events layout="week"]
```

Photo gallery (3 columns):
```
[wp_events layout="photo" col="3" posts_per_page="12"]
```

Map view with category:
```
[wp_events layout="map" category="conferences,workshops"]
```

Summary view for sidebar:
```
[wp_events layout="summary" posts_per_page="5"]
```

Filtered events:
```
[wp_events category="music" start_date="2024-01-01" end_date="2024-12-31" order="asc"]
```

**Available Parameters:**
- `layout` - style1, style2, style3, style4, week, photo, map, summary
- `col` - 1, 2, 3, 4 (columns)
- `posts_per_page` - Number of events to display
- `category` - Category slug(s), comma-separated
- `past_events` - yes/no
- `order` - ASC/DESC
- `orderby` - post_title, meta_value, event_start_date
- `start_date` - Filter from date (YYYY-MM-DD)
- `end_date` - Filter to date (YYYY-MM-DD)
- `ajaxpagi` - yes/no (AJAX pagination)

## Database Integration

All pro features use Supabase for enhanced data storage and retrieval.

**Tables Created:**
- `wpea_custom_fields` - Custom field definitions
- `wpea_event_field_values` - Custom field values
- `wpea_filter_configs` - Saved filter configurations
- `wpea_location_index` - Location search index
- `wpea_widget_settings` - Widget configurations

**Security:**
- Row Level Security (RLS) enabled on all tables
- Public read access for frontend
- Authenticated write access for admin

## Styling

All pro features include comprehensive CSS styling located in:
```
assets/css/wp-event-aggregator-pro.css
```

**Features:**
- Responsive design
- Mobile-optimized
- Accessible color contrasts
- Modern UI components
- Smooth animations
- Cross-browser compatible

## JavaScript Features

Interactive features powered by:
```
assets/js/wp-event-aggregator-pro.js
```

**Includes:**
- Map view integration
- Week navigation
- Filter handling
- AJAX pagination
- Smooth interactions

## Events Manager Compatibility

The plugin maintains compatibility with popular event management plugins:
- The Events Calendar
- Events Manager
- Event Organiser
- EventON
- My Calendar
- Event Espresso
- And more...

## Technical Requirements

- WordPress 5.0+
- PHP 7.4+
- Supabase account (for custom fields and advanced features)
- Modern browser with JavaScript enabled
- Leaflet.js (loaded automatically for map view)

## Configuration

### Environment Variables

The plugin uses Supabase environment variables:
- `SUPABASE_URL` - Your Supabase project URL
- `SUPABASE_ANON_KEY` - Your Supabase anonymous key

These are configured in your `.env` file.

## Support

For issues, questions, or feature requests, please refer to:
- Documentation: https://docs.xylusthemes.com/docs/wp-event-aggregator/
- GitHub: https://github.com/xylusthemes/wp-event-aggregator

## Changelog

### Pro Features Release
- ✅ Week View calendar layout
- ✅ Photo View gallery layout
- ✅ Map View with interactive maps
- ✅ Summary View compact list
- ✅ Custom Event Fields system
- ✅ Elementor Widget integration
- ✅ Upcoming Events widget
- ✅ Featured Events widget
- ✅ Venue & Organizer post types
- ✅ Location search functionality
- ✅ Advanced Filterbar
- ✅ Enhanced shortcode options
- ✅ Supabase database integration
- ✅ Comprehensive styling
- ✅ Interactive JavaScript features

## License

GPL-2.0+ - Same as WordPress

---

**Note:** This is an enhanced version of WP Event Aggregator with all pro features enabled. Original plugin by Xylus Themes.
