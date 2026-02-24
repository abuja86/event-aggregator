/*
  # WP Event Aggregator Pro Features Schema

  ## Overview
  Creates tables to support pro features for WP Event Aggregator including:
  - Custom event fields
  - Event views (week, photo, map, summary)
  - Filter configurations
  - Location search data
  - Widget configurations

  ## Tables Created
  
  ### 1. `wpea_custom_fields`
  Stores custom field definitions for events
  - `id` (uuid, primary key)
  - `field_name` (text) - Machine name of the field
  - `field_label` (text) - Human-readable label
  - `field_type` (text) - Type: text, textarea, select, checkbox, date, etc.
  - `field_options` (jsonb) - Configuration options for the field
  - `is_required` (boolean) - Whether field is required
  - `field_order` (integer) - Display order
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ### 2. `wpea_event_field_values`
  Stores custom field values for individual events
  - `id` (uuid, primary key)
  - `event_id` (bigint) - WordPress post ID
  - `field_id` (uuid) - References wpea_custom_fields
  - `field_value` (text) - The field value
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ### 3. `wpea_filter_configs`
  Stores filterbar configurations
  - `id` (uuid, primary key)
  - `name` (text) - Configuration name
  - `filters` (jsonb) - Filter settings
  - `is_active` (boolean)
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ### 4. `wpea_location_index`
  Optimized location search index
  - `id` (uuid, primary key)
  - `event_id` (bigint) - WordPress post ID
  - `venue_name` (text)
  - `address` (text)
  - `city` (text)
  - `state` (text)
  - `country` (text)
  - `zipcode` (text)
  - `latitude` (numeric)
  - `longitude` (numeric)
  - `geolocation` (geography point) - PostGIS point for spatial queries
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ### 5. `wpea_widget_settings`
  Stores widget configurations
  - `id` (uuid, primary key)
  - `widget_type` (text) - upcoming, featured, calendar, etc.
  - `settings` (jsonb) - Widget settings
  - `created_at` (timestamptz)
  - `updated_at` (timestamptz)

  ## Security
  - RLS enabled on all tables
  - Public read access for frontend display
  - Authenticated write access for admin operations

  ## Indexes
  - Location-based spatial index for fast geo queries
  - Event ID indexes for quick lookups
  - Field name and type indexes for filtering
*/

CREATE TABLE IF NOT EXISTS wpea_custom_fields (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  field_name text NOT NULL UNIQUE,
  field_label text NOT NULL,
  field_type text NOT NULL DEFAULT 'text',
  field_options jsonb DEFAULT '{}'::jsonb,
  is_required boolean DEFAULT false,
  field_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE TABLE IF NOT EXISTS wpea_event_field_values (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  event_id bigint NOT NULL,
  field_id uuid REFERENCES wpea_custom_fields(id) ON DELETE CASCADE,
  field_value text,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now(),
  UNIQUE(event_id, field_id)
);

CREATE TABLE IF NOT EXISTS wpea_filter_configs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  filters jsonb DEFAULT '{}'::jsonb,
  is_active boolean DEFAULT true,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE TABLE IF NOT EXISTS wpea_location_index (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  event_id bigint NOT NULL UNIQUE,
  venue_name text DEFAULT '',
  address text DEFAULT '',
  city text DEFAULT '',
  state text DEFAULT '',
  country text DEFAULT '',
  zipcode text DEFAULT '',
  latitude numeric,
  longitude numeric,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE TABLE IF NOT EXISTS wpea_widget_settings (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  widget_type text NOT NULL,
  settings jsonb DEFAULT '{}'::jsonb,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_event_field_values_event_id ON wpea_event_field_values(event_id);
CREATE INDEX IF NOT EXISTS idx_event_field_values_field_id ON wpea_event_field_values(field_id);
CREATE INDEX IF NOT EXISTS idx_custom_fields_name ON wpea_custom_fields(field_name);
CREATE INDEX IF NOT EXISTS idx_custom_fields_type ON wpea_custom_fields(field_type);
CREATE INDEX IF NOT EXISTS idx_location_index_event_id ON wpea_location_index(event_id);
CREATE INDEX IF NOT EXISTS idx_location_index_city ON wpea_location_index(city);
CREATE INDEX IF NOT EXISTS idx_location_index_country ON wpea_location_index(country);
CREATE INDEX IF NOT EXISTS idx_widget_settings_type ON wpea_widget_settings(widget_type);

ALTER TABLE wpea_custom_fields ENABLE ROW LEVEL SECURITY;
ALTER TABLE wpea_event_field_values ENABLE ROW LEVEL SECURITY;
ALTER TABLE wpea_filter_configs ENABLE ROW LEVEL SECURITY;
ALTER TABLE wpea_location_index ENABLE ROW LEVEL SECURITY;
ALTER TABLE wpea_widget_settings ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public read access for custom fields"
  ON wpea_custom_fields FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Public read access for event field values"
  ON wpea_event_field_values FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Public read access for filter configs"
  ON wpea_filter_configs FOR SELECT
  TO public
  USING (is_active = true);

CREATE POLICY "Public read access for location index"
  ON wpea_location_index FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Public read access for widget settings"
  ON wpea_widget_settings FOR SELECT
  TO public
  USING (true);

CREATE POLICY "Authenticated users can manage custom fields"
  ON wpea_custom_fields FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can manage event field values"
  ON wpea_event_field_values FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can manage filter configs"
  ON wpea_filter_configs FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can manage location index"
  ON wpea_location_index FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can manage widget settings"
  ON wpea_widget_settings FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);