# Design Document: Dynamic Content Management System

## Overview

This design document specifies the technical architecture for transforming the SCTI school website from a static/semi-static system into a fully dynamic content management system (CMS). The system will enable administrators to manage all website content through an intuitive admin dashboard without requiring code modifications.

### Goals

- Enable dynamic management of all website content (home page, gallery, programs, notices, contact information)
- Implement role-based access control for admins, teachers, and students
- Provide a user-friendly WYSIWYG editor for content creation
- Implement content versioning and audit trails
- Optimize performance through intelligent caching
- Support media library management with file uploads
- Enable content scheduling and publishing workflows

### Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL 8.0+
- **Frontend**: Vanilla JavaScript (existing SPA architecture)
- **WYSIWYG Editor**: TinyMCE or CKEditor
- **Caching**: File-based caching with automatic invalidation
- **File Storage**: Local filesystem with database metadata tracking
- **Authentication**: Session-based with bcrypt password hashing

### Design Principles

- Maintain compatibility with existing SPA architecture
- Minimize breaking changes to current codebase
- Prioritize security (input validation, XSS prevention, CSRF protection)
- Ensure responsive design for mobile content management
- Implement progressive enhancement for better user experience

## Architecture

### System Architecture

The CMS follows a three-tier architecture:

1. **Presentation Layer**: Admin dashboard UI + Public website
2. **Application Layer**: PHP API endpoints for CRUD operations
3. **Data Layer**: MySQL database + File storage

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
│  ┌──────────────────────┐    ┌──────────────────────┐      │
│  │  Admin Dashboard     │    │   Public Website     │      │
│  │  (admin/dashboard/)  │    │   (index.html)       │      │
│  └──────────────────────┘    └──────────────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                         │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              API Endpoints (api/)                     │  │
│  │  - Content API    - Media API    - User API          │  │
│  │  - Cache Manager  - Auth Manager - Version Control   │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                       Data Layer                             │
│  ┌──────────────────┐              ┌──────────────────┐    │
│  │  MySQL Database  │              │  File Storage    │    │
│  │  (Content, Users)│              │  (Media Library) │    │
│  └──────────────────┘              └──────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
scti-school/
├── admin/
│   ├── dashboard/
│   │   ├── index.php                 # Main admin dashboard
│   │   ├── content-editor.php        # Content editing interface
│   │   ├── media-library.php         # Media management
│   │   ├── programs-manager.php      # Programs CRUD
│   │   ├── notices-manager.php       # Notices CRUD
│   │   ├── gallery-manager.php       # Gallery management
│   │   ├── contact-manager.php       # Contact info & submissions
│   │   └── settings.php              # System settings
│   └── assets/
│       ├── css/
│       │   └── admin.css             # Admin-specific styles
│       └── js/
│           ├── admin.js              # Admin dashboard logic
│           ├── content-editor.js     # WYSIWYG integration
│           └── media-uploader.js     # File upload handling
├── api/
│   ├── content/
│   │   ├── home.php                  # Home page content API
│   │   ├── programs.php              # Programs API
│   │   ├── notices.php               # Notices API
│   │   ├── gallery.php               # Gallery API
│   │   └── contact.php               # Contact info API
│   ├── media/
│   │   ├── upload.php                # File upload handler
│   │   ├── list.php                  # Media library listing
│   │   └── delete.php                # File deletion
│   ├── auth/
│   │   ├── check-permission.php      # Permission verification
│   │   └── session-validate.php     # Session validation
│   └── cache/
│       └── invalidate.php            # Cache invalidation
├── uploads/
│   ├── images/                       # Uploaded images
│   ├── documents/                    # Uploaded documents
│   └── thumbnails/                   # Generated thumbnails
├── cache/
│   ├── pages/                        # Cached page content
│   └── data/                         # Cached data objects
└── includes/
    ├── CacheManager.php              # Caching logic
    ├── ContentManager.php            # Content operations
    ├── MediaManager.php              # Media operations
    ├── VersionControl.php            # Content versioning
    └── PermissionManager.php         # Access control
