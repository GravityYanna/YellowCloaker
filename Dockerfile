FROM php:8.0-apache

# Instala as extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Definir o ServerName para localhost para evitar mensagens de aviso
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copia o código fonte para o diretório raiz do Apache
COPY . /var/www/html/

# Define o ponto de entrada
CMD ["apache2-foreground"]
