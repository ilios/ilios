<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Populates the aamc_method and aamc_pcrs tables.
 */
class Migration_populate_aamc_vocabulary_tables extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM001', 'Case-Based Instruction/Learning')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM002', 'Clinical Experience - Ambulatory')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM003', 'Clinical Experience - Inpatient')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM004', 'Concept Mapping')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM005', 'Conference')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM006', 'Demonstration')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM007', 'Discussion, Large Group (>12)')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM008', 'Discussion, Small Group (≤12)')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM009', 'Games')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM010', 'Independent Learning')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM011', 'Journal Club')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM012', 'Laboratory')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM013', 'Lecture')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM014', 'Mentorship')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM015', 'Patient Presentation - Faculty')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM016', 'Patient Presentation - Learner')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM017', 'Peer Teaching')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM018', 'Preceptorship')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM019', 'Problem-Based Learning (PBL)')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM020', 'Reflection')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM021', 'Research')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM022', 'Role Play/Dramatization')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM023', 'Self-Directed Learning')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM024', 'Service Learning Activity')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM025', 'Simulation')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM026', 'Team-Based Learning (TBL)')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM027', 'Team-Building')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM028', 'Tutorial')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM029', 'Ward Rounds')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('IM030', 'Workshop Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM001', 'Clinical Documentation Review')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM002', 'Clinical Performance Rating/Checklist')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM003', 'Exam - Institutionally Developed, Clinical Performance')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM004', 'Exam - Institutionally Developed, Written/ Computer-based')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM005', 'Exam - Institutionally Developed, Oral')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM006', 'Exam - Licensure, Clinical Performance')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM007', 'Exam - Licensure, Written/Computer-based')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM008', 'Exam - Nationally Normed/Standardized, Subject')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM009', 'Multisource Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM010', 'Narrative Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM011', 'Oral Patient Presentation')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM012', 'Participation')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM013', 'Peer Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM014', 'Portfolio-Based Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM015', 'Practical (Lab)')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM016', 'Research or Project Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM017', 'Self-Assessment')");
        $this->db->query("INSERT INTO `aamc_method` (`method_id`, `description`) VALUES ('AM018', 'Stimulated Recall')");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0101','Perform all medical, diagnostic, and surgical procedures considered essential for the area of practice');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0102','Gather essential and accurate information about patients and their conditions through history-taking, physical examination, and the use of laboratory data, imaging and other tests');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0103','Organize and prioritize responsibilities to provide care that is safe, effective, and efficient');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0104','Interpret laboratory data, imaging studies, and other tests required for the area of practice');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0105','Make informed decisions about diagnostic and therapeutic interventions based on patient information and preferences, up-to-date scientific evidence, and clinical judgment');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0106','Develop and carry out patient management plans');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0107','Counsel and educate patients and their families to empower them to participate in their care and enable shared decision making');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0108','Provide appropriate referral of patients including ensuring continuity of care throughout transitions between providers or settings, and following up on patient progress and outcomes');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0109','Provide health care services to patients, families, and communities aimed at preventing health problems');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0201','Demonstrate an investigatory and analytic approach to clinical situations');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0202','Apply established and emerging bio-physical scientific principles fundamental to health care for patients and populations');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0203','Apply established and emerging principles of clinical sciences to diagnostic and therapeutic decision-making, clinical problem-solving, and other aspects of evidence-based health care');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0204','Apply principles of epidemiological sciences to the identification of health problems, risk factors, treatment strategies, resources, and disease prevention/health promotion efforts for patients and populations');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0205','Apply principles of social-behavioral sciences to provision of patient care, including assessment of the impact of psychosocial and cultural influences on health, disease, care-seeking, care compliance, and barriers to and attitudes toward care');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0206','Contribute to the creation, dissemination, application, and translation of new health care knowledge and practices');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0301','Identify strengths, deficiencies, and limits in one’s knowledge and expertise');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0302','Set learning and improvement goals');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0303','Identify and perform learning activities that address one’s gaps in knowledge, skills and/or attitudes');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0304','Systematically analyze practice using quality improvement methods, and implement changes with the goal of practice improvement');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0305','Incorporate feedback into daily practice');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0306','Locate, appraise, and assimilate evidence from scientific studies related to patients’ health problems');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0307','Use information technology to optimize learning');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0308','Participate in the education of patients, families, students, trainees, peers and other health professionals');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0309','Obtain and utilize information about individual patients, populations of patients, or communities from which patients are drawn to improve care');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0310','Continually identify, analyze, and implement new knowledge, guidelines, standards, technologies, products, or services that have been demonstrated to improve outcomes');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0401','Communicate effectively with patients, families, and the public, as appropriate, across a broad range of socioeconomic and cultural backgrounds');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0402','Communicate effectively with colleagues within one''s profession or specialty, other health professionals, and health related agencies (see also 7.3)');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0403','Work effectively with others as a member or leader of a health care team or other professional group (see also 7.4)');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0404','Act in a consultative role to other health professionals');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0405','Maintain comprehensive, timely, and legible medical records');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0406','Demonstrate sensitivity, honesty, and compassion in difficult conversations, including those about death, end of life, adverse events, bad news, disclosure of errors, and other sensitive topics');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0501','Demonstrate compassion, integrity, and respect for others');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0502','Demonstrate responsiveness to patient needs that supersedes self-interest');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0503','Demonstrate respect for patient privacy and autonomy');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0504','Demonstrate accountability to patients, society, and the profession');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0505','Demonstrate sensitivity and responsiveness to a diverse patient population, including but not limited to diversity in gender, age, culture, race, religion, disabilities, and sexual orientation');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0506','Demonstrate a commitment to ethical principles pertaining to provision or withholding of care, confidentiality, informed consent, and business practices, including compliance with relevant laws, policies, and regulations');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0601','Work effectively in various health care delivery settings and systems relevant to one''s clinical specialty');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0602','Coordinate patient care within the health care system relevant to one''s clinical specialty');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0603','Incorporate considerations of cost awareness and risk-benefit analysis in patient and/or population-based care');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0604','Advocate for quality patient care and optimal patient care systems');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0605','Participate in identifying system errors and implementing potential systems solutions');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0606','Perform administrative and practice management responsibilities commensurate with one’s role, abilities, and qualifications');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0701','Work with other health professionals to establish and maintain a climate of mutual respect, dignity, diversity, ethical integrity, and trust');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0702','Use the knowledge of one’s own role and the roles of other health professionals to appropriately assess and address the health care needs of the patients and populations served');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0703','Communicate with other health professionals in a responsive and responsible manner that supports the maintenance of health and the treatment of disease in individual patients and populations');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0704','Participate in different team roles to establish, develop, and continuously enhance interprofessional teams to provide patient- and population-centered care that is safe, timely, efficient, effective, and equitable');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0801','Develop the ability to use self-awareness of knowledge, skills and emotional limitations to engage in appropriate help-seeking behaviors');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0802','Demonstrate healthy coping mechanisms to respond to stress');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0803','Manage conflict between personal and professional responsibilities');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0804','Practice flexibility and maturity in adjusting to change with the capacity to alter one''s behavior ');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0805','Demonstrate trustworthiness that makes colleagues feel secure when one is responsible for the care of patients');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0806','Provide leadership skills that enhance team functioning, the learning environment, and/or the health care delivery system');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0807','Demonstrate self-confidence that puts patients, families, and members of the health care team at ease');");
        $this->db->query("INSERT INTO `aamc_pcrs` (`pcrs_id`, `description`) VALUES ('aamc-pcrs-comp-c0808','Recognize that ambiguity is part of clinical health care and respond by utilizing appropriate resources in dealing with uncertainty');");
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();
        $sql =<<<EOL
