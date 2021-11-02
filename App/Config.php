<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'mvclogin';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /** 
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'secret';

    /**
     * Set the mail sender
     *
     * @var string
     */
	const EMAIL_FROM = '';
	const SMTP_HOST = '';
	const SMTP_USERNAME = ''; 
	const SMTP_PASSWORD = '';
    const EMAIL_RECIPIENT = ''; 
    const EMAIL_FROM_NAME = ''; 
    const EMAIL_PORT = 000;





}

