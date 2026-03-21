# ใช้ PHP 8.2 พร้อม Apache
FROM php:8.2-apache

# ติดตั้งส่วนเสริมที่ Laravel ต้องใช้
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git

# ติดตั้ง PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# เปิดใช้งาน mod_rewrite ของ Apache
RUN a2enmod rewrite

# ตั้งค่าโฟลเดอร์ทำงาน
WORKDIR /var/www/html

# เอา Composer มาติดตั้ง
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ก๊อปปี้ไฟล์โปรเจกต์ทั้งหมดลงใน Container
COPY . /var/www/html

# ติดตั้ง Package ของ Laravel
RUN composer install --optimize-autoloader --no-dev

# ปรับสิทธิ์โฟลเดอร์ให้เขียนข้อมูลได้
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ชี้ให้เว็บไปอ่านไฟล์ที่โฟลเดอร์ public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
