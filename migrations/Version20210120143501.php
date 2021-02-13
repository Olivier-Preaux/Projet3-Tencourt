<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210120143501 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_tennis_match (user_id INT NOT NULL, tennis_match_id INT NOT NULL, INDEX IDX_F5DCAD75A76ED395 (user_id), INDEX IDX_F5DCAD75B26B5FAA (tennis_match_id), PRIMARY KEY(user_id, tennis_match_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_tennis_match ADD CONSTRAINT FK_F5DCAD75A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tennis_match ADD CONSTRAINT FK_F5DCAD75B26B5FAA FOREIGN KEY (tennis_match_id) REFERENCES tennis_match (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_tennis_match');
    }
}
