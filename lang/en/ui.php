<?php

/**
 * UI translations - English
 * T\u0142umaczenia UI - Angielski
 *
 * Zawiera t\u0142umaczenia element\u00f3w interfejsu u\u017cytkownika
 * Contains translations for user interface elements
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Navigation / Nawigacja
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'dashboard' => 'Dashboard',
        'api_keys' => 'API Keys',
        'api_explorer' => 'API Explorer',
        'usage' => 'Usage & Stats',
        'documentation' => 'Documentation',
        'profile' => 'Profile',
        'logout' => 'Log Out',
        'settings' => 'Settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard / Pulpit
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'title' => 'Dashboard',
        'welcome' => 'Welcome back, :name!',
        'quick_stats' => 'Quick Stats',
        'api_keys_count' => 'API Keys',
        'today_requests' => 'Today\'s Requests',
        'month_cost' => 'Month Cost',
        'recent_activity' => 'Recent Activity',
        'quick_actions' => 'Quick Actions',
        'generate_key' => 'Generate API Key',
        'test_api' => 'Test API',
        'view_docs' => 'View Documentation',
        'no_activity' => 'No activity yet. Start by generating your first API key!',
        'onboarding_title' => 'Get Started with Aisello API',
        'onboarding_step_1' => 'Generate your first API key',
        'onboarding_step_2' => 'Test the API in the playground',
        'onboarding_step_3' => 'Integrate into your application',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Keys / Klucze API
    |--------------------------------------------------------------------------
    */

    'api_keys' => [
        'title' => 'API Keys',
        'subtitle' => 'Manage your API keys for accessing Aisello services',
        'generate' => 'Generate New Key',
        'list_empty' => 'No API keys yet',
        'list_empty_description' => 'Generate your first API key to start using Aisello API',
        'name' => 'Key Name',
        'key' => 'API Key',
        'status' => 'Status',
        'last_used' => 'Last Used',
        'expires_at' => 'Expires',
        'created_at' => 'Created',
        'actions' => 'Actions',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'expired' => 'Expired',
        'never_used' => 'Never used',
        'never_expires' => 'Never expires',
        'view_details' => 'View Details',
        'revoke' => 'Revoke',
        'copy' => 'Copy',
        'copied' => 'Copied!',

        // Generate form
        'generate_title' => 'Generate New API Key',
        'generate_description' => 'Create a new API key to access Aisello services',
        'name_label' => 'Key Name',
        'name_placeholder' => 'e.g., Production API, Development Key',
        'name_help' => 'Choose a descriptive name to identify this key',
        'expires_label' => 'Expiration Date (optional)',
        'expires_help' => 'Leave empty for no expiration',
        'generate_button' => 'Generate Key',
        'cancel' => 'Cancel',

        // Generated key modal
        'generated_title' => 'API Key Generated Successfully',
        'generated_warning' => 'Important: Save this key now!',
        'generated_description' => 'This is the only time you\'ll see the full key. Copy it and store it securely.',
        'copy_button' => 'Copy to Clipboard',
        'done_button' => 'Done',

        // Details
        'details_title' => 'API Key Details',
        'usage_stats' => 'Usage Statistics',
        'total_requests' => 'Total Requests',
        'total_tokens' => 'Total Tokens',
        'total_cost' => 'Total Cost',
        'recent_usage' => 'Recent Usage',
        'endpoint' => 'Endpoint',
        'tokens' => 'Tokens',
        'cost' => 'Cost',
        'time' => 'Time',
        'date' => 'Date',

        // Revoke confirmation
        'revoke_title' => 'Revoke API Key?',
        'revoke_description' => 'Are you sure you want to revoke this key? Any applications using it will stop working immediately.',
        'revoke_button' => 'Yes, Revoke',
        'revoke_success' => 'API key revoked successfully',
        'generate_success' => 'API key generated successfully',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Explorer / Eksplorator API
    |--------------------------------------------------------------------------
    */

    'api_explorer' => [
        'title' => 'API Explorer',
        'subtitle' => 'Discover and test all available Aisello APIs',
        'search_placeholder' => 'Search APIs...',
        'category_all' => 'All',
        'category_ai' => 'AI & ML',
        'category_web' => 'Web Scraping',
        'category_data' => 'Data Processing',
        'try_it' => 'Try it out',
        'view_docs' => 'Documentation',
        'no_results' => 'No APIs found',
        'no_results_description' => 'Try adjusting your search criteria',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Playground / Plac zabaw API
    |--------------------------------------------------------------------------
    */

    'playground' => [
        'title' => 'API Playground',
        'subtitle' => 'Test API requests in real-time',
        'back_to_explorer' => 'Back to Explorer',

        // Left panel
        'authentication' => 'Authentication',
        'parameters' => 'Parameters',
        'headers' => 'Headers',
        'body' => 'Request Body',
        'examples' => 'Examples',
        'select_key' => 'Select API Key',
        'select_key_placeholder' => 'Choose an API key...',
        'no_keys' => 'No API keys available',
        'no_keys_description' => 'Generate an API key first',
        'send_request' => 'Send Request',
        'sending' => 'Sending...',

        // Right panel
        'response' => 'Response',
        'logs' => 'Logs',
        'response_time' => 'Response Time',
        'status_code' => 'Status Code',
        'tokens_used' => 'Tokens Used',
        'cost_estimate' => 'Cost',
        'copy_response' => 'Copy Response',
        'no_response' => 'No response yet',
        'no_response_description' => 'Send a request to see the response here',

        // Errors
        'error_network' => 'Network error. Please check your connection.',
        'error_timeout' => 'Request timed out. Please try again.',
        'error_server' => 'Server error. Please contact support.',

        // Progressive loading messages
        'loading' => [
            'analyzing' => 'Analyzing product data...',
            'collecting' => 'Gathering market information...',
            'researching' => 'Researching SEO keywords...',
            'structuring' => 'Creating description structure...',
            'generating' => 'Generating AI content...',
            'optimizing' => 'Optimizing for SEO...',
            'polishing' => 'Finalizing description...',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage & Stats / U\u017cycie i statystyki
    |--------------------------------------------------------------------------
    */

    'usage' => [
        'title' => 'Usage & Statistics',
        'subtitle' => 'Monitor your API usage and costs',
        'period' => 'Period',
        'today' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'custom' => 'Custom Range',
        'export' => 'Export to CSV',

        // Stats cards
        'total_requests' => 'Total Requests',
        'total_tokens' => 'Total Tokens',
        'total_cost' => 'Total Cost',
        'avg_response_time' => 'Avg Response Time',

        // Charts
        'requests_chart' => 'Requests Over Time',
        'cost_chart' => 'Cost Over Time',
        'endpoints_chart' => 'Top Endpoints',

        // Table
        'logs_title' => 'API Usage Logs',
        'endpoint' => 'Endpoint',
        'api_key' => 'API Key',
        'tokens' => 'Tokens',
        'cost' => 'Cost',
        'response_time' => 'Response Time',
        'timestamp' => 'Timestamp',
        'no_logs' => 'No usage logs yet',
        'no_logs_description' => 'Start using the API to see logs here',

        // Filters
        'filter' => 'Filter',
        'filter_endpoint' => 'Filter by endpoint',
        'filter_key' => 'Filter by API key',
        'filter_status' => 'Filter by status',
        'clear_filters' => 'Clear filters',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Prompts / Prompty użytkownika
    |--------------------------------------------------------------------------
    */

    'prompts' => [
        'title' => 'Custom Prompts',
        'subtitle' => 'Create and manage your custom AI prompts',
        'create' => 'Create Prompt',
        'create_title' => 'Create New Prompt',
        'create_description' => 'Define a custom prompt template for AI generation',
        'edit_title' => 'Edit Prompt',
        'empty' => 'No custom prompts yet',
        'empty_description' => 'Create your first custom prompt to personalize AI responses',
        'create_first' => 'Create First Prompt',
        'default' => 'Default',
        'set_default' => 'Set as Default',
        'name_label' => 'Prompt Name',
        'name_placeholder' => 'e.g., SEO Product Description, Marketing Copy',
        'template_label' => 'Prompt Template',
        'template_help' => 'Write your prompt template. Use variables like {name}, {description} etc.',
        'template_placeholder' => 'Write a professional product description for {name}...',
        'available_variables' => 'Available variables',
        'set_as_default' => 'Set as default prompt',
        'default_help' => 'This prompt will be automatically selected in the API playground',
        'create_button' => 'Create Prompt',
        'update_button' => 'Update Prompt',
        'confirm_delete' => 'Are you sure you want to delete this prompt?',
        'create_success' => 'Prompt created successfully',
        'update_success' => 'Prompt updated successfully',
        'delete_success' => 'Prompt deleted successfully',
        'set_default_success' => 'Prompt set as default',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common / Wspólne
    |--------------------------------------------------------------------------
    */

    'common' => [
        'created' => 'Created',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'view' => 'View',
        'close' => 'Close',
        'confirm' => 'Confirm',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
        'loading' => 'Loading...',
        'no_data' => 'No data available',
        'error' => 'An error occurred',
        'success' => 'Operation successful',
        'copied' => 'Copied to clipboard',
        'yes' => 'Yes',
        'no' => 'No',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'page' => 'Page',
        'of' => 'of',
        'showing' => 'Showing',
        'to' => 'to',
        'results' => 'results',
    ],

];
