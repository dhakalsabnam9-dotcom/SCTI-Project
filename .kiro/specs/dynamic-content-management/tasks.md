# Implementation Plan: Dynamic Content Management System

## Overview

This implementation plan transforms the SCTI school website into a fully dynamic CMS with an admin dashboard. The approach follows a bottom-up strategy: database schema → core PHP classes → API endpoints → admin UI → integration and testing. Each task builds incrementally to ensure working functionality at every step.

## Tasks

- [ ] 1. Set up database schema and core infrastructure
  - [ ] 1.1 Create database migration file for all 9 tables
    - Create `migrations/create_cms_tables.sql` with all table definitions
    - Include tables: home_content, programs, notices, gallery_images, contact_info, media_library, content_versions, content_cache, access_log
    - Add all indexes and foreign key constraints
    - _Requirements: 1.1, 1.2, 2.1, 2.2, 3.1, 4.1, 5.1, 7.1, 8.1_
  
  - [ ]* 1.2 Write property test for database schema integrity
    - **Property: Schema Validation**
    - **Validates: Requirements 1.1, 2.1, 3.1, 4.1, 5.1, 7.1**
    - Verify all tables exist with correct columns and constraints
  
  - [ ] 1.3 Create database connection utility class
    - Create `includes/Database.php` with PDO connection handling
    - Implement connection pooling and error handling
    - Add transaction support methods
    - _Requirements: All database operations_

- [ ] 2. Implement core PHP manager classes
  - [ ] 2.1 Implement ContentManager class
    - Create `includes/ContentManager.php`
    - Implement all methods: getContent, createContent, updateContent, deleteContent, publishContent, scheduleContent
    - Add input validation and sanitization
    - _Requirements: 1.1, 1.2, 1.3, 4.1, 4.2, 4.3, 5.1, 10.1_

  - [ ]* 2.2 Write property tests for ContentManager
    - **Property 1: Home Content Round-Trip**
    - **Validates: Requirements 1.1, 1.2, 1.5**
    - **Property 2: Content Update Visibility**
    - **Validates: Requirements 1.3, 3.2, 4.2**
    - **Property 11: Program CRUD Round-Trip**
    - **Validates: Requirements 4.1, 4.2, 4.4**
    - **Property 15: Notice CRUD Round-Trip**
    - **Validates: Requirements 5.1, 5.2**
  
  - [ ] 2.3 Implement MediaManager class
    - Create `includes/MediaManager.php`
    - Implement uploadFile, validateFile, generateThumbnail, deleteFile, getMediaList methods
    - Add file type and size validation
    - Implement image optimization and thumbnail generation
    - _Requirements: 2.1, 2.2, 2.3, 7.1, 7.2, 7.3, 7.4, 15.5, 15.6_
  
  - [ ]* 2.4 Write property tests for MediaManager
    - **Property 3: Media Upload With Metadata**
    - **Validates: Requirements 2.1, 2.2, 7.1**
    - **Property 6: File Type Validation**
    - **Validates: Requirements 2.6, 7.2, 7.3**
    - **Property 7: File Size Validation**
    - **Validates: Requirements 7.4**
    - **Property 48: Image Optimization**
    - **Validates: Requirements 15.5**
  
  - [ ] 2.5 Implement CacheManager class
    - Create `includes/CacheManager.php`
    - Implement get, set, invalidate, invalidatePattern, clear methods
    - Add file-based caching with TTL support
    - Implement cache statistics tracking
    - _Requirements: 15.1, 15.2, 15.3_
  
  - [ ]* 2.6 Write property tests for CacheManager
    - **Property 46: Cache Storage and Retrieval**
    - **Validates: Requirements 15.1, 15.3**
    - **Property 47: Cache Invalidation on Update**
    - **Validates: Requirements 15.2**
  
  - [ ] 2.7 Implement VersionControl class
    - Create `includes/VersionControl.php`
    - Implement saveVersion, getVersions, compareVersions, restoreVersion methods
    - Add JSON serialization for content snapshots
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_
  
  - [ ]* 2.8 Write property tests for VersionControl
    - **Property 28: Content Version Creation**
    - **Validates: Requirements 8.1, 8.2**
    - **Property 31: Version Rollback**
    - **Validates: Requirements 8.5**
  
  - [ ] 2.9 Implement PermissionManager class
    - Create `includes/PermissionManager.php`
    - Implement checkPermission, getUserRole, canManageContent, logAccessAttempt methods
    - Define role permissions (admin, teacher, student)
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_
  
  - [ ]* 2.10 Write property tests for PermissionManager
    - **Property 20: Authentication Required**
    - **Validates: Requirements 6.1**
    - **Property 22: Admin-Only Content Management**
    - **Validates: Requirements 6.3**
    - **Property 23: Teacher Read-Only Access**
    - **Validates: Requirements 6.4**

