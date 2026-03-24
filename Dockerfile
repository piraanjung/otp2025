# 1. ใช้ PHP 8.2 พร้อม Apache
FROM php:8.2-apache

# 2. ติดตั้งส่วนเสริมที่ Laravel ต้องใช้
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libzip-dev

# 3. ติดตั้ง PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. เปิดใช้งาน mod_rewrite ของ Apache สำหรับ Laravel
RUN a2enmod rewrite

# 5. ตั้งค่าโฟลเดอร์ทำงาน
WORKDIR /var/www/html

# 6. เอา Composer มาติดตั้ง
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. ก๊อปปี้ไฟล์โปรเจกต์ทั้งหมดลงใน Container
# (*** สำคัญ: ต้องไปลบ /storage ออกจาก .dockerignore ด้วย ***)
COPY . /var/www/html

# 8. ติดตั้ง Package ของ Laravel
RUN composer install --optimize-autoloader --no-dev --no-scripts --ignore-platform-reqs

# 9. 🌟 จัดการโฟลเดอร์และสิทธิ์ (Permission)
# สร้างโฟลเดอร์ที่จำเป็นต้องใช้เขียนไฟล์ (รวมถึง public/uploads หรือโฟลเดอร์ที่คุณต้องการ)
RUN mkdir -p /var/www/html/storage \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/public

# 10. ตั้งค่า Apache Document Root ไปที่ public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 11. ตั้งค่า Port สำหรับ Google Cloud Run (8080)
ENV PORT=8080
RUN sed -s -i -e "s/80/\${PORT}/" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
EXPOSE ${PORT}

# เริ่มการทำงานของ Apache
CMD ["apache2-foreground"]
