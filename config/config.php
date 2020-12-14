<?php

/**
 * Database configuration
 */
define( 'DB_SERVER', 'localhost' );
define( 'DB_USER', 'root' );
define( 'DB_PASS', '' );
define( 'DB_NAME', 'dealer_inspire' );

/**
 * Email settings
 */
define( 'TO_ADDRESS', 'guy-smiley@example.com' );
define( 'FROM_ADDRESS', 'guy-frowny@example.com' );
define( 'EMAIL_SUBJECT', 'New contact form submission!' );

/**
 * Saves form submissions to database
 * Import the attached dealer_inspire.sql file into MySQL
 */
define( 'ENABLE_SAVING_FORMS', false );

/**
 * E-mails form submissions
 * Ensure that PHP sendmail is enabled and running on port 25
 * Verify your "SMTP" and "smtp_port" settings in php.ini
 */
define( 'ENABLE_MAILING_FORMS', false );