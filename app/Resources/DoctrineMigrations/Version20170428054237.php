<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Replace session_type_css_class with calendar_color in session_type table
 */
class Version20170428054237 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session_type ADD calendar_color VARCHAR(7) NOT NULL');
        $this->addSql("UPDATE session_type SET calendar_color='#32edfc' WHERE session_type_css_class='clerkship'");
        $this->addSql("UPDATE session_type SET calendar_color='#ceccfe' WHERE session_type_css_class='exam'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='holiday'");
        $this->addSql("UPDATE session_type SET calendar_color='#ceccfe' WHERE session_type_css_class='hospice'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffcc67' WHERE session_type_css_class='laboratory'");
        $this->addSql("UPDATE session_type SET calendar_color='#99cdff' WHERE session_type_css_class='large-group-presentation'");
        $this->addSql("UPDATE session_type SET calendar_color='#00cc65' WHERE session_type_css_class='lecture'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffff00' WHERE session_type_css_class='opt-review-session'");
        $this->addSql("UPDATE session_type SET calendar_color='#cdffff' WHERE session_type_css_class='osce'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffb1b1' WHERE session_type_css_class='physical-exam'");
        $this->addSql("UPDATE session_type SET calendar_color='#fe7a78' WHERE session_type_css_class='problem-based-learning'");
        $this->addSql("UPDATE session_type SET calendar_color='#fe7a78' WHERE session_type_css_class='small-group'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='preceptorship'");
        $this->addSql("UPDATE session_type SET calendar_color='#ed81ee' WHERE session_type_css_class='reading-day'");
        $this->addSql("UPDATE session_type SET calendar_color='#32edfc' WHERE session_type_css_class='rounds'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='outpatient-clinic'");
        $this->addSql("UPDATE session_type SET calendar_color='#32edfc' WHERE session_type_css_class='call'");
        $this->addSql("UPDATE session_type SET calendar_color='#fe7a78' WHERE session_type_css_class='team-based-learning'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='exam-institutional-clinical'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='exam-institutional-writ-comp'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='exam-institutional-oral'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='exam-national-norm-standard-subj'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='multisource-assessment'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='narrative-assessment'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='practical-lab'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='stimulated-recall'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='self-assessment'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='clinical-doc-review'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='rating-checklist'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='exam-licensure-clinical'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='exam-licensure-writ-comp'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='participation'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='oral-patient-presentation'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='peer-assessment'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='portfolio-assessment'");
        $this->addSql("UPDATE session_type SET calendar_color='#c79376' WHERE session_type_css_class='research-project-assessment'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='independent-learning'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='reflection'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='self-directed-learning'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='concept-mapping'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='conference'");
        $this->addSql("UPDATE session_type SET calendar_color='#00cc65' WHERE session_type_css_class='discussion-large'");
        $this->addSql("UPDATE session_type SET calendar_color='#fe7a78' WHERE session_type_css_class='team-building'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='simulation'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='case-based-instruction'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='demonstration'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='games'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='journal-club'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='mentorship'");
        $this->addSql("UPDATE session_type SET calendar_color='#32edfc' WHERE session_type_css_class='learner-patient-presentation'");
        $this->addSql("UPDATE session_type SET calendar_color='#32edfc' WHERE session_type_css_class='faculty-patient-presentation'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='peer-teaching'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='research'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='role-play-drama'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='performance'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='tutorial'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='service-learning-activity'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='clinical-performance'");
        $this->addSql("UPDATE session_type SET calendar_color='#00cc65' WHERE session_type_css_class='panel-presentation'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='learner-project-presentation'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='movie'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='social-event'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='deadline'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE session_type_css_class='ceremony'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='ambulatory-clinical-experience'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='inpatient-clinical-experience'");
        $this->addSql("UPDATE session_type SET calendar_color='#dddddd' WHERE session_type_css_class='ward-rounds'");
        $this->addSql("UPDATE session_type SET calendar_color='#fe7a78' WHERE session_type_css_class='discussion-small'");
        $this->addSql("UPDATE session_type SET calendar_color='#fe7a78' WHERE session_type_css_class='workshop'");
        $this->addSql("UPDATE session_type SET calendar_color='#ffffff' WHERE calendar_color=''");

        $this->addSql('ALTER TABLE session_type DROP session_type_css_class');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session_type ADD session_type_css_class VARCHAR(64) DEFAULT NULL');
        $this->addSql("UPDATE session_types SET session_type_css_color='clerkship' WHERE calendar_color='#32edfc'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam' WHERE calendar_color='#ceccfe'");
        $this->addSql("UPDATE session_types SET session_type_css_color='holiday' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='hospice' WHERE calendar_color='#ceccfe'");
        $this->addSql("UPDATE session_types SET session_type_css_color='laboratory' WHERE calendar_color='#ffcc67'");
        $this->addSql("UPDATE session_types SET session_type_css_color='large-group-presentation' WHERE calendar_color='#99cdff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='lecture' WHERE calendar_color='#00cc65'");
        $this->addSql("UPDATE session_types SET session_type_css_color='opt-review-session' WHERE calendar_color='#ffff00'");
        $this->addSql("UPDATE session_types SET session_type_css_color='osce' WHERE calendar_color='#cdffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='physical-exam' WHERE calendar_color='#ffb1b1'");
        $this->addSql("UPDATE session_types SET session_type_css_color='problem-based-learning' WHERE calendar_color='#fe7a78'");
        $this->addSql("UPDATE session_types SET session_type_css_color='small-group' WHERE calendar_color='#fe7a78'");
        $this->addSql("UPDATE session_types SET session_type_css_color='preceptorship' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='reading-day' WHERE calendar_color='#ed81ee'");
        $this->addSql("UPDATE session_types SET session_type_css_color='rounds' WHERE calendar_color='#32edfc'");
        $this->addSql("UPDATE session_types SET session_type_css_color='outpatient-clinic' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='call' WHERE calendar_color='#32edfc'");
        $this->addSql("UPDATE session_types SET session_type_css_color='team-based-learning' WHERE calendar_color='#fe7a78'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam-institutional-clinical' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam-institutional-writ-comp' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam-institutional-oral' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam-national-norm-standard-subj' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='multisource-assessment' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='narrative-assessment' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='practical-lab' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='stimulated-recall' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='self-assessment' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='clinical-doc-review' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='rating-checklist' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam-licensure-clinical' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='exam-licensure-writ-comp' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='participation' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='oral-patient-presentation' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='peer-assessment' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='portfolio-assessment' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='research-project-assessment' WHERE calendar_color='#c79376'");
        $this->addSql("UPDATE session_types SET session_type_css_color='independent-learning' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='reflection' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='self-directed-learning' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='concept-mapping' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='conference' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='discussion-large' WHERE calendar_color='#00cc65'");
        $this->addSql("UPDATE session_types SET session_type_css_color='team-building' WHERE calendar_color='#fe7a78'");
        $this->addSql("UPDATE session_types SET session_type_css_color='simulation' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='case-based-instruction' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='demonstration' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='games' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='journal-club' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='mentorship' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='learner-patient-presentation' WHERE calendar_color='#32edfc'");
        $this->addSql("UPDATE session_types SET session_type_css_color='faculty-patient-presentation' WHERE calendar_color='#32edfc'");
        $this->addSql("UPDATE session_types SET session_type_css_color='peer-teaching' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='research' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='role-play-drama' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='performance' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='tutorial' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='service-learning-activity' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='clinical-performance' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='panel-presentation' WHERE calendar_color='#00cc65'");
        $this->addSql("UPDATE session_types SET session_type_css_color='learner-project-presentation' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='movie' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='social-event' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='deadline' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='ceremony' WHERE calendar_color='#ffffff'");
        $this->addSql("UPDATE session_types SET session_type_css_color='ambulatory-clinical-experience' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='inpatient-clinical-experience' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='ward-rounds' WHERE calendar_color='#dddddd'");
        $this->addSql("UPDATE session_types SET session_type_css_color='discussion-small' WHERE calendar_color='#fe7a78'");
        $this->addSql("UPDATE session_types SET session_type_css_color='workshop' WHERE calendar_color='#fe7a78'");
        $this->addSql("UPDATE session_types SET session_type_css_color='unknown' WHERE session_type_css_color=''");

        $this->addSql('ALTER TABLE session_type DROP calendar_color');
    }
}