- [ ] 3. Checkpoint - Verify core classes
  - Ensure all core PHP classes are implemented and tested
  - Run property tests to validate core functionality
  - Ask the user if questions arise

- [ ] 4. Implement Content API endpoints
  - [ ] 4.1 Create home page content API
    - Create `api/content/home.php`
    - Implement GET (retrieve) and PUT (update) endpoints
    - Add authentication and permission checks
    - Integrate with ContentManager and CacheManager
    - _Requirements: 1.1, 1.2, 1.3_
  
  - [ ] 4.2 Create programs API
    - Create `api/content/programs.php`
    - Implement GET (list/single), POST (create), PUT (update), DELETE endpoints
    - Add status filtering for public vs admin views
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.6_
  
  - [ ]* 4.3 Write property tests for programs API
    - **Property 12: Program Deletion**
    - **Validates: Requirements 4.3**
    - **Property 14: Inactive Program Filtering**
    - **Validates: Requirements 4.6**
  
  - [ ] 4.4 Create notices API
    - Create `api/content/notices.php`
    - Implement GET (list/single), POST (create), PUT (update), DELETE endpoints
    - Add category filtering and expiration date handling
    - Implement chronological ordering with urgent priority
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_
  
  - [ ]* 4.5 Write property tests for notices API
    - **Property 16: Notice Expiration Filtering**
    - **Validates: Requirements 5.3**
    - **Property 17: Notice Chronological Ordering**
    - **Validates: Requirements 5.4**
    - **Property 18: Urgent Notice Prominence**
    - **Validates: Requirements 5.5**
  
  - [ ] 4.6 Create gallery API
    - Create `api/content/gallery.php`
    - Implement GET (list/filter), POST (add), PUT (update metadata), DELETE endpoints
    - Add category/album filtering
    - _Requirements: 2.1, 2.3, 2.4, 2.5_
  
  - [ ]* 4.7 Write property tests for gallery API
    - **Property 4: Media Deletion Completeness**
    - **Validates: Requirements 2.3**
    - **Property 5: Image Categorization**
    - **Validates: Requirements 2.5**
  
  - [ ] 4.8 Create contact information API
    - Create `api/content/contact.php`
    - Implement GET (info/submissions) and PUT (update info) endpoints
    - Add contact form submission handling
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [ ]* 4.9 Write property tests for contact API
    - **Property 8: Contact Information Round-Trip**
    - **Validates: Requirements 3.1**
    - **Property 9: Contact Form Submission Persistence**
    - **Validates: Requirements 3.3, 3.4**

- [ ] 5. Implement Media API endpoints
  - [ ] 5.1 Create media upload API
    - Create `api/media/upload.php`
    - Implement file upload with validation
    - Integrate with MediaManager for processing
    - Return media metadata in response
    - _Requirements: 7.1, 7.2, 7.3, 7.4_
  
  - [ ] 5.2 Create media library listing API
    - Create `api/media/list.php`
    - Implement pagination and filtering
    - Add search functionality
    - _Requirements: 7.5_
  
  - [ ] 5.3 Create media deletion API
    - Create `api/media/delete.php`
    - Implement usage checking before deletion
    - Return warnings if media is referenced
    - _Requirements: 7.6_
  
  - [ ]* 5.4 Write property tests for media APIs
    - **Property 26: Media File Search and Filter**
    - **Validates: Requirements 7.5**
    - **Property 27: Media Deletion Reference Check**
    - **Validates: Requirements 7.6**

