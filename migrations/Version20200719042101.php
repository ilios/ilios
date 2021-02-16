<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20200719042101 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Add student advisors to courses and sessions';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE course_student_advisor (course_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D5D0445E591CC992 (course_id), INDEX IDX_D5D0445EA76ED395 (user_id), PRIMARY KEY(course_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_student_advisor (session_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CBB93279613FECDF (session_id), INDEX IDX_CBB93279A76ED395 (user_id), PRIMARY KEY(session_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course_student_advisor ADD CONSTRAINT FK_D5D0445E591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_student_advisor ADD CONSTRAINT FK_D5D0445EA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_student_advisor ADD CONSTRAINT FK_CBB93279613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_student_advisor ADD CONSTRAINT FK_CBB93279A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE course_student_advisor');
        $this->addSql('DROP TABLE session_student_advisor');
    }
}
