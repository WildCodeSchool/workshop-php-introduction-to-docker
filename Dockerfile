# Use the PHP version with Apache
FROM php:5.6-apache

# Copy your PHP code into the container
COPY . /var/www/html/

# Apache is already configured to expose port 80, 
# so you might not need the EXPOSE instruction