DELETE FROM `aamc_method` WHERE `method_id` IN ('IM001', 'IM002', 'IM003', 'IM004', 'IM005', 'IM006', 'IM007', 'IM008',
    'IM009', 'IM010', 'IM011', 'IM012', 'IM013', 'IM014', 'IM015', 'IM016', 'IM017', 'IM018', 'IM019', 'IM020', 'IM021',
    'IM022', 'IM023', 'IM024', 'IM025', 'IM026', 'IM027', 'IM028', 'IM029', 'IM030', 'AM001', 'AM002', 'AM003', 'AM004',
    'AM005', 'AM006', 'AM007', 'AM008', 'AM009', 'AM010', 'AM011', 'AM012', 'AM013', 'AM014', 'AM015', 'AM016', 'AM017',
    'AM018')
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
DELETE FROM `aamc_pcrs` WHERE `pcrs_id` IN ('aamc-pcrs-comp-c0101', 'aamc-pcrs-comp-c0102', 'aamc-pcrs-comp-c0103',
    'aamc-pcrs-comp-c0104', 'aamc-pcrs-comp-c0105', 'aamc-pcrs-comp-c0106', 'aamc-pcrs-comp-c0107',
    'aamc-pcrs-comp-c0108', 'aamc-pcrs-comp-c0109', 'aamc-pcrs-comp-c0201', 'aamc-pcrs-comp-c0202',
    'aamc-pcrs-comp-c0203', 'aamc-pcrs-comp-c0204', 'aamc-pcrs-comp-c0205', 'aamc-pcrs-comp-c0206',
    'aamc-pcrs-comp-c0301', 'aamc-pcrs-comp-c0302', 'aamc-pcrs-comp-c0303', 'aamc-pcrs-comp-c0304',
    'aamc-pcrs-comp-c0305', 'aamc-pcrs-comp-c0306', 'aamc-pcrs-comp-c0307', 'aamc-pcrs-comp-c0308',
    'aamc-pcrs-comp-c0309', 'aamc-pcrs-comp-c0310', 'aamc-pcrs-comp-c0401', 'aamc-pcrs-comp-c0402',
    'aamc-pcrs-comp-c0403', 'aamc-pcrs-comp-c0404', 'aamc-pcrs-comp-c0405', 'aamc-pcrs-comp-c0406',
    'aamc-pcrs-comp-c0501', 'aamc-pcrs-comp-c0502', 'aamc-pcrs-comp-c0503', 'aamc-pcrs-comp-c0504',
    'aamc-pcrs-comp-c0505', 'aamc-pcrs-comp-c0506', 'aamc-pcrs-comp-c0601', 'aamc-pcrs-comp-c0602',
    'aamc-pcrs-comp-c0603', 'aamc-pcrs-comp-c0604', 'aamc-pcrs-comp-c0605', 'aamc-pcrs-comp-c0606',
    'aamc-pcrs-comp-c0701', 'aamc-pcrs-comp-c0702', 'aamc-pcrs-comp-c0703', 'aamc-pcrs-comp-c0704',
    'aamc-pcrs-comp-c0801', 'aamc-pcrs-comp-c0802', 'aamc-pcrs-comp-c0803', 'aamc-pcrs-comp-c0804',
    'aamc-pcrs-comp-c0805', 'aamc-pcrs-comp-c0806', 'aamc-pcrs-comp-c0807', 'aamc-pcrs-comp-c0808')
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
