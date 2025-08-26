<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826123305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP id_etat');
        $this->addSql('ALTER TABLE participant ADD site_id INT NOT NULL, DROP idparticipant');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11F6BD1646 ON participant (site_id)');
        $this->addSql('ALTER TABLE site CHANGE id_site sortie_id INT NOT NULL');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E4CC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id)');
        $this->addSql('CREATE INDEX IDX_694309E4CC72D953 ON site (sortie_id)');
        $this->addSql('ALTER TABLE sortie DROP id_sortie');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat ADD id_etat INT NOT NULL');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E4CC72D953');
        $this->addSql('DROP INDEX IDX_694309E4CC72D953 ON site');
        $this->addSql('ALTER TABLE site CHANGE sortie_id id_site INT NOT NULL');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11F6BD1646');
        $this->addSql('DROP INDEX IDX_D79F6B11F6BD1646 ON participant');
        $this->addSql('ALTER TABLE participant ADD idparticipant VARCHAR(255) NOT NULL, DROP site_id');
        $this->addSql('ALTER TABLE sortie ADD id_sortie INT NOT NULL');
    }
}
