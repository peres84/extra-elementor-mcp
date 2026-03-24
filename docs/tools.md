# Elementor MCP Tools Reference

## Current Available Tools

### Discovery & Inspection
| Tool | Description |
|------|-------------|
| `list-pages` | List all Elementor-built pages/posts. Filter by post_type and status. |
| `list-templates` | List all saved Elementor templates. Filter by type (page, section, container). |
| `list-widgets` | List all registered widget types with names, icons, categories, and keywords. |
| `get-page-structure` | Get the full element tree of a page — containers, widgets, nesting, and settings summary. |
| `get-element-settings` | Get the complete settings object of a specific element (widget or container). |
| `get-widget-schema` | Get the full JSON Schema for a widget type — all available controls and their types. |
| `get-container-schema` | Get the JSON Schema for container controls (flex, grid, background, border, etc). |
| `get-global-settings` | Get site-wide Elementor kit settings: colors, typography, spacing, breakpoints. |
| `find-element` | Search elements on a page by type, widget type, or text content in settings. |
| `list-dynamic-tags` | List available Elementor dynamic tags for dynamic content. |
| `list-code-snippets` | List custom code snippets registered in the site. |

### Page & Template Management
| Tool | Description |
|------|-------------|
| `create-page` | Create a new WordPress page with Elementor enabled. Optional initial content. |
| `build-page` | Create a complete page from a declarative structure in one call. Supports nested containers and any widget. |
| `delete-page-content` | Clear all Elementor content from a page, keeping the page itself. |
| `apply-template` | Apply a saved template to a page at a given position with fresh element IDs. |
| `save-as-template` | Save a page's content as a reusable template. |
| `import-template` | Import a JSON template structure into a page. |
| `export-page` | Export a page's full Elementor data as JSON. |
| `update-page-settings` | Update page-level settings (background, padding, custom CSS, layout). |
| `create-theme-template` | Create a theme builder template (header, footer, single, archive, 404, etc). |
| `set-template-conditions` | Set display conditions for a theme template (Entire Site, specific pages, etc). |
| `create-popup` | Create a new Elementor popup. |
| `set-popup-settings` | Configure popup settings (triggers, conditions, timing). |

### Element CRUD Operations
| Tool | Description |
|------|-------------|
| `add-container` | Add a flex or grid container. Supports nesting, background, border, padding, etc. |
| `update-container` | Update settings on an existing container (partial merge). |
| `update-element` | Update any element (container or widget) settings. |
| `update-widget` | Update settings on an existing widget (partial merge). |
| `batch-update` | Update multiple elements in a single save operation. Much more efficient than individual updates. |
| `remove-element` | Remove an element and all its children. |
| `duplicate-element` | Duplicate an element with fresh IDs, placed after the original. |
| `move-element` | Move an element to a new parent container and/or position. |
| `reorder-elements` | Reorder children of a container by providing ordered element ID array. |
| `set-dynamic-tag` | Bind a dynamic tag to a widget setting. |