```

### API Design

All API endpoints follow RESTful conventions and return JSON responses.

#### Standard Response Format

```json
{
  "success": true|false,
  "data": {},
  "message": "Human-readable message",
  "errors": []
}
```

#### Content API Endpoints

**Home Page Content**
- `GET /api/content/home.php` - Retrieve home page content
- `PUT /api/content/home.php` - Update home page content (admin only)

**Programs**
- `GET /api/content/programs.php` - List all active programs
- `GET /api/content/programs.php?id={id}` - Get single program
- `POST /api/content/programs.php` - Create new program (admin only)
- `PUT /api/content/programs.php?id={id}` - Update program (admin only)
- `DELETE /api/content/programs.php?id={id}` - Delete program (admin only)

**Notices**
- `GET /api/content/notices.php` - List active notices
- `GET /api/content/notices.php?id={id}` - Get single notice
- `POST /api/content/notices.php` - Create notice (admin only)
- `PUT /api/content/notices.php?id={id}` - Update notice (admin only)
- `DELETE /api/content/notices.php?id={id}` - Delete notice (admin only)

**Gallery**
- `GET /api/content/gallery.php` - List gallery images
- `GET /api/content/gallery.php?category={cat}` - Filter by category
- `POST /api/content/gallery.php` - Add gallery image (admin only)
- `PUT /api/content/gallery.php?id={id}` - Update image metadata (admin only)
- `DELETE /api/content/gallery.php?id={id}` - Remove image (admin only)

**Contact Information**
- `GET /api/content/contact.php` - Get contact information
- `PUT /api/content/contact.php` - Update contact info (admin only)
- `GET /api/content/contact.php?submissions=true` - List contact form submissions (admin only)

#### Media API Endpoints

- `POST /api/media/upload.php` - Upload file (admin only)
- `GET /api/media/list.php` - List media library files (admin only)
- `DELETE /api/media/delete.php?id={id}` - Delete file (admin only)

#### Authentication & Authorization

All admin endpoints require:
1. Valid session with admin role
2. CSRF token validation
3. Permission check via `PermissionManager`

## Components and Interfaces

### 1. Content Manager Component

**Purpose**: Handle all content CRUD operations with versioning support.

**Class**: `ContentManager`

**Methods**:
```php
class ContentManager {
    public function getContent(string $type, ?int $id = null): array
    public function createContent(string $type, array $data): int
    public function updateContent(string $type, int $id, array $data): bool
    public function deleteContent(string $type, int $id): bool
    public function publishContent(int $id, string $type): bool
    public function scheduleContent(int $id, string $type, DateTime $publishDate): bool
    public function getVersionHistory(string $type, int $id): array
    public function restoreVersion(string $type, int $id, int $versionId): bool
}
```

### 2. Media Manager Component

**Purpose**: Handle file uploads, storage, and media library operations.

**Class**: `MediaManager`

**Methods**:
```php
class MediaManager {
    public function uploadFile(array $file, string $type): array
    public function validateFile(array $file): bool
    public function generateThumbnail(string $filePath, int $width, int $height): string
    public function deleteFile(int $mediaId): bool
    public function getMediaList(array $filters = []): array
    public function checkFileUsage(int $mediaId): array
    public function optimizeImage(string $filePath): bool
}
```

### 3. Cache Manager Component

**Purpose**: Manage page and data caching with automatic invalidation.

**Class**: `CacheManager`

**Methods**:
```php
class CacheManager {
    public function get(string $key): ?string
    public function set(string $key, string $value, int $ttl = 3600): bool
    public function invalidate(string $key): bool
    public function invalidatePattern(string $pattern): int
    public function clear(): bool
    public function getCacheStats(): array
}
```

### 4. Version Control Component

**Purpose**: Track content changes and enable rollback functionality.

**Class**: `VersionControl`

**Methods**:
```php
class VersionControl {
    public function saveVersion(string $contentType, int $contentId, array $data, int $userId): int
    public function getVersions(string $contentType, int $contentId): array
    public function compareVersions(int $versionId1, int $versionId2): array
    public function restoreVersion(int $versionId): bool
    public function pruneOldVersions(int $keepCount = 10): int
}
```

### 5. Permission Manager Component

**Purpose**: Handle role-based access control and permission checks.

**Class**: `PermissionManager`

**Methods**:
```php
class PermissionManager {
    public function checkPermission(int $userId, string $permission): bool
    public function getUserRole(int $userId): string
    public function canManageContent(int $userId, string $contentType): bool
    public function logAccessAttempt(int $userId, string $action, bool $granted): void
    public function getPermissions(string $role): array
}
```

### 6. Admin Dashboard UI Component

**Purpose**: Provide intuitive interface for content management.

**Key Features**:
- Dashboard overview with statistics
- Content editor with WYSIWYG support
- Media library browser with drag-and-drop upload
- Bulk operations interface
- Search and filter capabilities
- Responsive design for mobile management

**JavaScript Modules**:
```javascript
// admin.js - Main dashboard logic
class AdminDashboard {
    constructor()
    loadStatistics()
    initializeEventListeners()
    handleBulkOperations()
}

