<?php

namespace IliosCoreBundleTestsDataLoader;

class AamcMethodData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[AM001] = array(
            'id' => "AM001",
            'description' => "Clinical Documentation Review",
            'sessionTypes' => ['134','152'            ]
        );

        $arr[AM002] = array(
            'id' => "AM002",
            'description' => "Clinical Performance Rating/Checklist",
            'sessionTypes' => ['135'            ]
        );

        $arr[AM003] = array(
            'id' => "AM003",
            'description' => "Exam - Institutionally Developed, Clinical Performance",
            'sessionTypes' => ['27','153'            ]
        );

        $arr[AM004] = array(
            'id' => "AM004",
            'description' => "Exam - Institutionally Developed, Written/ Computer-based",
            'sessionTypes' => ['137','154'            ]
        );

        $arr[AM005] = array(
            'id' => "AM005",
            'description' => "Exam - Institutionally Developed, Oral",
            'sessionTypes' => ['138'            ]
        );

        $arr[AM006] = array(
            'id' => "AM006",
            'description' => "Exam - Licensure, Clinical Performance",
            'sessionTypes' => ['139'            ]
        );

        $arr[AM007] = array(
            'id' => "AM007",
            'description' => "Exam - Licensure, Written/Computer-based",
            'sessionTypes' => ['140'            ]
        );

        $arr[AM008] = array(
            'id' => "AM008",
            'description' => "Exam - Nationally Normed/Standardized, Subject",
            'sessionTypes' => ['141','155'            ]
        );

        $arr[AM009] = array(
            'id' => "AM009",
            'description' => "Multisource Assessment",
            'sessionTypes' => ['142'            ]
        );

        $arr[AM010] = array(
            'id' => "AM010",
            'description' => "Narrative Assessment",
            'sessionTypes' => ['143','156'            ]
        );

        $arr[AM011] = array(
            'id' => "AM011",
            'description' => "Oral Patient Presentation",
            'sessionTypes' => ['144'            ]
        );

        $arr[AM012] = array(
            'id' => "AM012",
            'description' => "Participation",
            'sessionTypes' => ['145'            ]
        );

        $arr[AM013] = array(
            'id' => "AM013",
            'description' => "Peer Assessment",
            'sessionTypes' => ['146'            ]
        );

        $arr[AM014] = array(
            'id' => "AM014",
            'description' => "Portfolio-Based Assessment",
            'sessionTypes' => ['147'            ]
        );

        $arr[AM015] = array(
            'id' => "AM015",
            'description' => "Practical (Lab)",
            'sessionTypes' => ['148','157'            ]
        );

        $arr[AM016] = array(
            'id' => "AM016",
            'description' => "Research or Project Assessment",
            'sessionTypes' => ['149','158'            ]
        );

        $arr[AM017] = array(
            'id' => "AM017",
            'description' => "Self-Assessment",
            'sessionTypes' => ['150'            ]
        );

        $arr[AM018] = array(
            'id' => "AM018",
            'description' => "Stimulated Recall",
            'sessionTypes' => ['151'            ]
        );

        $arr[IM001] = array(
            'id' => "IM001",
            'description' => "Case-Based Instruction/Learning",
            'sessionTypes' => ['109'            ]
        );

        $arr[IM002] = array(
            'id' => "IM002",
            'description' => "Clinical Experience - Ambulatory",
            'sessionTypes' => ['34'            ]
        );

        $arr[IM003] = array(
            'id' => "IM003",
            'description' => "Clinical Experience - Inpatient",
            'sessionTypes' => ['35'            ]
        );

        $arr[IM004] = array(
            'id' => "IM004",
            'description' => "Concept Mapping",
            'sessionTypes' => ['111'            ]
        );

        $arr[IM005] = array(
            'id' => "IM005",
            'description' => "Conference",
            'sessionTypes' => ['112'            ]
        );

        $arr[IM006] = array(
            'id' => "IM006",
            'description' => "Demonstration",
            'sessionTypes' => ['114'            ]
        );

        $arr[IM007] = array(
            'id' => "IM007",
            'description' => "Discussion, Large Group (>12)",
            'sessionTypes' => ['115','121','132'            ]
        );

        $arr[IM008] = array(
            'id' => "IM008",
            'description' => "Discussion, Small Group (?12)",
            'sessionTypes' => ['30'            ]
        );

        $arr[IM009] = array(
            'id' => "IM009",
            'description' => "Games",
            'sessionTypes' => ['116'            ]
        );

        $arr[IM010] = array(
            'id' => "IM010",
            'description' => "Independent Learning",
            'sessionTypes' => ['19','21','113'            ]
        );

        $arr[IM011] = array(
            'id' => "IM011",
            'description' => "Journal Club",
            'sessionTypes' => ['117'            ]
        );

        $arr[IM012] = array(
            'id' => "IM012",
            'description' => "Laboratory",
            'sessionTypes' => ['23'            ]
        );

        $arr[IM013] = array(
            'id' => "IM013",
            'description' => "Lecture",
            'sessionTypes' => ['25','110','118','120','124'            ]
        );

        $arr[IM014] = array(
            'id' => "IM014",
            'description' => "Mentorship",
            'sessionTypes' => ['119'            ]
        );

        $arr[IM015] = array(
            'id' => "IM015",
            'description' => "Patient Presentation - Faculty",
            'sessionTypes' => ['122'            ]
        );

        $arr[IM016] = array(
            'id' => "IM016",
            'description' => "Patient Presentation - Learner",
            'sessionTypes' => ['123'            ]
        );

        $arr[IM017] = array(
            'id' => "IM017",
            'description' => "Peer Teaching",
            'sessionTypes' => ['125'            ]
        );

        $arr[IM018] = array(
            'id' => "IM018",
            'description' => "Preceptorship",
            'sessionTypes' => ['31'            ]
        );

        $arr[IM019] = array(
            'id' => "IM019",
            'description' => "Problem-Based Learning (PBL)",
            'sessionTypes' => ['29'            ]
        );

        $arr[IM020] = array(
            'id' => "IM020",
            'description' => "Reflection",
            'sessionTypes' => ['126'            ]
        );

        $arr[IM021] = array(
            'id' => "IM021",
            'description' => "Research",
            'sessionTypes' => ['127'            ]
        );

        $arr[IM022] = array(
            'id' => "IM022",
            'description' => "Role Play/Dramatization",
            'sessionTypes' => ['128'            ]
        );

        $arr[IM023] = array(
            'id' => "IM023",
            'description' => "Self-Directed Learning",
            'sessionTypes' => ['129'            ]
        );

        $arr[IM024] = array(
            'id' => "IM024",
            'description' => "Service Learning Activity",
            'sessionTypes' => ['130'            ]
        );

        $arr[IM025] = array(
            'id' => "IM025",
            'description' => "Simulation",
            'sessionTypes' => ['131'            ]
        );

        $arr[IM026] = array(
            'id' => "IM026",
            'description' => "Team-Based Learning (TBL)",
            'sessionTypes' => ['36'            ]
        );

        $arr[IM027] = array(
            'id' => "IM027",
            'description' => "Team-Building",
            'sessionTypes' => ['133'            ]
        );

        $arr[IM028] = array(
            'id' => "IM028",
            'description' => "Tutorial",
            'sessionTypes' => ['26'            ]
        );

        $arr[IM029] = array(
            'id' => "IM029",
            'description' => "Ward Rounds",
            'sessionTypes' => ['33'            ]
        );

        $arr[IM030] = array(
            'id' => "IM030",
            'description' => "Workshop Assessment",
            'sessionTypes' => ['28'            ]
        );

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
