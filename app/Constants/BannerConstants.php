<?php

namespace App\Constants;

class BannerConstants
{
    /**
     * Available banner positions for home page sections
     */
    public static function getHomePagePositions(): array
    {
        return [
          //   'homepage_banner' => __('Homepage Banner (Main Slider)'),
            'before_hero' => __('Before Hero Section'),
            'after_hero' => __('After Hero Section'),
            'before_about' => __('Before About Section'),
            'after_about' => __('After About Section'),
            'before_features' => __('Before Features Section'),
            'after_features' => __('After Features Section'),
           
            'before_registration' => __('Before Registration Section'),
            'after_registration' => __('After Registration Section'),
            'before_affiliate' => __('Before Affiliate Section'),
            'after_affiliate' => __('After Affiliate Section'),
            'before_subscription_plans' => __('Before Subscription Plans Section'),
            'after_subscription_plans' => __('After Subscription Plans Section'),
            'before_contact' => __('Before Contact Section'),
            'after_contact' => __('After Contact Section'),
        ];
    }

    /**
     * Available target pages grouped by category
     */
    public static function getTargetPages(): array
    {
        return [
            'general' => [
                'label' => __('General Pages'),
                'pages' => [
                    'home' => __('Homepage'),
                    'about' => __('About Us'),
                    'terms' => __('Terms of Use'),
                    'privacy' => __('Privacy Policy'),
                    // 'return-policy' => __('Return Policy'),
                    // 'shipping-policy' => __('Shipping Policy'),
                ],
            ],
            'products' => [
                'label' => __('Products'),
                'pages' => [
                    'products' => __('Products List'),
                    'products.show' => __('Product Detail'),
                    // 'products.category' => __('Product Category'),
                    // 'products.on-sale' => __('Products On Sale'),
                    // 'products.in-stock' => __('Products In Stock'),
                ],
            ],
            'services' => [
                'label' => __('Services'),
                'pages' => [
                    'clinics' => __('Clinics'),
                    'clinics.show' => __('Clinic Detail'),
                    'doctors' => __('Doctors'),
                    'rental-spaces' => __('Rental Spaces'),
                    'suppliers' => __('Suppliers'),
                ],
            ],
            'content' => [
                'label' => __('Content'),
                'pages' => [
                    'blogs' => __('Blogs'),
                    'blogs.show' => __('Blog Detail'),
                    'courses' => __('Courses'),
                    'courses.show' => __('Course Detail'),
                    'jobs' => __('Jobs'),
                    'jobs.show' => __('Job Detail'),
                ],
            ],
        ];
    }

    /**
     * Get all target pages as a flat array (value => label)
     */
    public static function getTargetPagesFlat(): array
    {
        $pages = [];
        foreach (self::getTargetPages() as $category) {
            $pages = array_merge($pages, $category['pages']);
        }

        return $pages;
    }

    /**
     * Get target pages grouped by category (for display in forms)
     */
    public static function getTargetPagesGrouped(): array
    {
        return self::getTargetPages();
    }

