# Design Document: School Website Documentation

## Overview

The Sindhuli Community Technical Institute (SCTI) website is a multi-page static website built with HTML, CSS, and vanilla JavaScript. It serves as the primary digital presence for the institution, providing information about programs, campus facilities, events, and contact details. The website features a responsive design with consistent navigation, visual branding, and interactive components including an image gallery, accordion-based program listings, and a contact form with client-side validation.

## Architecture

The website follows a traditional multi-page architecture with shared components and styling patterns across all pages.

```mermaid
graph TD
    A[index.html - Homepage] --> B[Shared Header Component]
    C[About Us.html] --> B
    D[Gallery.html] --> B
    E[Programs.html] --> B
    F[Notice Board.html] --> B
    G[Contact Us.html] --> B
    
    B --> H[Navigation Menu]
    B --> I[Logo & Branding]
    
    A --> J[style.css - Global Styles]
    C --> J
    D --> J
    E --> J
    F --> J
    G --> J
    
    A --> K[Assets Directory]
    D --> K
    
    K --> L[Images: banner.png, logos, staff photos]
    K --> M[Gallery Images: img1-5.jpg, work.jpg]
    
    A --> N[Footer Component]
    C --> N
    D --> N
    E --> N
    F --> N
    G --> N
