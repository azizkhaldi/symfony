<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019142743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auther (id INT AUTO_INCREMENT NOT NULL, nb_books INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD auther_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331BCC5EBBA FOREIGN KEY (auther_id) REFERENCES auther (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A331BCC5EBBA ON book (auther_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331BCC5EBBA');
        $this->addSql('DROP TABLE auther');
        $this->addSql('DROP INDEX IDX_CBE5A331BCC5EBBA ON book');
        $this->addSql('ALTER TABLE book DROP auther_id');
    }
}
