<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class DisciplineData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 2,
            'title' => "Anatomy",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => [
                '16470',
                '16474',
                '16476',
                '16483',
                '16484',
                '16485',
                '16490',
                '16492',
                '16496',
                '16497',
                '16500',
                '16501',
                '16503',
                '16504',
                '16509',
                '16512',
                '16513',
                '16528',
                '16533',
                '16540',
                '16545',
                '16547',
                '16551',
                '16552',
                '16553',
                '16555',
                '16556',
            ]
        );

        $arr[] = array(
            'id' => 3,
            'title' => "Anesthesiology",
            'owningSchool' => "1",
            'courses' => ['551'],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 4,
            'title' => "Behavioral Science",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16494','16516','16517','16523','16534','16538']
        );

        $arr[] = array(
            'id' => 5,
            'title' => "Biochemistry",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16480','16482','16486','16544','16561']
        );

        $arr[] = array(
            'id' => 8,
            'title' => "Cell Biology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16471','16482','16526']
        );

        $arr[] = array(
            'id' => 9,
            'title' => "Clinical and Translational Research",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 10,
            'title' => "Clinical Skills",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16492','16497','16500','16501','16509','16528','16533','16540']
        );

        $arr[] = array(
            'id' => 11,
            'title' => "Complementary/Alternative Healthcare",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 12,
            'title' => "Problem Solving",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 13,
            'title' => "Cultural Competence",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16516','16517','16523']
        );

        $arr[] = array(
            'id' => 15,
            'title' => "Embryology",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16470','16545','16547','16551','16556']
        );

        $arr[] = array(
            'id' => 16,
            'title' => "Emergency Medicine",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16468','16487','16522']
        );

        $arr[] = array(
            'id' => 18,
            'title' => "Epidemiology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 19,
            'title' => "Ethics, Medical",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16514']
        );

        $arr[] = array(
            'id' => 21,
            'title' => "Genetics",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16495','16498','16499','16507','16546']
        );

        $arr[] = array(
            'id' => 22,
            'title' => "Geriatrics",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 25,
            'title' => "Histology",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16475','16477','16479','16484','16485','16502','16503','16505']
        );

        $arr[] = array(
            'id' => 27,
            'title' => "Human Sexuality/Sexual Functioning",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 28,
            'title' => "Humanities, Medical",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 29,
            'title' => "Immunology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 31,
            'title' => "Health Informatics",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 32,
            'title' => "Global Health Issues",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 35,
            'title' => "Microbiology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 37,
            'title' => "Neurology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16488','16491']
        );

        $arr[] = array(
            'id' => 39,
            'title' => "Surgical Specialties",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 40,
            'title' => "Nutrition",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 41,
            'title' => "Obstetrics and Gynecology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 42,
            'title' => "Occupational Health/Medicine",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 48,
            'title' => "Pathology",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16524','16526','16535']
        );

        $arr[] = array(
            'id' => 49,
            'title' => "Pediatrics",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 50,
            'title' => "Pharmacology",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16473','16482','16489','16491','16493','16515','16519']
        );

        $arr[] = array(
            'id' => 51,
            'title' => "Physiology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16488','16491']
        );

        $arr[] = array(
            'id' => 52,
            'title' => "Prevention/Health Maintenance",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 53,
            'title' => "Professional Development",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16469','16472','16510','16511','16514','16541','16550']
        );

        $arr[] = array(
            'id' => 54,
            'title' => "Psychiatry",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 55,
            'title' => "Public Health",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 57,
            'title' => "Radiology",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => ['16478','16554']
        );

        $arr[] = array(
            'id' => 59,
            'title' => "Societal Problems",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 60,
            'title' => "Surgery",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 101,
            'title' => "Anatomy",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 102,
            'title' => "Bioanalysis/Clinical Chemistry",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 103,
            'title' => "Biochemistry",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 104,
            'title' => "Biotechnology",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 105,
            'title' => "Complementary and Alternative Medicine",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 106,
            'title' => "Dispensing and distribution systems",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 107,
            'title' => "Drug Information",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 108,
            'title' => "Economics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 109,
            'title' => "Epidemiology and Biostatistics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 110,
            'title' => "Ethics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 111,
            'title' => "Extemporaneous compounding",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 112,
            'title' => "Genetics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 113,
            'title' => "Health care delivery systems",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 114,
            'title' => "History of Medicine",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 115,
            'title' => "Immunology",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 116,
            'title' => "Informatics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 117,
            'title' => "Law & regulatory affairs",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 118,
            'title' => "Literature evaluation and research design",
            'owningSchool' => "3",
            'courses' => ['543'],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 119,
            'title' => "Medication safety",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 120,
            'title' => "Medicinal Chemistry",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 121,
            'title' => "Microbiology",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 122,
            'title' => "Molecular Biology",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 123,
            'title' => "Pathology",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 124,
            'title' => "Patient assessment laboratory",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 125,
            'title' => "Pharmaceutics/Biopharmaceutics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 126,
            'title' => "Pharmacogenomics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 127,
            'title' => "Pharmacognosy",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 128,
            'title' => "Pharmacokinetics",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 129,
            'title' => "Pharmacotherapy",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 130,
            'title' => "Pharmacy practice and pharmacist-provided care",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 131,
            'title' => "Practice Management",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 132,
            'title' => "Professional communication",
            'owningSchool' => "3",
            'courses' => ['543'],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 133,
            'title' => "Social & behavioral aspects of practice",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 134,
            'title' => "Special populations",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 135,
            'title' => "Toxicology",
            'owningSchool' => "3",
            'courses' => [],
            'programYears' => ['58','69'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 136,
            'title' => "Family Medicine",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 137,
            'title' => "Internal Medicine",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 145,
            'title' => "Acute Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 146,
            'title' => "Ambulatory Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 147,
            'title' => "Biostatistics",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 148,
            'title' => "Chronic Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 149,
            'title' => "Clinical Reasoning",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 150,
            'title' => "Communication Skills",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16492','16497','16500','16501','16509','16528','16533','16538','16540']
        );

        $arr[] = array(
            'id' => 151,
            'title' => "Community Health",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16572']
        );

        $arr[] = array(
            'id' => 152,
            'title' => "Continuing Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 153,
            'title' => "Critical Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 154,
            'title' => "Diagnosis of Disease",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 155,
            'title' => "Domestic Violence/Abuse",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 156,
            'title' => "End-of-Life Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 157,
            'title' => "Evidence-Based Medicine",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 158,
            'title' => "Determinants of Health",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16494','16516','16517','16523','16538']
        );

        $arr[] = array(
            'id' => 159,
            'title' => "Gender and Cultural Bias",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16516','16517','16523']
        );

        $arr[] = array(
            'id' => 160,
            'title' => "Health Care Financing",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16534']
        );

        $arr[] = array(
            'id' => 161,
            'title' => "Health Care Quality Improvement",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 162,
            'title' => "Health Care Systems",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16534']
        );

        $arr[] = array(
            'id' => 163,
            'title' => "Human Development/Life Cycle",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16470','16545','16551']
        );

        $arr[] = array(
            'id' => 164,
            'title' => "Human Sexual/Gender Development",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 165,
            'title' => "Jurisprudence, Medical",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 166,
            'title' => "Neuroscience",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => ['16483','16490']
        );

        $arr[] = array(
            'id' => 167,
            'title' => "Organ System Pathophysiology",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 168,
            'title' => "Pain Management",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 169,
            'title' => "Palliative Care",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 170,
            'title' => "Physical Diagnosis",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 171,
            'title' => "Population-Based Medicine",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 172,
            'title' => "Socioeconomics, Medical",
            'owningSchool' => "1",
            'courses' => ['595'],
            'programYears' => ['42','67'],
            'sessions' => []
        );

        $arr[] = array(
            'id' => 173,
            'title' => "Substance Abuse",
            'owningSchool' => "1",
            'courses' => [],
            'programYears' => ['42','67'],
            'sessions' => []
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
