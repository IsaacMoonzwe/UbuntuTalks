Release Number: RV-1.0.1( Base Version TV-2.31.0.20211214)
Release Date: 20220216

Updates:
    task-95202 Development Work(Add Video Slide)  

Fixes:NA  
    
-------------------------------------------------------------
Release Number: RV-1.0.0
Release Date: 2022-01-25

Updates:
    task-94309 Add theme colour
    task-94259 Additional changes

Fixes:NA  
    
-------------------------------------------------------------


Release Number: TV-2.31.0.20211214
Release Date: 2021-12-14

Updates:
    task-88381 Group class slots: slot duration in the Group class module is to provide the ability to admin and tutor to create group classes with different slots

Fixes:
    bug_060972 - Sorting issue in teacher listing
    bug-060976 - Profile pic on meeting tool
    bug-060947 - Fixed past time clickable
    bug-060787 - Report issue emails
    bug-060927 - Fix wrong language on checkout
    bug-060396 - Performance related issue in unscheduled lessons Teacher and Students listing
    bug-060310 - The serial number under group class listing is incorrect at the Admin's end
    bug-060305 - Calender weekly schedule issue in case if -ve timezone teacher timezone
    bug-060230 - View calendar and confirm popup issue 
    bug-059564 - Lesson ending time label is incorrect at the Admin
    bug-060081 - Person should be in paid status after payment completed through the Paypal
    bug-060084 - Discount coupons are not reflected in the payment confirmation
    
-------------------------------------------------------------

Installation steps:

    System requirements:
        PHP version 7.4
        MySQL version 5.7.8(or above)

    Download the files and configure with your development/production environment.
    You can get all the files mentioned in .gitignore file from git-ignored-files directory.
    Copy following files from {root}/git-ignored-files/ to {root}/
        -.htaccess
        index.php 
        mbs-errors.log	
    Rename {root}/-.htaccess to .htaccess.	
    Copy {root}/git-ignored-files/public file to  {root}/public.
    Copy {root}/git-ignored-files/conf directory to  {root}/.	
    Rename {root}/public/-.htaccess with .htaccess.
    Copy user-upload-with-data or user-upload-without-data directory from {root}/git-ignored-files/ as per requirements and rename to 'user-uploads'
    Create a new "caching" directory under {root}/public/ 
    Create a new "cache" directory under {root}/public/ 
    Write permissions to
        {root}/user-uploads including all sub directories.
        {root}/public/caching/
        {root}/public/cache/
    Upload license file under the {root}/.
    Upload Fatbit library Core folder under the {root}/library/.
    Import database schema from {root}/database directory as per requirements.
    Define DB configuration under {root}/public/settings.php.
    Update following setting as per your requirements
        {root}/conf/conf-common.php
        define('CONF_DEVELOPMENT_MODE', false);
        define('CONF_USE_FAT_CACHE', false);
        define('ALLOW_EMAILS', false);
        define('SEARCH_MAX_COUNT', 1000);
    Install composer dependencies, Run command composer install
    After completion of installation open URL: {domain-name}/procedure
        for e.g: https://teach.yo-coach.com/procedure
    Set the cron after every 5 minutes with url {domain-name}/cron