    /**
     * Get available sections/positions for each target page
     */
    public static function getPageSections(): array
    {
        return [
            // Homepage sections
            'home' => [
                // 'homepage_banner' => __('Homepage Banner (Main Slider)'),
                'before_hero' => __('Before Hero Section'),
                'after_hero' => __('After Hero Section'),
                'before_about' => __('Before About Section'),
                'after_about' => __('After About Section'),
                'before_features' => __('Before Features Section'),
                'after_features' => __('After Features Section'),
                'before_registration' => __('Before Registration Section'),
                'after_registration' => __('After Registration Section'),
                'before_affiliate' => __('Before Affiliate Section'),
                'after_affiliate' => __('After Affiliate Section'),
                'before_subscription_plans' => __('Before Subscription Plans Section'),
                'after_subscription_plans' => __('After Subscription Plans Section'),
                'before_contact' => __('Before Contact Section'),
                'after_contact' => __('After Contact Section'),
            ],
            // Products pages sections
            'products' => [
                'products_header' => __('Products Header'),
                'products_before_list' => __('Before Products List'),
                'products_after_list' => __('After Products List'),
                'products_sidebar' => __('Products Sidebar'),
                'products_footer' => __('Products Footer'),
            ],
            'products.show' => [
                'product_header' => __('Product Header'),
                'product_before_details' => __('Before Product Details'),
                'product_after_details' => __('After Product Details'),
                'product_sidebar' => __('Product Sidebar'),
                'product_footer' => __('Product Footer'),
            ],
            // Clinics pages sections
            'clinics' => [
                'clinics_header' => __('Clinics Header'),
                'clinics_before_list' => __('Before Clinics List'),
                'clinics_after_list' => __('After Clinics List'),
                'clinics_sidebar' => __('Clinics Sidebar'),
            ],
            'clinics.show' => [
                'clinic_header' => __('Clinic Header'),
                'clinic_before_details' => __('Before Clinic Details'),
                'clinic_after_details' => __('After Clinic Details'),
                'clinic_sidebar' => __('Clinic Sidebar'),
            ],
            // Doctors pages sections
            'doctors' => [
                'doctors_header' => __('Doctors Header'),
                'doctors_before_list' => __('Before Doctors List'),
                'doctors_after_list' => __('After Doctors List'),
                'doctors_sidebar' => __('Doctors Sidebar'),
            ],
            // Rental spaces sections
            'rental-spaces' => [
                'rental_spaces_header' => __('Rental Spaces Header'),
                'rental_spaces_before_list' => __('Before Rental Spaces List'),
                'rental_spaces_after_list' => __('After Rental Spaces List'),
                'rental_spaces_sidebar' => __('Rental Spaces Sidebar'),
            ],
            // Suppliers sections
            'suppliers' => [
                'suppliers_header' => __('Suppliers Header'),
                'suppliers_before_list' => __('Before Suppliers List'),
                'suppliers_after_list' => __('After Suppliers List'),
                'suppliers_sidebar' => __('Suppliers Sidebar'),
            ],
            // Blogs sections
            'blogs' => [
                'blogs_header' => __('Blogs Header'),
                'blogs_before_list' => __('Before Blogs List'),
                'blogs_after_list' => __('After Blogs List'),
                'blogs_sidebar' => __('Blogs Sidebar'),
            ],
            'blogs.show' => [
                'blog_header' => __('Blog Header'),
                'blog_before_content' => __('Before Blog Content'),
                'blog_after_content' => __('After Blog Content'),
                'blog_sidebar' => __('Blog Sidebar'),
            ],
            // Courses sections
            'courses' => [
                'courses_header' => __('Courses Header'),
                'courses_before_list' => __('Before Courses List'),
                'courses_after_list' => __('After Courses List'),
                'courses_sidebar' => __('Courses Sidebar'),
            ],
            'courses.show' => [
                'course_header' => __('Course Header'),
                'course_before_content' => __('Before Course Content'),
                'course_after_content' => __('After Course Content'),
                'course_sidebar' => __('Course Sidebar'),
            ],
            // Jobs sections
            'jobs' => [
                'jobs_header' => __('Jobs Header'),
                'jobs_before_list' => __('Before Jobs List'),
                'jobs_after_list' => __('After Jobs List'),
                'jobs_sidebar' => __('Jobs Sidebar'),
            ],
            'jobs.show' => [
                'job_header' => __('Job Header'),
                'job_before_details' => __('Before Job Details'),
                'job_after_details' => __('After Job Details'),
                'job_sidebar' => __('Job Sidebar'),
            ],
            // General pages sections
            'about' => [
                'about_header' => __('About Header'),
                'about_before_content' => __('Before About Content'),
                'about_after_content' => __('After About Content'),
            ],
            'terms' => [
                'terms_header' => __('Terms Header'),
                'terms_before_content' => __('Before Terms Content'),
                'terms_after_content' => __('After Terms Content'),
            ],
            'privacy' => [
                'privacy_header' => __('Privacy Header'),
                'privacy_before_content' => __('Before Privacy Content'),
                'privacy_after_content' => __('After Privacy Content'),
            ],
        ];
    }

    /**
     * Get sections for specific pages
     */
    public static function getSectionsForPages(array $pageIdentifiers): array
    {
        $allSections = self::getPageSections();
        $sections = [];

        foreach ($pageIdentifiers as $page) {
            if (isset($allSections[$page])) {
                $sections = array_merge($sections, $allSections[$page]);
            }
        }

        // If no specific sections found, return all homepage sections as default
        if (empty($sections)) {
            $sections = $allSections['home'] ?? [];
        }

        return $sections;
    }

    /**
     * Get all available sections across all pages (for custom positions)
     */
    public static function getAllSections(): array
    {
        $allSections = [];
        foreach (self::getPageSections() as $pageSections) {
            $allSections = array_merge($allSections, $pageSections);
        }

        return array_unique($allSections, SORT_REGULAR);
    }
}