// content-editor.js - WYSIWYG integration
class ContentEditor {
    constructor(elementId)
    initialize()
    getContent()
    setContent(html)
    insertMedia(mediaUrl)
    sanitizeContent()
}

// media-uploader.js - File upload handling
class MediaUploader {
    constructor(dropZoneId)
    handleDrop(event)
    uploadFile(file)
    showProgress(percent)
    handleError(error)
}
```

## Data Models

### Database Schema

#### 1. home_content Table

Stores dynamic home page content sections.

```sql
CREATE TABLE home_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) UNIQUE NOT NULL,
    section_title VARCHAR(255),
    content_html TEXT,
    content_text TEXT,
    image_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_section_key (section_key),
    INDEX idx_active (is_active)
);
```

**Section Keys**: `hero`, `about`, `features`, `testimonials`, `stats`

#### 2. programs Table (Enhanced)

Extended version of existing programs concept.

```sql
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    short_description TEXT,
    full_description TEXT,
    duration VARCHAR(100),
    requirements TEXT,
    fees DECIMAL(10, 2),
    image_url VARCHAR(255),
    brochure_url VARCHAR(255),
    status ENUM('draft', 'active', 'inactive', 'archived') DEFAULT 'draft',
    display_order INT DEFAULT 0,
    enrollment_open BOOLEAN DEFAULT TRUE,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
);
```

#### 3. notices Table (Enhanced)

Extended version with publishing workflow.

```sql
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content_html TEXT NOT NULL,
    content_text TEXT NOT NULL,
    category ENUM('general', 'academic', 'event', 'urgent', 'admission', 'exam', 'holiday') NOT NULL,
    priority ENUM('normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('draft', 'scheduled', 'published', 'archived') DEFAULT 'draft',
    publish_date DATETIME,
    expiry_date DATETIME,
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_publish_date (publish_date),
    INDEX idx_expiry_date (expiry_date)
);
```

#### 4. gallery_images Table

Stores gallery images with metadata and categorization.

```sql
CREATE TABLE gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    category VARCHAR(100),
    album VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    media_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (media_id) REFERENCES media_library(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_album (album),
    INDEX idx_active (is_active),
    INDEX idx_display_order (display_order)
);
```

#### 5. contact_info Table

Stores contact page information.

```sql
CREATE TABLE contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    info_key VARCHAR(50) UNIQUE NOT NULL,
    info_value TEXT NOT NULL,
    info_type ENUM('text', 'email', 'phone', 'address', 'hours', 'social') NOT NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_info_key (info_key),
    INDEX idx_active (is_active)
);
```

**Info Keys**: `address`, `phone`, `email`, `office_hours`, `facebook`, `twitter`, `instagram`

#### 6. media_library Table

Central repository for all uploaded files.

```sql
CREATE TABLE media_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image', 'document', 'video', 'other') NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    width INT,
    height INT,
    thumbnail_path VARCHAR(255),
    alt_text VARCHAR(255),
    caption TEXT,
    uploaded_by INT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_file_type (file_type),
    INDEX idx_upload_date (upload_date)
);
```

#### 7. content_versions Table

Tracks all content changes for audit and rollback.

```sql
CREATE TABLE content_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('home', 'program', 'notice', 'gallery', 'contact') NOT NULL,
    content_id INT NOT NULL,
    version_number INT NOT NULL,
    content_data JSON NOT NULL,
    change_summary VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_content_lookup (content_type, content_id),
    INDEX idx_created_at (created_at)
);
```

#### 8. content_cache Table

Stores cached content for performance optimization.

```sql
CREATE TABLE content_cache (
    cache_key VARCHAR(255) PRIMARY KEY,
    cache_value LONGTEXT NOT NULL,
    cache_type ENUM('page', 'data', 'fragment') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expires (expires_at),
    INDEX idx_type (cache_type)
);
```

#### 9. access_log Table

Logs admin access attempts for security auditing.

```sql
CREATE TABLE access_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_type ENUM('admin', 'teacher', 'student') NOT NULL,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50),
    resource_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    access_granted BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_action (action)
);
```

### Data Relationships

```
admins
  ├── home_content (created_by, updated_by)
  ├── programs (created_by, updated_by)
  ├── notices (created_by, updated_by)
  ├── gallery_images (created_by)
  ├── contact_info (updated_by)
  ├── media_library (uploaded_by)
  ├── content_versions (created_by)
  └── access_log (user_id)

