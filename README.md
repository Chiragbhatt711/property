# qqtube

1. Create .env file
	add database name in your phpmyadmin below is phpmyadmin link
    http://localhost/phpmyadmin

2. Run below command to update composer and download dependencies in your local 
    composer update

3. run below command to generate key in .env

	php artisan key:generate

4. Run below command to create tables in your local database
    php artisan migrate

5. after run below commands


php artisan db:seed --class=CreateAdminUserSeeder

6. Run below command for run project
    php artisan serve --port=2023
    copy http://127.0.0.1:2023 this url to run project

    
7. Admin Login
    email = admin@admin.com
    password = admin#2024


8. Add in .env file

# AVANTHGARDE_PAYMENT_URL = "https://www.avantgardepayments.com/agcore/payment" #Live
AVANTHGARDE_PAYMENT_URL = "https://sandbox.avantgardepayments.com:8082/agcore/payment" #Sandbox

# AVANTHGARDE_MERCHANT_ID = "201710300001" #Live
AVANTHGARDE_MERCHANT_ID = "202104210302" #Sandbox
# AVANTHGARDE_MERCHANT_KEY = "HvKjjFAS3iIa3zbS7WNMYLuPndu8priH3mSdi8voskw=" #Live
AVANTHGARDE_MERCHANT_KEY = "N2B808GWxEls3oFzOz6wfxgEpSfPaQunLCU54vDJty4=" #Sandbox

RECAPTCHA_SITE_KEY = 6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI #use in localhost
RECAPTCHA_SECRET_KEY = 6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe #use in localhost



COINGATE_TOKEN=sxq39wZEzKDQUbew3zLVDSABXPReqsdsGVxknyrW
CALLBACK_URL=http://143.110.178.186/coingate/callback.php
# CALLBACK_URL=http://127.0.0.1:8000/coingate-callback
CANCEL_URL=http://127.0.0.1:8000/coingate-cancel
SUCCESS_URL=http://127.0.0.1:8000/coingate-success
