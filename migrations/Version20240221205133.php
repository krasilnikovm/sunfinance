<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221205133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan (id UUID NOT NULL, customer_id UUID NOT NULL, reference VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, amount_issued INT NOT NULL, amount_to_pay INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN loan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN loan.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE payment (id UUID NOT NULL, payment_order_id UUID DEFAULT NULL, loan_id UUID NOT NULL, amount INT NOT NULL, status VARCHAR(30) NOT NULL, ref_id VARCHAR(255) NOT NULL, payment_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payer_firstname VARCHAR(255) NOT NULL, payer_lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D21B741A9 ON payment (ref_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840DCD592F50 ON payment (payment_order_id)');
        $this->addSql('CREATE INDEX IDX_6D28840DCE73868F ON payment (loan_id)');
        $this->addSql('COMMENT ON COLUMN payment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.payment_order_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.loan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.payment_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE payment_order (id UUID NOT NULL, payment_id UUID DEFAULT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A260A52A4C3A3BB ON payment_order (payment_id)');
        $this->addSql('COMMENT ON COLUMN payment_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_order.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCD592F50 FOREIGN KEY (payment_order_id) REFERENCES payment_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCE73868F FOREIGN KEY (loan_id) REFERENCES loan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_order ADD CONSTRAINT FK_A260A52A4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DCD592F50');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DCE73868F');
        $this->addSql('ALTER TABLE payment_order DROP CONSTRAINT FK_A260A52A4C3A3BB');
        $this->addSql('DROP TABLE loan');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_order');
    }
}