media_library
  └── gallery_images (media_id)

content_versions
  ├── home_content (content_type='home', content_id)
  ├── programs (content_type='program', content_id)
  ├── notices (content_type='notice', content_id)
  ├── gallery_images (content_type='gallery', content_id)
  └── contact_info (content_type='contact', content_id)
```


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, I identified several areas of redundancy:

- Properties 1.1 and 1.2 (storing text and image references) can be combined into a single property about home content persistence
- Properties 2.1 and 2.2 (uploading images and storing metadata) can be combined into a single property about media upload with metadata
- Properties 4.1 and 4.2 (creating and editing programs) both test CRUD persistence and can be covered by a single round-trip property
- Properties 5.1 and 5.2 (creating notices and supporting categories) can be combined into a single property about notice persistence
- Properties 8.1 and 8.2 (saving versions and recording metadata) can be combined into a single property about version creation with audit trail
- Properties 15.1 and 15.3 (caching content and serving to anonymous users) can be combined into a single property about cache serving

The following properties provide comprehensive, non-redundant coverage of the testable requirements:

### Property 1: Home Content Round-Trip

*For any* home page content section (with text, images, and metadata), storing it in the database and then retrieving it should return content equivalent to what was stored.

**Validates: Requirements 1.1, 1.2, 1.5**

### Property 2: Content Update Visibility

*For any* content type (home, program, notice, gallery, contact), when an admin updates the content, fetching the public-facing version should immediately reflect the updated content.

**Validates: Requirements 1.3, 3.2, 4.2**

### Property 3: Media Upload With Metadata

*For any* valid image file with metadata (title, description, category), uploading it should result in both the file and all metadata being stored in the media library and retrievable.

**Validates: Requirements 2.1, 2.2, 7.1**

### Property 4: Media Deletion Completeness

*For any* image in the gallery and media library, deleting it should remove it from both the gallery listing and the media library, making it no longer retrievable.

**Validates: Requirements 2.3**

### Property 5: Image Categorization

*For any* set of images with assigned categories or albums, filtering by a specific category should return only images belonging to that category.

**Validates: Requirements 2.5**

### Property 6: File Type Validation

*For any* file with an invalid image format (not jpg, png, gif, webp), attempting to upload it should be rejected with an appropriate error.

**Validates: Requirements 2.6, 7.2, 7.3**

### Property 7: File Size Validation

*For any* file larger than 10MB, attempting to upload it should be rejected with an appropriate error.

**Validates: Requirements 7.4**

### Property 8: Contact Information Round-Trip

*For any* contact information (address, phone, email, hours), storing it in the database and then retrieving it should return information equivalent to what was stored.

**Validates: Requirements 3.1**

### Property 9: Contact Form Submission Persistence

*For any* contact form submission, submitting it should result in the submission being stored in the database and appearing in the admin dashboard with all fields intact.

**Validates: Requirements 3.3, 3.4**

### Property 10: Contact Submission Status Updates

*For any* contact submission, marking it with a status (read, resolved) should persist that status, and subsequent retrieval should reflect the updated status.

**Validates: Requirements 3.5**

### Property 11: Program CRUD Round-Trip

*For any* program with all fields (name, description, duration, requirements, fees, status), creating it, updating any fields, and retrieving it should return the updated values.

**Validates: Requirements 4.1, 4.2, 4.4**

### Property 12: Program Deletion

*For any* program, deleting it should make it no longer retrievable from the programs listing.

**Validates: Requirements 4.3**

### Property 13: Program File Attachments

*For any* program with attached images or documents, the associations should be maintained, and retrieving the program should include references to all attached files.

**Validates: Requirements 4.5**

### Property 14: Inactive Program Filtering

*For any* program with status set to inactive or archived, it should not appear in public program listings, but should still be retrievable by admins.

**Validates: Requirements 4.6**

### Property 15: Notice CRUD Round-Trip

*For any* notice with all fields (title, content, category, priority, dates), creating it and retrieving it should return all fields with values equivalent to what was stored.

**Validates: Requirements 5.1, 5.2**

### Property 16: Notice Expiration Filtering

*For any* notice with an expiration date in the past, it should not appear in public notice listings.

**Validates: Requirements 5.3**

### Property 17: Notice Chronological Ordering

*For any* set of published notices, retrieving them should return them ordered by publish date in reverse chronological order (newest first).

**Validates: Requirements 5.4**

### Property 18: Urgent Notice Prominence

*For any* set of notices including urgent ones, retrieving them should return urgent notices before non-urgent notices of the same date.

**Validates: Requirements 5.5**

### Property 19: Notice Filtering

*For any* set of notices with various categories and statuses, filtering by a specific category or status should return only notices matching that filter.

**Validates: Requirements 5.6**

### Property 20: Authentication Required

*For any* admin dashboard endpoint, attempting to access it without valid authentication credentials should be denied with an appropriate error.

**Validates: Requirements 6.1**

### Property 21: Role-Based Dashboard Routing

*For any* authenticated user, logging in should redirect them to a dashboard appropriate for their role (admin, teacher, or student).

**Validates: Requirements 6.2**

### Property 22: Admin-Only Content Management

*For any* content management operation (create, update, delete), attempting it as a non-admin user should be denied with an appropriate error.

**Validates: Requirements 6.3**

### Property 23: Teacher Read-Only Access

*For any* content, teachers should be able to read it, but attempting to modify it should be denied with an appropriate error.

**Validates: Requirements 6.4**

### Property 24: Student Dashboard Restrictions

*For any* admin or teacher content, attempting to access it as a student should be denied with an appropriate error.

**Validates: Requirements 6.5**

### Property 25: Unauthorized Access Logging

*For any* unauthorized access attempt to admin features, the system should both deny the access and create a log entry recording the attempt.

**Validates: Requirements 6.6**

### Property 26: Media File Search and Filter

*For any* set of media files with various types and upload dates, searching or filtering should return only files matching the search criteria.

**Validates: Requirements 7.5**

### Property 27: Media Deletion Reference Check

*For any* media file that is referenced in content (gallery, programs, etc.), attempting to delete it should trigger a warning indicating where it's being used.

**Validates: Requirements 7.6**

### Property 28: Content Version Creation

*For any* content modification, the system should create a version record containing the previous content state, the user who made the change, and the timestamp.

**Validates: Requirements 8.1, 8.2**

### Property 29: Version History Retrieval

*For any* content item with multiple edits, retrieving the version history should return all versions in chronological order with complete metadata.

**Validates: Requirements 8.3**

### Property 30: Version Comparison

*For any* two versions of the same content, comparing them should return the differences between the versions.

**Validates: Requirements 8.4**

### Property 31: Version Rollback

*For any* content with previous versions, rolling back to a specific version should restore the content to match that version exactly.

**Validates: Requirements 8.5**

### Property 32: XSS Sanitization

*For any* content containing potential XSS payloads (script tags, event handlers, etc.), the system should sanitize the content, removing or escaping dangerous elements.

**Validates: Requirements 9.5**

### Property 33: Content State Management

*For any* content with a state (draft, scheduled, published, archived), storing it and retrieving it should preserve the state value.

**Validates: Requirements 10.1**

### Property 34: Draft Content Filtering

*For any* content in draft state, it should not appear in public listings, but should be retrievable by admins in the dashboard.

**Validates: Requirements 10.2**

### Property 35: Archived Content Retention

*For any* content that is archived, it should not appear in public listings, but should still exist in the database and be retrievable by admins.

**Validates: Requirements 10.5**

### Property 36: Content Search

*For any* set of content across all types, searching with a query string should return only content items whose title or body contains the search term.

**Validates: Requirements 11.1**

### Property 37: Multi-Criteria Filtering

*For any* set of content, filtering by multiple criteria (type, status, date range, author) should return only content matching all specified criteria.

**Validates: Requirements 11.2**

### Property 38: Search Result Metadata

*For any* search results, each result should include relevant metadata (title, type, status, date, author) in the response.

**Validates: Requirements 11.4**

### Property 39: Result Sorting

*For any* set of search results, sorting by a specified field (date, title, relevance) should return results ordered correctly by that field.

**Validates: Requirements 11.5**

### Property 40: Bulk Operations

*For any* set of content items, performing a bulk operation (delete, archive, status change) should apply the operation to all selected items.

**Validates: Requirements 13.2**

### Property 41: Bulk Operation Error Reporting

*For any* bulk operation where some items fail, the response should clearly indicate which items succeeded and which failed with specific error messages.

**Validates: Requirements 13.5**

### Property 42: Content Export Completeness

*For any* content export, the generated file should contain all content items with all their fields and metadata intact.

**Validates: Requirements 14.1, 14.2**

### Property 43: Export Format Support

*For any* content export in JSON or CSV format, the output should be valid according to the format specification and parseable by standard tools.

**Validates: Requirements 14.3**

### Property 44: Export Media References

*For any* content export that includes items with media attachments, the export should include the file paths or URLs for all referenced media.

**Validates: Requirements 14.4**

### Property 45: Backup Metadata Tracking

*For any* completed export/backup, the system should record the backup date and file size, making this information retrievable.

**Validates: Requirements 14.5**

### Property 46: Cache Storage and Retrieval

*For any* page content, after it's cached, subsequent requests for the same content should be served from the cache until the cache expires or is invalidated.

**Validates: Requirements 15.1, 15.3**

### Property 47: Cache Invalidation on Update

*For any* cached content, updating the underlying content should immediately invalidate the cache, causing the next request to fetch fresh content.

**Validates: Requirements 15.2**

### Property 48: Image Optimization

*For any* uploaded image, the system should create an optimized version with reduced file size while maintaining acceptable quality.

**Validates: Requirements 15.5**

### Property 49: Responsive Image Generation

*For any* uploaded image, the system should generate multiple sizes (thumbnail, medium, large) for responsive delivery.

**Validates: Requirements 15.6**

## Error Handling

### Error Categories

The CMS will handle errors in the following categories:

1. **Validation Errors**: Invalid input data (malformed email, missing required fields, invalid file types)
2. **Authentication Errors**: Invalid credentials, expired sessions, missing tokens
3. **Authorization Errors**: Insufficient permissions, role restrictions
4. **Resource Errors**: Content not found, file not found, media already deleted
5. **System Errors**: Database connection failures, file system errors, cache failures
6. **Business Logic Errors**: Cannot delete referenced media, cannot publish without required fields

### Error Response Format

All API endpoints will return errors in a consistent format:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "field": "fieldName",
    "details": {}
  }
}
```

