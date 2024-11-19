<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119094012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE book ADD is_promoted BOOLEAN DEFAULT false");
        $this->addSql("UPDATE book SET is_promoted = false");

        $this->addSql("ALTER TABLE book ADD promotion_status VARCHAR(10) DEFAULT 'None'");
        $this->addSql("UPDATE book SET promotion_status = CASE WHEN is_promoted = true THEN 'Basic' ELSE 'None' END");

        $this->addSql("ALTER TABLE book DROP is_promoted");

        $this->addSql("ALTER TABLE book ADD slug VARCHAR(255)");

        $this->addSql("UPDATE book SET slug = CONCAT('book-', id)");

        $this->addSql("ALTER TABLE book ALTER COLUMN slug SET NOT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_CBE5A331989D9B62 ON book (slug)");

        $this->addSql('ALTER TABLE bookmark ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE bookmark ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE bookmark ALTER book_id TYPE UUID');
        $this->addSql('ALTER TABLE bookmark ALTER bookmarked_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN bookmark.id IS \'\'');
        $this->addSql('COMMENT ON COLUMN bookmark.user_id IS \'\'');
        $this->addSql('COMMENT ON COLUMN bookmark.book_id IS \'\'');
        $this->addSql('COMMENT ON COLUMN bookmark.bookmarked_at IS \'\'');
        $this->addSql('ALTER TABLE parchment ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN parchment.id IS \'\'');
        $this->addSql('ALTER TABLE review ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER book_id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER published_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN review.id IS \'\'');
        $this->addSql('COMMENT ON COLUMN review.user_id IS \'\'');
        $this->addSql('COMMENT ON COLUMN review.book_id IS \'\'');
        $this->addSql('COMMENT ON COLUMN review.published_at IS \'\'');
        $this->addSql('ALTER TABLE "user" ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parchment ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN parchment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP INDEX UNIQ_CBE5A331989D9B62');
        $this->addSql('ALTER TABLE book DROP promotion_status');
        $this->addSql('ALTER TABLE book DROP slug');
        $this->addSql('ALTER TABLE book ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN book.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE review ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER published_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE review ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER book_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.book_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE bookmark ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE bookmark ALTER bookmarked_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bookmark ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE bookmark ALTER book_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN bookmark.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bookmark.bookmarked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookmark.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bookmark.book_id IS \'(DC2Type:uuid)\'');
    }
}
