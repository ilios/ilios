<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Populates the aamc_method and aamc_mecrs tables.
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
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0101','Provide patient care that is compassionate, appropriate, and effective for the treatment of health problems and the promotion of health')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0102','Perform all medical, diagnostic, and surgical procedures considered essential for the area of practice')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0103',' Gather essential and accurate information about patients and their condition through history-taking, physical examination, and the use of laboratory data, imaging and other tests')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0104','Interpret laboratory data, imaging studies, and other tests required for the area of practice')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0105','Counsel and educate patients and their families to empower them to participate in their care, showing consideration for their perspective throughout treatment')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0106','Make informed decisions about diagnostic and therapeutic interventions based on patient information and preferences, up-to-date scientific evidence, and clinical judgment')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0107','Develop and carry out patient management plans')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0108','Provide appropriate referral of patients including ensuring continuity of care throughout transitions between providers or settings, and following up on patient progress and outcomes')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0109','Provide health care services to patients, families, and communities aimed at preventing health problems')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0201','Demonstrate an investigatory and analytic approach to clinical situations')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0202','Apply established and emerging bio-physical scientific principles fundamental to health care for patients and populations')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0203','Apply established and emerging principles of clinical sciences to diagnostic and therapeutic decision-making, clinical problem-solving, and other aspects of evidence-based health care')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0204','Apply principles of epidemiological sciences to the identification of health problems, risk factors, treatment strategies, resources, and disease prevention/health promotion efforts for patients and populations')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0205','Apply principles of social-behavioral sciences to provision of patient care, including assessment of the impact of psychosocial-cultural influences on health, disease, care-seeking, care-compliance, barriers to and attitudes toward care')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0206','Contribute to the creation, dissemination, application, and translation of new health care knowledge and practices')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0301','Identify strengths, deficiencies, and limits in one’s knowledge and expertise')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0302','Set learning and improvement goals')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0303','Identify and perform learning activities that address one’s gaps in knowledge, skills or attitudes')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0304','Systematically analyze practice using quality improvement methods, and implement changes with the goal of practice improvement')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0305','Incorporate feedback into daily practice')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0306','Locate, appraise, and assimilate evidence from scientific studies related to patients’ health problems')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0307','Use information technology to optimize learning')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0308','Participate in the education of patients, families, students, trainees, peers and other health professionals')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0309','Use information technology to obtain and utilize information about individual patients, populations of patients being served or communities from which patients are drawn to improve care')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0310','Continually identify, analyze, and implement new knowledge, guidelines, standards, technologies, products, or services that have been demonstrated to improve outcomes')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0401','Communicate effectively with patients, families, and the public, as appropriate, across a broad range of socioeconomic and cultural backgrounds')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0402','Communicate effectively with colleagues within one’s profession or specialty, other health professionals, and health related agencies')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0403','Work effectively with others as a member or leader of a health care team or other professional group')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0404','Act in a consultative role to other health professionals')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0405','Maintain comprehensive, timely, and legible medical records')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0406','Demonstrate sensitivity, honesty, and compassion in difficult conversations about issues such as death, end-of-life issues, adverse events, bad news, disclosure of errors, and other sensitive topics')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0501','Demonstrate compassion, integrity, and respect for others')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0502','Demonstrate responsiveness to patient needs that supersedes self-interest')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0503','Demonstrate respect for patient privacy and autonomy')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0504','Demonstrate accountability to patients, society and the profession')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0505','Demonstrate sensitivity and responsiveness to a diverse patient population, including but not limited to diversity in gender, age, culture, race, religion, disabilities, and sexual orientation')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0506','Demonstrate a commitment to ethical principles pertaining to provision or withholding of care, confidentiality, informed consent, and business practices, including compliance with relevant laws, policies, and regulations')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0601','Work effectively in various health care delivery settings and systems relevant to their clinical specialty')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0602','Coordinate patient care within the health care system relevant to their clinical specialty')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0603','Incorporate considerations of cost awareness and risk-benefit analysis in patient and/or population-based care')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0604','Advocate for quality patient care and optimal patient care systems')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0605','Work in interprofessional teams to enhance patient safety and improve patient care quality')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0606','Participate in identifying system errors and implementing potential systems solutions')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0701','Work in cooperation with other professionals to establish and maintain a climate of respect, dignity, diversity, ethical integrity, and trust in order to enhance team functioning and serve the needs of patients, families, and populations')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0702','Utilize and enhance one’s own expertise by understanding and engaging the unique and diverse knowledge, skills, and abilities of other professionals to enhance team performance and maximize the quality of patient care')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0703','Exchange relevant information effectively with patients, families, communities, and other health professionals in a respectful, responsive, and responsible manner, considering varied perspectives and ensuring common understanding of, agreement with, and adherence to care decisions for optimal outcomes')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0704','Participate in and engage other members of interprofessional patient care teams in the establishment, development, leadership, and continuous enhancement of the team in order to provide care that is safe, timely, efficient, effective, and equitable')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0801','Develop the ability to use self-awareness of knowledge, skills and emotional limitations to engage in appropriate help-seeking behaviors')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0802','Demonstrate healthy coping mechanisms to respond to stress')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0803','Manage conflict between personal and professional responsibilities')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0804','Practice flexibility and maturity in adjusting to change with the capacity to alter behavior')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0805','Demonstrate trustworthiness that makes colleagues feel secure when one is responsible for the care of patients')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0806','Provide leadership skills that enhance team functioning, the learning environment, and/or the health care delivery system')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0807','Demonstrate self-confidence that puts patients, families, and members of the health care team at ease')");
        $this->db->query("INSERT INTO `aamc_mecrs` (`mecrs_id`, `description`) VALUES ('aamc-mecrs-comp-c0808','Recognize that ambiguity is part of clinical health care and respond by utilizing appropriate resources in dealing with uncertainty')");
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
DELETE FROM `aamc_mecrs` WHERE `mecrs_id` IN ('aamc-mecrs-comp-c0101', 'aamc-mecrs-comp-c0102', 'aamc-mecrs-comp-c0103',
    'aamc-mecrs-comp-c0104', 'aamc-mecrs-comp-c0105', 'aamc-mecrs-comp-c0106', 'aamc-mecrs-comp-c0107', 'aamc-mecrs-comp-c0108',
    'aamc-mecrs-comp-c0109', 'aamc-mecrs-comp-c0201', 'aamc-mecrs-comp-c0202', 'aamc-mecrs-comp-c0203', 'aamc-mecrs-comp-c0204',
    'aamc-mecrs-comp-c0205', 'aamc-mecrs-comp-c0206', 'aamc-mecrs-comp-c0301', 'aamc-mecrs-comp-c0302', 'aamc-mecrs-comp-c0303',
    'aamc-mecrs-comp-c0304', 'aamc-mecrs-comp-c0305', 'aamc-mecrs-comp-c0306', 'aamc-mecrs-comp-c0307', 'aamc-mecrs-comp-c0308',
    'aamc-mecrs-comp-c0309', 'aamc-mecrs-comp-c0310', 'aamc-mecrs-comp-c0401', 'aamc-mecrs-comp-c0402', 'aamc-mecrs-comp-c0403',
    'aamc-mecrs-comp-c0404', 'aamc-mecrs-comp-c0405', 'aamc-mecrs-comp-c0406', 'aamc-mecrs-comp-c0501', 'aamc-mecrs-comp-c0502',
    'aamc-mecrs-comp-c0503', 'aamc-mecrs-comp-c0504', 'aamc-mecrs-comp-c0505', 'aamc-mecrs-comp-c0506', 'aamc-mecrs-comp-c0601',
    'aamc-mecrs-comp-c0602', 'aamc-mecrs-comp-c0603', 'aamc-mecrs-comp-c0604', 'aamc-mecrs-comp-c0605', 'aamc-mecrs-comp-c0606',
    'aamc-mecrs-comp-c0701', 'aamc-mecrs-comp-c0702', 'aamc-mecrs-comp-c0703', 'aamc-mecrs-comp-c0704', 'aamc-mecrs-comp-c0801',
    'aamc-mecrs-comp-c0802', 'aamc-mecrs-comp-c0803', 'aamc-mecrs-comp-c0804', 'aamc-mecrs-comp-c0805', 'aamc-mecrs-comp-c0806',
    'aamc-mecrs-comp-c0807', 'aamc-mecrs-comp-c0808')
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