- [ ] 6. Implement authentication and authorization APIs
  - [ ] 6.1 Create permission check API
    - Create `api/auth/check-permission.php`
    - Implement role-based permission verification
    - _Requirements: 6.1, 6.3, 6.4, 6.5_
  
  - [ ] 6.2 Create session validation API
    - Create `api/auth/session-validate.php`
    - Implement session checking and renewal
    - _Requirements: 6.1, 6.2_
  
  - [ ]* 6.3 Write property tests for auth APIs
    - **Property 21: Role-Based Dashboard Routing**
    - **Validates: Requirements 6.2**
    - **Property 25: Unauthorized Access Logging**
    - **Validates: Requirements 6.6**

- [ ] 7. Implement cache management API
  - [ ] 7.1 Create cache invalidation API
    - Create `api/cache/invalidate.php`
    - Implement manual cache clearing for admins
    - Add pattern-based invalidation
    - _Requirements: 15.2_

- [ ] 8. Checkpoint - Verify all APIs
  - Test all API endpoints with sample requests
  - Verify authentication and authorization on protected endpoints
  - Ensure proper error handling and response formats
  - Ask the user if questions arise

- [ ] 9. Implement admin dashboard UI structure
  - [ ] 9.1 Create main admin dashboard page
    - Create `admin/dashboard/index.php`
    - Implement dashboard layout with navigation
    - Add statistics overview (content counts, recent activity)
    - Implement role-based menu display
    - _Requirements: 6.2, 12.1, 12.2_
  
  - [ ] 9.2 Create admin CSS styles
    - Create `admin/assets/css/admin.css`
    - Implement responsive design for mobile and desktop
    - Add touch-friendly controls for mobile
    - _Requirements: 12.1, 12.2, 12.4_
  
  - [ ] 9.3 Create admin JavaScript utilities
    - Create `admin/assets/js/admin.js`
    - Implement AdminDashboard class with statistics loading
    - Add event listeners for common actions
    - Implement bulk operation handling
    - _Requirements: 13.1, 13.2, 13.3_

- [ ] 10. Implement content editor interface
  - [ ] 10.1 Create content editor page
    - Create `admin/dashboard/content-editor.php`
    - Implement WYSIWYG editor integration (TinyMCE or CKEditor)
    - Add content preview functionality
    - Implement draft/publish workflow controls
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.6, 10.1, 10.2_
  
  - [ ] 10.2 Create content editor JavaScript
    - Create `admin/assets/js/content-editor.js`
    - Implement ContentEditor class
    - Add media insertion from library
    - Implement content sanitization
    - _Requirements: 9.3, 9.5_
  
  - [ ]* 10.3 Write property test for XSS sanitization
    - **Property 32: XSS Sanitization**
    - **Validates: Requirements 9.5**

- [ ] 11. Implement media library interface
  - [ ] 11.1 Create media library page
    - Create `admin/dashboard/media-library.php`
    - Implement grid view of media files
    - Add search and filter controls
    - Display file metadata and usage information
    - _Requirements: 7.5, 7.6_
  
  - [ ] 11.2 Create media uploader JavaScript
    - Create `admin/assets/js/media-uploader.js`
    - Implement MediaUploader class with drag-and-drop
    - Add upload progress display
    - Implement client-side file validation
    - _Requirements: 7.1, 7.2, 7.3, 7.4_
  
  - [ ]* 11.3 Write property test for responsive image generation
    - **Property 49: Responsive Image Generation**
    - **Validates: Requirements 15.6**

- [ ] 12. Implement programs manager interface
  - [ ] 12.1 Create programs manager page
    - Create `admin/dashboard/programs-manager.php`
    - Implement programs listing with edit/delete actions
    - Add program creation form
    - Implement status toggle controls
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.6_
  
  - [ ]* 12.2 Write property test for program file attachments
    - **Property 13: Program File Attachments**
    - **Validates: Requirements 4.5**

