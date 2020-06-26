<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200624170429 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aliments (id INT AUTO_INCREMENT NOT NULL, categorie_aliment_id INT DEFAULT NULL, nom_aliment VARCHAR(255) NOT NULL, INDEX IDX_B7C2C9DCDF9255BD (categorie_aliment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_aliment (id INT AUTO_INCREMENT NOT NULL, nom_categorie_aliment VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aliments ADD CONSTRAINT FK_B7C2C9DCDF9255BD FOREIGN KEY (categorie_aliment_id) REFERENCES categorie_aliment (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aliments DROP FOREIGN KEY FK_B7C2C9DCDF9255BD');
        $this->addSql('DROP TABLE aliments');
        $this->addSql('DROP TABLE categorie_aliment');
    }
}
