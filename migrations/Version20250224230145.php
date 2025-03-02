<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250224230145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reaction table for emoji reactions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE reaction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, type VARCHAR(20) NOT NULL, emoji VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_A4D707F7A76ED395 (user_id), INDEX IDX_A4D707F74B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (user_id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F74B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE reaction');
    }
} 