<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241106192411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(64) NOT NULL, second_name VARCHAR(64) NOT NULL, birthday DATE NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_17EB68C0A76ED395 (user_id), INDEX IDX_17EB68C04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX title_price_idx ON product (title, price)');

        // add product data
        $this->addSql("INSERT INTO product (id, title, price) VALUES (1, 'Три товарища', 49.80);");
        $this->addSql("INSERT INTO product (id, title, price) VALUES (2, 'Триумфальная арка', 349.00);");
        $this->addSql("INSERT INTO product (id, title, price) VALUES (3, 'Один год жизни', 149.00);");
        $this->addSql("INSERT INTO product (id, title, price) VALUES (4, 'Северный дракон', 249.00);");

        // add user data
        $this->addSql("INSERT INTO user (id, first_name, second_name, birthday, created_at) VALUES (1, 'Петр', 'Петров', '2000-04-14', '2024-02-01 18:17:16');");
        $this->addSql("INSERT INTO user (id, first_name, second_name, birthday, created_at) VALUES (2, 'Иван', 'Иванов', '1997-06-17', '2024-02-02 18:17:43');");

         // add relation user_order data
        $this->addSql("INSERT INTO user_order (user_id, product_id, created_at) VALUES (1, 4, '2024-02-14 18:41:25');");
        $this->addSql("INSERT INTO user_order (user_id, product_id, created_at) VALUES (2, 1, '2024-02-14 18:40:52');");
        $this->addSql("INSERT INTO user_order (user_id, product_id, created_at) VALUES (2, 2, '2024-02-14 18:40:51');");
        $this->addSql("INSERT INTO user_order (user_id, product_id, created_at) VALUES (2, 3, '2024-02-02 18:40:45');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_order DROP FOREIGN KEY FK_17EB68C0A76ED395');
        $this->addSql('ALTER TABLE user_order DROP FOREIGN KEY FK_17EB68C04584665A');
        $this->addSql('DROP INDEX title_price_idx ON product');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_order');
    }
}
