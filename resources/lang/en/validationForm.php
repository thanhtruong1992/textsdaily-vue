<?php

return [
    "name" => [
        "required" => "Please enter your name!",
    ],
    "reader_id" => [
        "required" => "Please select client!",
    ],
    "email" => [
        "required" => "Please enter your email!",
        "email" => "Please enter a valid email address!",
        "multiple" => "Please enter a valid email address!",
    ],
    "password" => [
        "required" => "Please provide a password!",
        "confirmed" => "Please enter the same value password again!",
    ],
    "confirm_password" => [
        "required" => "Please provide a confirm password!",
    ],
    "country_select" => [
        "required" => "Please select country!",
    ],
    "timezone_select" => [
        "required" => "Please select time zone!",
    ],
    "language_select" => [
        "required" => "Please select language!",
    ],
    "currency_select" => [
        "required" => "Please select currency!",
    ],
    "default_price" => [
        "required" => "Please provide default price per sms!",
        "min" => "Default price per sms must be greater than 0!",
    ],
    "mobile_number" => [
        "required" => "Please enter mobile number!",
    ],
    "title" => [
        "required" => "Please enter title!",
    ],
    "first_name" => [
        "required" => "Please enter first name!",
    ],
    "last_name" => [
        "required" => "Please enter last name!",
    ],
    "group" => [
        "required" => "Please select group!",
    ],
    "additional_field_1" => [
        "required" => "Please enter additional field 1!",
    ],
    "additional_field_2" => [
        "required" => "Please enter additional field 2!",
    ],
    "additional_field_3" => [
        "required" => "Please enter additional field 3!",
    ],
    "campaign_name" => [
        "required" => "Please enter campaign name!",
    ],
    "client_name" => [
        "required" => "Please enter client name!",
    ],
    "template_name" => [
        "required" => "Please enter template name!",
    ],
    "schedule_date" => [
        "required" => "Please selected schedule!"
    ],
    "sender" => [
        "required" => "Please enter sender!",
        "maxlength" => "Please enter no more than 11 characters"
    ],
    "billing_type" => [
        "required" => "Please select account type!"
    ],
    "message" => [
        "required" => "Please enter message!",
    ],
    "valid_period" => [
        "required" => "Please select period!"
    ],
    "select_hour" => [
        "required" => "Please select hour!",
    ],
    "select_group" => [
        "required" => "Please select groups!",
    ],
    "select_recipient" => [
        "required" => "Please select recipients!",
    ],
    "select_service_provider" => [
        "required" => "Please select default service provider!",
    ],
    "subscriber_list_name" => [
        "required" => "Please enter subscriber list name!",
    ],
    "phone_number_test" => [
        "required" => "Please enter test phone number!",
    ],
    "subscriber_list_description" => [
        "required" => "Please enter subscriber list description!",
    ],
    "file_csv" => [
            "required" => "Please choose file!",
            "filesize" => "Max file size 40MB!"
    ],
    "file_image" => [
            "required" => "Please choose file!",
            "filesize" => "Max file size 25MB!"
    ],
    "file_terminated" => [
            "required" => "Select file terminated!"
    ],
    "file_enclosed" => [
            "required" => "Select file enclosed!"
    ],
    "content" => [
            "required" => "Please enter content!"
    ],
    "field" => [
            "required" => "Please enter field!"
    ],
    'send_time' => [
            'required' => 'Please select send time!'
    ],
    'time' => [
            'required' => 'Please select time!',
            'time' => "Please enter a valid time!"
    ],
    'send_timezone' => [
            'required' => 'Please select timezone!'
    ],
    'link' => [
            'required' => 'Link is required!',
            'url' => "Please enter a valid url address!"
    ],
    "datetime" => [
            'valid' => "Date must be greater than now!"
    ],
    "client_select" => [
            "required" => "Please select client!"
    ],
    "description" => [
            "required" => "Please enter description!"
    ],
    "username" => [
            "required" => "Please enter username!",
            "username" => "Please enter a valid username!",
            "unique" => "The username has already been taken!",
            "min" => "The username must be at least 8 characters."
    ]
];