- [ ] 13. Implement notices manager interface
  - [ ] 13.1 Create notices manager page
    - Create `admin/dashboard/notices-manager.php`
    - Implement notices listing with filters
    - Add notice creation/editing form
    - Implement scheduling controls
    - _Requirements: 5.1, 5.2, 5.3, 5.6_
  
  - [ ]* 13.2 Write property test for notice filtering
    - **Property 19: Notice Filtering**
    - **Validates: Requirements 5.6**

- [ ] 14. Implement gallery manager interface
  - [ ] 14.1 Create gallery manager page
    - Create `admin/dashboard/gallery-manager.php`
    - Implement gallery image listing
    - Add category/album management
    - Implement image reordering
    - _Requirements: 2.4, 2.5_

- [ ] 15. Implement contact manager interface
  - [ ] 15.1 Create contact manager page
    - Create `admin/dashboard/contact-manager.php`
    - Implement contact info editing form
    - Display contact form submissions
    - Add submission status management
    - _Requirements: 3.1, 3.2, 3.3, 3.5_
  
  - [ ]* 15.2 Write property test for contact submission status
    - **Property 10: Contact Submission Status Updates**
    - **Validates: Requirements 3.5**

- [ ] 16. Implement settings and advanced features
  - [ ] 16.1 Create settings page
    - Create `admin/dashboard/settings.php`
    - Implement content versioning interface
    - Add version history display and comparison
    - Implement rollback functionality
    - _Requirements: 8.3, 8.4, 8.5_
  
  - [ ]* 16.2 Write property tests for version control
    - **Property 29: Version History Retrieval**
    - **Validates: Requirements 8.3**
    - **Property 30: Version Comparison**
    - **Validates: Requirements 8.4**
  
  - [ ] 16.3 Implement search and filter functionality
    - Add global search across all content types
    - Implement multi-criteria filtering
    - Add sorting options
    - _Requirements: 11.1, 11.2, 11.4, 11.5_
  
  - [ ]* 16.4 Write property tests for search functionality
    - **Property 36: Content Search**
    - **Validates: Requirements 11.1**
    - **Property 37: Multi-Criteria Filtering**
    - **Validates: Requirements 11.2**
    - **Property 39: Result Sorting**
    - **Validates: Requirements 11.5**
  
  - [ ] 16.5 Implement bulk operations
    - Add bulk selection UI
    - Implement bulk delete, archive, status change
    - Add confirmation dialogs
    - Display operation progress
    - _Requirements: 13.1, 13.2, 13.3, 13.4_
  
  - [ ]* 16.6 Write property tests for bulk operations
    - **Property 40: Bulk Operations**
    - **Validates: Requirements 13.2**
    - **Property 41: Bulk Operation Error Reporting**
    - **Validates: Requirements 13.5**
  
  - [ ] 16.7 Implement content export functionality
    - Add export interface to settings page
    - Implement JSON and CSV export formats
    - Include media references in exports
    - Track backup metadata
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_
  
  - [ ]* 16.8 Write property tests for export functionality
    - **Property 42: Content Export Completeness**
    - **Validates: Requirements 14.1, 14.2**
    - **Property 43: Export Format Support**
    - **Validates: Requirements 14.3**
    - **Property 44: Export Media References**
    - **Validates: Requirements 14.4**

- [ ] 17. Checkpoint - Verify admin dashboard
  - Test all admin dashboard pages
  - Verify responsive design on mobile and desktop
  - Test WYSIWYG editor functionality
  - Verify all CRUD operations work correctly
  - Ask the user if questions arise

- [ ] 18. Implement content state management
  - [ ] 18.1 Add content scheduling functionality
    - Implement scheduled publishing in ContentManager
    - Create cron job or scheduled task handler
    - Add UI controls for scheduling
    - _Requirements: 10.3_
  
  - [ ]* 18.2 Write property tests for content states
    - **Property 33: Content State Management**
    - **Validates: Requirements 10.1**
    - **Property 34: Draft Content Filtering**
    - **Validates: Requirements 10.2**
    - **Property 35: Archived Content Retention**
    - **Validates: Requirements 10.5**

