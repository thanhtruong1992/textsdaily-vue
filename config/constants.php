<?php
return [
        "lang" => [
                "en" => [
                        "filter_fiels" => [
                                "subscriber_status" => "Subscriber Status",
                                "mobile_number" => "Mobile Number",
                                "first_name" => "First Name",
                                "last_name" => "Last Name"
                        ]
                ]
        ],
        "path_sample_mobile_pattern_template" => "sample_templates/mobile-pattern-table.csv",
        "path_sample_service_provider_template" => "sample_templates/preferred_service_provider.csv",
        "path_sample_mnc_mcc_template" => "sample_templates/mcc-mnc-table.csv",
        "path_sample_price_config_template" => "sample_templates/price-config-template.csv",
        "path_file_logo" => "uploads/logos/",
        "path_file_subscriber" => "uploads/subscribers/",
        "path_file_export_subscriber" => "uploads/export-subscribers/",
        "path_file_result_import_subscriber" => "uploads/result-subscribers/",
        "path_file_report_center" => "uploads/report-centers/",
        "path_file_transaction_history" => "uploads/transaction-histories/",
        "path_file_auto_trigger_report" => "uploads/auto-trigger-reports/",
        "path_file_export_inbound_message" => "uploads/inbound-message",
        "array_color_chart" => [
                "#327017",
                "#4f9b2e",
                "#5ebf35",
                "#95f26d",
                "#b9f79e",
        ],
        "color_chart_default" => "#b9f79e",
        "color_chart_pdf" => "#8DBE6A",
        "data_month" => [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
        ],
        "data_month_short" => [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "June",
                "July",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec"
        ],
        "expired_token" => 1,
        "limit_queue"   => 10,
        "linit_again_queue" => 4,
        "list_email_notify_queue_again" => "truongngo@success-ss.com.vn, dattran@success-ss.com.vn, babatunde@success-ss.com.vn",
        "limit_queue_report" => 10,
        "table_service_provider_report" => [
                'INFOBIP' => 'infobip_reports',
                'ROUTEMOBILE' => 'router_mobile_reports'
        ],
        "service_provider_report" => ['INFOBIP', 'ROUTEMOBILE']
];