FROM php:8.4-fpm

# --- Устанавливаем системные зависимости ---
RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip

# --- Устанавливаем PHP-расширения ---
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions decimal opcache pdo_pgsql pdo_mysql pcntl mysqli bcmath intl zip

# --- Устанавливаем Composer ---
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# --- Добавляем аргументы пользователя ---
ARG UID=1000
ARG GID=1000
ARG USER=appuser

# --- Создаём пользователя и группу с теми же UID/GID, что и у тебя на хосте ---
RUN groupadd -g ${GID} ${USER} \
    && useradd -m -u ${UID} -g ${GID} -s /bin/bash ${USER}

# --- Делаем /app рабочей директорией ---
WORKDIR /app

# --- Настраиваем права ---
RUN chown -R ${USER}:${USER} /app

# --- Настраиваем git (чтобы не ругался на dubious ownership) ---
RUN git config --global --add safe.directory /app

# --- Переключаемся на пользователя разработки ---
USER ${USER}

# --- Команда по умолчанию ---
CMD ["php-fpm"]
