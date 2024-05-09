FROM sonarsoftware/customerportal:c22c941fb92c79d554f0e26b23cfd64f9e467c6c as base

WORKDIR /var/www/html

COPY database/migrations/2024_05_09_000000_revert_password_resets_table_name.php database/migrations/

VOLUME /var/www/html/storage
EXPOSE 80 443
