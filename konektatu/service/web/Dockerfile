# Usamos la imagen base de PHP con Apache
FROM php:7.4-apache

# Instalar SSH y dependencias necesarias
RUN apt-get update && apt-get install -y \
    openssh-server \
    && rm -rf /var/lib/apt/lists/*

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Copiar los archivos al contenedor
COPY . /var/www/html/

# Crear el directorio uploads y configurar los permisos
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 777 /var/www/html/uploads

# Configurar Apache para permitir acceso a uploads
RUN echo "<Directory /var/www/html/uploads>\n\
    AllowOverride None\n\
    Require all granted\n\
    Options +Indexes +FollowSymLinks +ExecCGI\n\
</Directory>" >> /etc/apache2/apache2.conf

# Configurar SSH (instalar y habilitar)
RUN mkdir /var/run/sshd 
RUN useradd -m proba && echo "proba:xza" | chpasswd

# Exponer puertos 80 (HTTP) y 22 (SSH)
EXPOSE 80 22

# Iniciar SSH y Apache con supervisord
CMD service ssh start && apache2-foreground
