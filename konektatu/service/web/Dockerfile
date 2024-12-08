# Usamos la imagen base de PHP con Apache
FROM php:7.4-apache

# Instalar SSH (si aún no está instalado)
RUN apt-get update && apt-get install -y \
    openssh-server \
    && rm -rf /var/lib/apt/lists/*

# Crear el directorio uploads
RUN mkdir -p /var/www/html/uploads

# Cambiar el propietario y los permisos de la carpeta uploads
RUN chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 775 /var/www/html/uploads

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Copiar los archivos al contenedor
COPY . /var/www/html/

# Configuración de Apache para permitir acceso a uploads
RUN echo "<Directory /var/www/html/uploads>\n\
    AllowOverride None\n\
    Require all granted\n\
    Options +Indexes +FollowSymLinks\n\
</Directory>" >> /etc/apache2/apache2.conf

# Configurar SSH (instalar y habilitar)
RUN mkdir /var/run/sshd && \
    echo 'root:rootpassword' | chpasswd && \
    sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config && \
    sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    service ssh start

# Exponer puertos 80 (HTTP) y 22 (SSH)
EXPOSE 80 22

# Iniciar SSH y Apache
CMD service ssh start && apache2-foreground
