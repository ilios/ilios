<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CompetencyData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => "Patient Care",
            'owningSchool' => "1",
            'objectives' => [],
            'children' => ['7','8','9','10','11','12'],
            'aamcPcrses' => [],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => "Medical Knowledge",
            'owningSchool' => "1",
            'objectives' => [],
            'children' => ['13','14','51','52'],
            'aamcPcrses' => [],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => "Practice-Based Learning & Improvement",
            'owningSchool' => "1",
            'objectives' => [],
            'children' => ['15','16','17'],
            'aamcPcrses' => [],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 4,
            'title' => "Interpersonal & Communication skills",
            'owningSchool' => "1",
            'objectives' => [],
            'children' => ['18','19','20'],
            'aamcPcrses' => [],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 5,
            'title' => "Professionalism",
            'owningSchool' => "1",
            'objectives' => [],
            'children' => ['21','22','23','24','25'],
            'aamcPcrses' => [],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 6,
            'title' => "Systems-Based Practice",
            'owningSchool' => "1",
            'objectives' => [],
            'children' => ['26','50'],
            'aamcPcrses' => [],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 7,
            'title' => "History Taking",
            'owningSchool' => "1",
            'objectives' => ['47719','77686'],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 8,
            'title' => "Physical Exam",
            'owningSchool' => "1",
            'objectives' => ['47720','77687'],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 9,
            'title' => "Oral Case Presentation",
            'owningSchool' => "1",
            'objectives' => ['47721','77688'],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0106'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 10,
            'title' => "Medical Notes",
            'owningSchool' => "1",
            'objectives' => ['47722','77689'],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0405'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 11,
            'title' => "Procedures and Skills",
            'owningSchool' => "1",
            'objectives' => ['47723','47724','77690','77691'],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 12,
            'title' => "Patient Management",
            'owningSchool' => "1",
            'objectives' => ['47725','47726','47727','77692','77693','77694'],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0103','aamc-pcrs-comp-c0108','aamc-pcrs-comp-c0109'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 13,
            'title' => "Problem-Solving and Diagnosis",
            'owningSchool' => "1",
            'objectives' => ['47730','63770','77697','77722'],
            'parent' => "2",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0203'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 14,
            'title' => "Knowledge for Practice",
            'owningSchool' => "1",
            'objectives' => ['47728','47729','77695','77696'],
            'parent' => "2",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0201','aamc-pcrs-comp-c0310'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 15,
            'title' => "Information Management",
            'owningSchool' => "1",
            'objectives' => ['47731','77698'],
            'parent' => "3",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0305','aamc-pcrs-comp-c0309'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 16,
            'title' => "Evidence-Based Medicine",
            'owningSchool' => "1",
            'objectives' => ['47732','47733','47736','77699','77700','77703'],
            'parent' => "3",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0202','aamc-pcrs-comp-c0204','aamc-pcrs-comp-c0306'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 17,
            'title' => "Reflection and Self-Improvement",
            'owningSchool' => "1",
            'objectives' => ['47734','47735','47737','77701','77702','77704'],
            'parent' => "3",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0301','aamc-pcrs-comp-c0307'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 18,
            'title' => "Doctor-Patient Relationship",
            'owningSchool' => "1",
            'objectives' => ['47738','77705'],
            'parent' => "4",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0107'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 19,
            'title' => "Communication and Information Sharing with Patients and Families",
            'owningSchool' => "1",
            'objectives' => [
                '47739',
                '47740',
                '47741',
                '47742',
                '47743',
                '77706',
                '77707',
                '77708',
                '77709',
                '77710',
            ],
            'parent' => "4",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0107','aamc-pcrs-comp-c0401','aamc-pcrs-comp-c0406'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 20,
            'title' => "Communication with the Medical Team",
            'owningSchool' => "1",
            'objectives' => ['47744','47745','47746','77711','77712','77713'],
            'parent' => "4",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0402','aamc-pcrs-comp-c0405'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 21,
            'title' => "Professional Relationships",
            'owningSchool' => "1",
            'objectives' => ['47747','47748','77714','77715'],
            'parent' => "5",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0501','aamc-pcrs-comp-c0505'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 22,
            'title' => "Boundaries and Priorities",
            'owningSchool' => "1",
            'objectives' => ['47749','77716'],
            'parent' => "5",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0502'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 23,
            'title' => "Work Habits, Appearance, and Etiquette",
            'owningSchool' => "1",
            'objectives' => ['47750','77717'],
            'parent' => "5",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0504'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 24,
            'title' => "Ethical Principles",
            'owningSchool' => "1",
            'objectives' => ['47751','77718'],
            'parent' => "5",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0506'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 25,
            'title' => "Institutional, Regulatory, and Professional Society Standards",
            'owningSchool' => "1",
            'objectives' => ['47752','77719'],
            'parent' => "5",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0506'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 26,
            'title' => "Healthcare Delivery Systems",
            'owningSchool' => "1",
            'objectives' => ['47753','47754','77720','77721'],
            'parent' => "6",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0403','aamc-pcrs-comp-c0603'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 27,
            'title' => "Interpersonal and Communication Skills",
            'owningSchool' => "3",
            'objectives' => [
                '66210',
                '66211',
                '66212',
                '66213',
                '66214',
                '66215',
                '66216',
                '83980',
                '83981',
                '83982',
                '83983',
                '83984',
                '83985',
                '83986',
            ],
            'children' => [],
            'aamcPcrses' => [],
            'programYears' => ['58','69']
        );

        $arr[] = array(
            'id' => 28,
            'title' => "Patient and Population-based Care",
            'owningSchool' => "3",
            'objectives' => [
                '66191',
                '66192',
                '66193',
                '66194',
                '66195',
                '66196',
                '66197',
                '83961',
                '83962',
                '83963',
                '83964',
                '83965',
                '83966',
                '83967',
            ],
            'children' => [],
            'aamcPcrses' => [],
            'programYears' => ['58','69']
        );

        $arr[] = array(
            'id' => 29,
            'title' => "Practice-based Learning and Improvement",
            'owningSchool' => "3",
            'objectives' => ['66206','66207','66208','66209','83976','83977','83978','83979'],
            'children' => [],
            'aamcPcrses' => [],
            'programYears' => ['58','69']
        );

        $arr[] = array(
            'id' => 30,
            'title' => "Professionalism",
            'owningSchool' => "3",
            'objectives' => [
                '66217',
                '66218',
                '66219',
                '66220',
                '66221',
                '66222',
                '66223',
                '83987',
                '83988',
                '83989',
                '83990',
                '83991',
                '83992',
                '83993',
            ],
            'children' => [],
            'aamcPcrses' => [],
            'programYears' => ['58','69']
        );

        $arr[] = array(
            'id' => 31,
            'title' => "Scientific and Clinical Foundations",
            'owningSchool' => "3",
            'objectives' => [
                '66198',
                '66199',
                '66200',
                '66201',
                '66202',
                '66203',
                '66204',
                '66205',
                '83968',
                '83969',
                '83970',
                '83971',
                '83972',
                '83973',
                '83974',
                '83975',
            ],
            'children' => [],
            'aamcPcrses' => [],
            'programYears' => ['58','69']
        );

        $arr[] = array(
            'id' => 32,
            'title' => "Systems-based Practice",
            'owningSchool' => "3",
            'objectives' => [
                '66224',
                '66225',
                '66226',
                '66227',
                '66228',
                '66229',
                '66230',
                '83994',
                '83995',
                '83996',
                '83997',
                '83998',
                '83999',
                '84000',
            ],
            'children' => [],
            'aamcPcrses' => [],
            'programYears' => ['58','69']
        );

        $arr[] = array(
            'id' => 50,
            'title' => "Systems Improvement",
            'owningSchool' => "1",
            'objectives' => ['63773','77725'],
            'parent' => "6",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0605'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 51,
            'title' => "Treatment",
            'owningSchool' => "1",
            'objectives' => ['63771','77723'],
            'parent' => "2",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0203'],
            'programYears' => ['42','67']
        );

        $arr[] = array(
            'id' => 52,
            'title' => "Inquiry and Discovery",
            'owningSchool' => "1",
            'objectives' => ['63772','77724'],
            'parent' => "2",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0206'],
            'programYears' => ['42','67']
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