- [ ] 19. Update public website to use dynamic content
  - [ ] 19.1 Update home page to fetch dynamic content
    - Modify `index.html` or create `index.php`
    - Fetch home page content from API
    - Implement client-side rendering or server-side rendering
    - Add cache headers for performance
    - _Requirements: 1.3, 15.4_
  
  - [ ] 19.2 Update programs page
    - Modify programs page to fetch from API
    - Filter out inactive programs
    - Implement program detail views
    - _Requirements: 4.2, 4.6_
  
  - [ ] 19.3 Update notices page
    - Modify notices page to fetch from API
    - Implement expiration filtering
    - Display urgent notices prominently
    - _Requirements: 5.3, 5.4, 5.5_
  
  - [ ] 19.4 Update gallery page
    - Modify gallery page to fetch from API
    - Implement category filtering
    - Add responsive image loading
    - _Requirements: 2.4, 2.5, 15.6_
  
  - [ ] 19.5 Update contact page
    - Modify contact page to fetch dynamic contact info
    - Ensure contact form submits to API
    - _Requirements: 3.2, 3.4_

- [ ] 20. Implement security measures
  - [ ] 20.1 Add CSRF protection
    - Implement CSRF token generation and validation
    - Add tokens to all forms
    - Validate tokens on all POST/PUT/DELETE requests
    - _Requirements: 9.5_
  
  - [ ] 20.2 Implement input validation and sanitization
    - Add validation to all API endpoints
    - Sanitize all user input
    - Implement XSS prevention
    - _Requirements: 9.5_
  
  - [ ]* 20.3 Write security property tests
    - Test XSS payload rejection
    - Test SQL injection prevention
    - Test CSRF token validation
    - Test file upload security

- [ ] 21. Implement performance optimizations
  - [ ] 21.1 Add database query optimization
    - Review and optimize all database queries
    - Add appropriate indexes
    - Implement query result caching
    - _Requirements: 15.4_
  
  - [ ] 21.2 Implement image optimization pipeline
    - Add automatic image compression on upload
    - Generate responsive image sizes
    - Implement lazy loading for images
    - _Requirements: 15.5, 15.6_
  
  - [ ] 21.3 Configure cache headers and strategies
    - Set appropriate cache headers for static assets
    - Implement cache warming for frequently accessed content
    - Configure CDN-friendly caching
    - _Requirements: 15.1, 15.3, 15.4_

- [ ] 22. Final integration and testing
  - [ ] 22.1 Integration testing
    - Test complete workflows (create → edit → publish → view)
    - Test role-based access across all features
    - Test content versioning and rollback
    - Verify cache invalidation on updates
    - _Requirements: All_
  
  - [ ]* 22.2 Run all property-based tests
    - Execute full property test suite
    - Verify all 49 properties pass
    - Fix any failing tests
  
  - [ ] 22.3 Cross-browser testing
    - Test on Chrome, Firefox, Safari, Edge
    - Verify responsive design on various screen sizes
    - Test touch interactions on mobile devices
    - _Requirements: 12.1, 12.2, 12.5_
  
  - [ ] 22.4 Performance testing
    - Verify home page loads within 2 seconds
    - Test search response time (< 2 seconds)
    - Measure cache effectiveness
    - _Requirements: 11.3, 15.4_
  
  - [ ] 22.5 Security audit
    - Review all authentication and authorization checks
    - Test for common vulnerabilities (XSS, SQL injection, CSRF)
    - Verify file upload security
    - Review access logging

- [ ] 23. Final checkpoint - Production readiness
  - Ensure all tests pass
  - Verify all requirements are met
  - Review documentation and code comments
  - Ask the user if ready to deploy or if any issues need addressing

## Notes

- Tasks marked with `*` are optional property-based tests and can be skipped for faster MVP delivery
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation and allow for user feedback
- Property tests validate universal correctness properties across all inputs
- The implementation follows a bottom-up approach: database → classes → APIs → UI → integration
- All code should include proper error handling and logging
- Security measures (authentication, authorization, input validation) must be implemented throughout
- Performance optimizations (caching, image optimization) are integrated into the workflow