### Error Codes

- `AUTH_REQUIRED`: Authentication required
- `AUTH_INVALID`: Invalid credentials
- `AUTH_EXPIRED`: Session expired
- `PERMISSION_DENIED`: Insufficient permissions
- `VALIDATION_FAILED`: Input validation failed
- `NOT_FOUND`: Resource not found
- `ALREADY_EXISTS`: Resource already exists (duplicate)
- `FILE_TOO_LARGE`: File exceeds size limit
- `INVALID_FILE_TYPE`: Unsupported file type
- `MEDIA_IN_USE`: Cannot delete media that is referenced
- `REQUIRED_FIELD`: Required field missing
- `DATABASE_ERROR`: Database operation failed
- `FILE_SYSTEM_ERROR`: File operation failed
- `CACHE_ERROR`: Cache operation failed

### Error Handling Strategies

**Validation Errors**:
- Validate all input on both client and server side
- Return specific field-level errors
- Provide helpful error messages with correction guidance

**Authentication/Authorization Errors**:
- Log all authentication failures
- Implement rate limiting to prevent brute force attacks
- Clear error messages without revealing security details

**Resource Errors**:
- Check resource existence before operations
- Provide clear "not found" messages
- Suggest alternatives when appropriate

**System Errors**:
- Log all system errors with full stack traces
- Return generic error messages to users (don't expose internals)
- Implement retry logic for transient failures
- Graceful degradation (e.g., serve stale cache if database is down)

**Business Logic Errors**:
- Check business rules before operations
- Provide clear explanations of why operation failed
- Suggest corrective actions

### Logging Strategy

All errors will be logged with the following information:
- Timestamp
- Error type and code
- User ID (if authenticated)
- Request details (endpoint, method, parameters)
- Stack trace (for system errors)
- IP address and user agent

Logs will be stored in:
- `logs/error.log` - All errors
- `logs/security.log` - Authentication and authorization failures
- `logs/access.log` - All API access attempts

## Testing Strategy

### Dual Testing Approach

The CMS will be validated using both unit tests and property-based tests:

**Unit Tests**: Verify specific examples, edge cases, and error conditions
- Specific content creation scenarios
- Edge cases (empty strings, null values, boundary conditions)
- Error handling paths
- Integration between components

**Property-Based Tests**: Verify universal properties across all inputs
- CRUD operations with randomly generated data
- Round-trip properties (store/retrieve, serialize/deserialize)
- Filtering and sorting with various data sets
- Permission checks across all roles and operations

Both approaches are complementary and necessary for comprehensive coverage. Unit tests catch concrete bugs and verify specific behaviors, while property-based tests verify general correctness across a wide range of inputs.

### Property-Based Testing Configuration

**Library**: PHPUnit with `eris/eris` for property-based testing

**Configuration**:
- Minimum 100 iterations per property test
- Each property test must reference its design document property
- Tag format: `@group Feature: dynamic-content-management, Property {number}: {property_text}`

**Example Property Test Structure**:

```php
/**
 * @group Feature: dynamic-content-management, Property 1: Home Content Round-Trip
 */
public function testHomeContentRoundTrip()
{
    $this->forAll(
        Generator\associative([
            'section_key' => Generator\elements(['hero', 'about', 'features']),
            'title' => Generator\string(),
            'content' => Generator\string(),
            'image_url' => Generator\string()
        ])
    )->then(function ($content) {
        $id = $this->contentManager->createContent('home', $content);
        $retrieved = $this->contentManager->getContent('home', $id);
        
        $this->assertEquals($content['section_key'], $retrieved['section_key']);
        $this->assertEquals($content['title'], $retrieved['title']);
        $this->assertEquals($content['content'], $retrieved['content']);
        $this->assertEquals($content['image_url'], $retrieved['image_url']);
    });
}
```

### Unit Testing Strategy

**Test Organization**:
- `tests/Unit/ContentManagerTest.php` - Content CRUD operations
- `tests/Unit/MediaManagerTest.php` - Media upload and management
- `tests/Unit/CacheManagerTest.php` - Caching operations
- `tests/Unit/VersionControlTest.php` - Versioning and rollback
- `tests/Unit/PermissionManagerTest.php` - Access control
- `tests/Integration/ApiEndpointsTest.php` - API endpoint integration
- `tests/Integration/AuthenticationTest.php` - Authentication flow

**Coverage Goals**:
- Minimum 80% code coverage
- 100% coverage of critical paths (authentication, authorization, data persistence)
- All error handling paths tested

### Test Data Management

**Fixtures**:
- Use database transactions for test isolation
- Create reusable fixtures for common test scenarios
- Reset database state between tests

**Test Database**:
- Separate test database: `scti_school_test`
- Automated schema setup in test bootstrap
- Seed with minimal required data

### Integration Testing

**API Testing**:
- Test all endpoints with valid and invalid inputs
- Verify response formats and status codes
- Test authentication and authorization on all protected endpoints
- Test CSRF protection

**End-to-End Testing**:
- Test complete workflows (create program → upload image → publish)
- Test role-based access across multiple user types
- Test content publishing workflow from draft to published

### Performance Testing

While not part of unit testing, performance should be validated:
- Load testing with multiple concurrent users
- Cache effectiveness measurement
- Database query optimization verification
- File upload performance with various file sizes

### Security Testing

**Automated Security Tests**:
- XSS payload injection tests
- SQL injection tests
- CSRF token validation tests
- File upload security tests (malicious files, path traversal)
- Session hijacking prevention tests

**Manual Security Review**:
- Code review for security vulnerabilities
- Penetration testing of admin dashboard
- Review of file permissions and access controls

### Continuous Integration

Tests should be run automatically:
- On every commit to development branch
- Before merging pull requests
- Nightly full test suite runs
- Performance tests weekly

### Test Documentation

Each test should include:
- Clear description of what is being tested
- Reference to requirement or property being validated
- Expected behavior
- Any special setup or teardown requirements