### Widget Add Tools (Dedicated)
| Tool | Description |
|------|-------------|
| `add-heading` | Add heading widget. Full typography, stroke, shadow, blend mode support. |
| `add-text-editor` | Add rich text editor widget. Typography, drop cap, text columns. |
| `add-image` | Add image widget. Width, filters, border, box shadow, hover effects. |
| `add-button` | Add button widget. Typography, border, background, hover colors, shadow. |
| `add-video` | Add video widget. YouTube, Vimeo, Dailymotion, self-hosted. Overlay support. |
| `add-icon` | Add icon widget. Font Awesome, SVG. Default/stacked/framed views. |
| `add-icon-box` | Add icon box widget. Icon + title + description. Great for service cards. |
| `add-icon-list` | Add icon list widget. Feature lists, checklists, contact info. |
| `add-image-box` | Add image box widget. Image + title + description. |
| `add-image-carousel` | Add rotating image carousel/slider. |
| `add-divider` | Add horizontal divider/separator. |
| `add-spacer` | Add vertical spacing widget. |
| `add-button` | Add button widget with link, colors, hover effects. |
| `add-counter` | Add animated counter widget that counts up to a number. |
| `add-progress` | Add animated progress bar with label and percentage. |
| `add-accordion` | Add accordion widget. Supports FAQ schema markup. |
| `add-tabs` | Add tabbed content widget. Horizontal or vertical layout. |
| `add-toggle` | Add toggle widget (multiple items can be open). |
| `add-testimonial` | Add testimonial widget. Quote, author, job, image. |
| `add-social-icons` | Add social media icon links. |
| `add-star-rating` | Add star rating display. |
| `add-rating` | Add star/icon rating widget with custom scale. |
| `add-google-maps` | Add embedded Google Maps. |
| `add-html` | Add custom HTML code widget. |
| `add-shortcode` | Add WordPress shortcode widget. |
| `add-menu-anchor` | Add menu anchor for one-page navigation. |
| `add-alert` | Add alert/notice widget (info, success, warning, danger). |
| `add-animated-headline` | Add animated headline with rotating/highlighted text. |
| `add-blockquote` | Add styled blockquote widget. |
| `add-call-to-action` | Add CTA widget with image, title, description, button. |
| `add-countdown` | Add countdown timer widget. |
| `add-flip-box` | Add flip box widget (front/back content). |
| `add-form` | Add Elementor Pro form widget. |
| `add-gallery` | Add justified gallery widget. |
| `add-hotspot` | Add image hotspot widget with tooltips. |
| `add-login` | Add login form widget. |
| `add-loop-grid` | Add loop grid for dynamic content. |
| `add-loop-carousel` | Add loop carousel for dynamic content. |
| `add-lottie` | Add Lottie animation widget. |
| `add-media-carousel` | Add media carousel (images + videos). |
| `add-nav-menu` | Add WordPress navigation menu widget. |
| `add-nested-accordion` | Add nested accordion widget. |
| `add-nested-tabs` | Add nested tabs widget. |
| `add-off-canvas` | Add off-canvas panel widget. |
| `add-portfolio` | Add portfolio grid widget. |
| `add-posts-grid` | Add posts grid widget. |
| `add-price-list` | Add price list widget (menu-style). |
| `add-price-table` | Add pricing table widget. |
| `add-progress-tracker` | Add reading progress tracker. |
| `add-reviews` | Add reviews widget with social proof. |
| `add-search` | Add search form widget. |
| `add-share-buttons` | Add social share buttons. |
| `add-slides` | Add full-width slides widget. |
| `add-table-of-contents` | Add table of contents widget. |
| `add-testimonial-carousel` | Add testimonial carousel widget. |
| `add-text-path` | Add text path (wordart) widget. |
| `add-code-highlight` | Add code syntax highlighting widget. |
| `add-code-snippet` | Add code snippet widget. |

### Styling & Globals
| Tool | Description |
|------|-------------|
| `update-global-colors` | Update the site-wide Elementor color palette. |
| `update-global-typography` | Update site-wide typography presets. |
| `add-custom-css` | Add custom CSS to a page or element. |
| `add-custom-js` | Add custom JavaScript to a page. |

### Media & Images
| Tool | Description |
|------|-------------|
| `search-images` | Search Openverse for Creative Commons licensed images. |
| `sideload-image` | Download an external image URL into WordPress Media Library. |
| `add-stock-image` | Search, download, and add an image widget — all in one step. |
| `upload-svg-icon` | Upload an SVG icon to the media library for use in widgets. |

---

## Extra Tools Needed (Not Yet Available)

### WordPress Core Management
| Tool | Description |
|------|-------------|
| `manage-menus` | Create, update, and assign WordPress navigation menus (primary, footer, etc). Currently no way to programmatically set menu items or assign menus to theme locations. |
| `manage-media` | Upload local files to the WordPress Media Library. Currently `sideload-image` only works with URLs, not local file paths. |
| `manage-pages-settings` | Set page as homepage, set reading settings, manage page slug/permalink. Currently can create pages but can't assign them as the front page. |
| `manage-plugins` | List, activate, deactivate WordPress plugins. Useful for checking if required plugins (ElementsKit, etc) are active. |

### Content & SEO
| Tool | Description |
|------|-------------|
| `manage-seo` | Set SEO meta titles, descriptions, Open Graph data, and schema markup for pages. Integration with Yoast/RankMath if installed. |
| `manage-translations` | Handle multilingual content (EN/ES as needed). Create translated versions of pages if WPML/Polylang is installed. |

### Theme & Site Settings
| Tool | Description |
|------|-------------|
| `manage-site-identity` | Set site title, tagline, favicon, and site icon through WordPress settings. |
| `manage-custom-fonts` | Upload and register custom fonts for use in Elementor typography settings. |
| `get-theme-info` | Get current theme details, installed plugins, PHP version, and WordPress version for compatibility checks. |

### ElementsKit Specific
| Tool | Description |
|------|-------------|
| `manage-elementskit-widgets` | Get/set settings for ElementsKit-specific widgets (icon-box, testimonial, client-logo, progressbar, etc). Currently we update them via `batch-update` but need to guess setting keys. A schema tool for these widgets would help. |

### Workflow & Preview
| Tool | Description |
|------|-------------|
| `publish-page` | Publish a draft page or change page status without going to wp-admin. |
| `get-page-preview-url` | Generate a proper preview URL with nonce for draft pages. |
| `revision-history` | View and restore Elementor revision history for a page. Useful for undoing mistakes. |
