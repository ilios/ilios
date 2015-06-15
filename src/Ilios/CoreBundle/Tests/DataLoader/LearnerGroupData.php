<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearnerGroupData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 4715,
            'title' => "Prologue Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [
                '46357',
                '46358',
                '46359',
                '46360',
                '46361',
                '46362',
                '46363',
                '46364',
                '46365',
                '46366',
                '46367',
                '46368',
                '46369',
                '46370',
                '46371',
                '46372',
                '46373',
                '46374',
                '46375',
                '46376',
                '46377',
                '46378',
                '46379',
                '46380',
                '46381',
                '46382',
                '46383',
                '54599',
            ],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4718,
            'title' => "Class of 2018",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4719,
            'title' => "ECSG (Class of 2018)",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4722',
                '4723',
                '4724',
                '4725',
                '4726',
                '4727',
                '4728',
                '4729',
                '4730',
                '4731',
                '4732',
                '4733',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4722,
            'title' => "ECSG  1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46117','46220','46262'],
            'instructorGroups' => [],
            'users' => ['11259','11264'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4723,
            'title' => "ECSG  2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46118','46221','46263'],
            'instructorGroups' => [],
            'users' => ['11262'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4724,
            'title' => "ECSG  3",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46119','46222','46264'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4725,
            'title' => "ECSG  4",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46120','46223','46265'],
            'instructorGroups' => [],
            'users' => ['11266'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4726,
            'title' => "ECSG  5",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46121','46224','46266'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4727,
            'title' => "ECSG  6",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46122','46225','46267'],
            'instructorGroups' => [],
            'users' => ['11256','11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4728,
            'title' => "ECSG  7",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46123','46226','46268'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4729,
            'title' => "ECSG  8",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46124','46227','46269'],
            'instructorGroups' => [],
            'users' => ['11261','11263'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4730,
            'title' => "ECSG  9",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46125','46228','46270'],
            'instructorGroups' => [],
            'users' => ['11269'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4731,
            'title' => "ECSG  10",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46126','46271','53090'],
            'instructorGroups' => [],
            'users' => ['11257'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4732,
            'title' => "ECSG  11",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46127','46230','46272'],
            'instructorGroups' => [],
            'users' => ['11258','11265','11268','11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4733,
            'title' => "ECSG  12",
            'location' => "",
            'cohort' => "66",
            'parent' => "4719",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46128','46231','46273'],
            'instructorGroups' => [],
            'users' => ['11260'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4734,
            'title' => "FPC SG Year 1 (Class of 2018)",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4735',
                '4736',
                '4737',
                '4738',
                '4739',
                '4740',
                '4741',
                '4742',
                '4743',
                '4744',
                '4745',
                '4746',
                '4747',
                '4748',
                '4749',
                '4750',
                '4751',
                '4752',
                '4753',
                '4754',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4735,
            'title' => "FPC SG 1",
            'location' => "S-158",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46288'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4736,
            'title' => "FPC SG 2",
            'location' => "S-160",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46289'],
            'instructorGroups' => [],
            'users' => ['11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4737,
            'title' => "FPC SG 3",
            'location' => "S-162",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46290'],
            'instructorGroups' => [],
            'users' => ['11266'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4738,
            'title' => "FPC SG 4",
            'location' => "S-168",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46291'],
            'instructorGroups' => [],
            'users' => ['11262'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4739,
            'title' => "FPC SG 5",
            'location' => "S-170",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46292'],
            'instructorGroups' => [],
            'users' => ['11261'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4740,
            'title' => "FPC SG 6",
            'location' => "S-172",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46293'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4741,
            'title' => "FPC SG 7",
            'location' => "S-173",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49803'],
            'instructorGroups' => [],
            'users' => ['11260'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4742,
            'title' => "FPC SG 8",
            'location' => "S-175",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49804'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4743,
            'title' => "FPC SG 9",
            'location' => "S-176",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49805'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4744,
            'title' => "FPC SG 10",
            'location' => "S-178",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49806'],
            'instructorGroups' => [],
            'users' => ['11258'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4745,
            'title' => "FPC SG 11",
            'location' => "S-162",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46282'],
            'instructorGroups' => [],
            'users' => ['11257'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4746,
            'title' => "FPC SG 12",
            'location' => "S-168",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46283'],
            'instructorGroups' => [],
            'users' => ['11264'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4747,
            'title' => "FPC SG 13",
            'location' => "S-170",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46284'],
            'instructorGroups' => [],
            'users' => ['11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4748,
            'title' => "FPC SG 14",
            'location' => "S-171",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46285'],
            'instructorGroups' => [],
            'users' => ['11265'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4749,
            'title' => "FPC SG 15",
            'location' => "S-172",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46286'],
            'instructorGroups' => [],
            'users' => ['11268','11269'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4750,
            'title' => "FPC SG 16",
            'location' => "S-173",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['46287'],
            'instructorGroups' => [],
            'users' => ['11256'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4751,
            'title' => "FPC SG 17",
            'location' => "S-174",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49807'],
            'instructorGroups' => [],
            'users' => ['11263'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4752,
            'title' => "FPC SG 18",
            'location' => "S-175",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49808'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4753,
            'title' => "FPC SG 19",
            'location' => "S-176",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49809'],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4754,
            'title' => "FPC SG 20",
            'location' => "S-178",
            'cohort' => "66",
            'parent' => "4734",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['49810'],
            'instructorGroups' => [],
            'users' => ['11259'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4759,
            'title' => "Organs Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4760,
            'title' => "M&N Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4761,
            'title' => "BMB Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4762,
            'title' => "I3 Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4763,
            'title' => "M3 Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4764,
            'title' => "LC/Prepilogue Entire Class",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4766,
            'title' => "Anatomy Lab",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4785',
                '4786',
                '4787',
                '4788',
                '4789',
                '4790',
                '4791',
                '4792',
                '4793',
                '4794',
                '4795',
                '4796',
                '4797',
                '4798',
                '4799',
                '4800',
                '4801',
                '4802',
                '4803',
                '4804',
                '4805',
                '4806',
                '4807',
                '4808',
                '4809',
                '4810',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4785,
            'title' => "Anatomy Lab 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11266'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4786,
            'title' => "Anatomy Lab 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4787,
            'title' => "Anatomy Lab 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4788,
            'title' => "Anatomy Lab 4",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11262','11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4789,
            'title' => "Anatomy Lab 5",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4790,
            'title' => "Anatomy Lab 6",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4791,
            'title' => "Anatomy Lab 7",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4792,
            'title' => "Anatomy Lab 8",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11260','11261'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4793,
            'title' => "Anatomy Lab 9",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4794,
            'title' => "Anatomy Lab 10",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4795,
            'title' => "Anatomy Lab 11",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4796,
            'title' => "Anatomy Lab 12",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4797,
            'title' => "Anatomy Lab 13",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11258'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4798,
            'title' => "Anatomy Lab 14",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11263'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4799,
            'title' => "Anatomy Lab 15",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11257','11264'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4800,
            'title' => "Anatomy Lab 16",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4801,
            'title' => "Anatomy Lab 17",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11265'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4802,
            'title' => "Anatomy Lab 18",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4803,
            'title' => "Anatomy Lab 19",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4804,
            'title' => "Anatomy Lab 20",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11269'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4805,
            'title' => "Anatomy Lab 21",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4806,
            'title' => "Anatomy Lab 22",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4807,
            'title' => "Anatomy Lab 23",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11259','11268'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4808,
            'title' => "Anatomy Lab 24",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4809,
            'title' => "Anatomy Lab 25",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4810,
            'title' => "Anatomy Lab 26",
            'location' => "",
            'cohort' => "66",
            'parent' => "4766",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4811,
            'title' => "East/West",
            'location' => "",
            'cohort' => "66",
            'children' => ['4812','4813'],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4812,
            'title' => "East",
            'location' => "",
            'cohort' => "66",
            'parent' => "4811",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11258','11260','11261','11262','11266','11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4813,
            'title' => "West",
            'location' => "",
            'cohort' => "66",
            'parent' => "4811",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256','11257','11259','11263','11264','11265','11268','11269','11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4814,
            'title' => "HP Rad Lab",
            'location' => "",
            'cohort' => "66",
            'children' => ['4815','4816','4817','4818'],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4815,
            'title' => "HP Rad Lab 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4814",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11260','11261','11262','11266','11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4816,
            'title' => "HP Rad Lab 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4814",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11258'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4817,
            'title' => "HP Rad Lab 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "4814",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11257','11263','11264','11265','11269','11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4818,
            'title' => "HP Rad Lab  4",
            'location' => "",
            'cohort' => "66",
            'parent' => "4814",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256','11259','11268'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4821,
            'title' => "PE Skills",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4822',
                '4823',
                '4824',
                '4825',
                '4826',
                '4827',
                '4828',
                '4829',
                '4830',
                '4831',
                '4832',
                '4833',
                '4834',
                '4835',
                '4836',
                '4837',
                '4838',
                '4839',
                '4840',
                '4841',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4822,
            'title' => "PE Skills 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4823,
            'title' => "PE Skills 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4824,
            'title' => "PE Skills 3",
            'location' => "S-159",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11266'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4825,
            'title' => "PE Skills 4",
            'location' => "S-163",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4826,
            'title' => "PE Skills 5",
            'location' => "S-170",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4827,
            'title' => "PE Skills 6",
            'location' => "S-172",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11261'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4828,
            'title' => "PE Skills 7",
            'location' => "S-174",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11262'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4829,
            'title' => "PE Skills 8",
            'location' => "S-176",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11260'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4830,
            'title' => "PE Skills 9",
            'location' => "S-178",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11258','11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4831,
            'title' => "PE Skills 10",
            'location' => "S-180",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4832,
            'title' => "PE Skills 11",
            'location' => "N-417",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4833,
            'title' => "PE Skills 12",
            'location' => "S-159",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11259','11269'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4834,
            'title' => "PE Skills 13",
            'location' => "S-163",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11265'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4835,
            'title' => "PE Skills 14",
            'location' => "N-423",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4836,
            'title' => "PE Skills 15",
            'location' => "S-170",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11264'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4837,
            'title' => "PE Skills 16",
            'location' => "S-172",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4838,
            'title' => "PE Skills 17",
            'location' => "S-174",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11263','11268'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4839,
            'title' => "PE Skills 18",
            'location' => "S-176",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4840,
            'title' => "PE Skills 19",
            'location' => "S-178",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256','11257'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4841,
            'title' => "PE Skills 20",
            'location' => "S-180",
            'cohort' => "66",
            'parent' => "4821",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4842,
            'title' => "Prologue Midpoint Feedback",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4843,
            'title' => "ACM",
            'location' => "",
            'cohort' => "66",
            'children' => ['4844','4845','4846','5006'],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4844,
            'title' => "Loman",
            'location' => "",
            'cohort' => "66",
            'parent' => "4843",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11258','11260','11262','11266','11267'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4845,
            'title' => "Harper",
            'location' => "",
            'cohort' => "66",
            'parent' => "4843",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11257','11264','11265','11268','11269','11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4846,
            'title' => "Jain",
            'location' => "",
            'cohort' => "66",
            'parent' => "4843",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256','11259','11263'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4847,
            'title' => "BMB Neuro Apprenticeship Group",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4848',
                '4849',
                '4850',
                '4851',
                '4852',
                '4853',
                '4854',
                '4855',
                '4856',
                '4857',
                '4858',
                '4859',
                '4860',
                '4861',
                '4862',
                '4863',
                '4864',
                '4865',
                '4866',
                '4867',
                '4868',
                '4869',
                '4870',
                '4871',
                '4872',
                '4873',
                '4874',
                '4875',
                '4876',
                '4877',
                '4878',
                '4879',
                '4880',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4848,
            'title' => "BMB Neuro Apprenticeship Group 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4849,
            'title' => "BMB Neuro Apprenticeship Group 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4850,
            'title' => "BMB Neuro Apprenticeship Group 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4851,
            'title' => "BMB Neuro Apprenticeship Group 4",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4852,
            'title' => "BMB Neuro Apprenticeship Group 5",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4853,
            'title' => "BMB Neuro Apprenticeship Group 6",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4854,
            'title' => "BMB Neuro Apprenticeship Group 7",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4855,
            'title' => "BMB Neuro Apprenticeship Group 8",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4856,
            'title' => "BMB Neuro Apprenticeship Group 9",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4857,
            'title' => "BMB Neuro Apprenticeship Group 10",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4858,
            'title' => "BMB Neuro Apprenticeship Group 11",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4859,
            'title' => "BMB Neuro Apprenticeship Group 12",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4860,
            'title' => "BMB Neuro Apprenticeship Group 13",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4861,
            'title' => "BMB Neuro Apprenticeship Group 14",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4862,
            'title' => "BMB Neuro Apprenticeship Group 15",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4863,
            'title' => "BMB Neuro Apprenticeship Group 16",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4864,
            'title' => "BMB Neuro Apprenticeship Group 17",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4865,
            'title' => "BMB Neuro Apprenticeship Group 18",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4866,
            'title' => "BMB Neuro Apprenticeship Group 19",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4867,
            'title' => "BMB Neuro Apprenticeship Group 20",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4868,
            'title' => "BMB Neuro Apprenticeship Group 21",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4869,
            'title' => "BMB Neuro Apprenticeship Group 22",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4870,
            'title' => "BMB Neuro Apprenticeship Group 23",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4871,
            'title' => "BMB Neuro Apprenticeship Group 24",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4872,
            'title' => "BMB Neuro Apprenticeship Group 25",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4873,
            'title' => "BMB Neuro Apprenticeship Group 26",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4874,
            'title' => "BMB Neuro Apprenticeship Group 27",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4875,
            'title' => "BMB Neuro Apprenticeship Group 28",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4876,
            'title' => "BMB Neuro Apprenticeship Group 29",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4877,
            'title' => "BMB Neuro Apprenticeship Group 30",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4878,
            'title' => "BMB Neuro Apprenticeship Group 31",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4879,
            'title' => "BMB Neuro Apprenticeship Group 32",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4880,
            'title' => "BMB Neuro Apprenticeship Group 33",
            'location' => "",
            'cohort' => "66",
            'parent' => "4847",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4881,
            'title' => "BMB Psych Apprenticeship Group",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4882',
                '4883',
                '4884',
                '4885',
                '4886',
                '4887',
                '4888',
                '4889',
                '4890',
                '4891',
                '4892',
                '4893',
                '4894',
                '4895',
                '4896',
                '4897',
                '4898',
                '4899',
                '4900',
                '4901',
                '4902',
                '4903',
                '4904',
                '4905',
                '4906',
                '4907',
                '4908',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4882,
            'title' => "BMB Psych Apprenticeship Group 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4883,
            'title' => "BMB Psych Apprenticeship Group 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4884,
            'title' => "BMB Psych Apprenticeship Group 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4885,
            'title' => "BMB Psych Apprenticeship Group 4",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4886,
            'title' => "BMB Psych Apprenticeship Group 5",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4887,
            'title' => "BMB Psych Apprenticeship Group 6",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4888,
            'title' => "BMB Psych Apprenticeship Group 7",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4889,
            'title' => "BMB Psych Apprenticeship Group 8",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4890,
            'title' => "BMB Psych Apprenticeship Group 9",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4891,
            'title' => "BMB Psych Apprenticeship Group 10",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4892,
            'title' => "BMB Psych Apprenticeship Group 11",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4893,
            'title' => "BMB Psych Apprenticeship Group 12",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4894,
            'title' => "BMB Psych Apprenticeship Group 13",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4895,
            'title' => "BMB Psych Apprenticeship Group 14",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4896,
            'title' => "BMB Psych Apprenticeship Group 15",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4897,
            'title' => "BMB Psych Apprenticeship Group 16",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4898,
            'title' => "BMB Psych Apprenticeship Group 17",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4899,
            'title' => "BMB Psych Apprenticeship Group 18",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4900,
            'title' => "BMB Psych Apprenticeship Group 19",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4901,
            'title' => "BMB Psych Apprenticeship Group 20",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4902,
            'title' => "BMB Psych Apprenticeship Group 21",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4903,
            'title' => "BMB Psych Apprenticeship Group 22",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4904,
            'title' => "BMB Psych Apprenticeship Group 23",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4905,
            'title' => "BMB Psych Apprenticeship Group 24",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4906,
            'title' => "BMB Psych Apprenticeship Group 25",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4907,
            'title' => "BMB Psych Apprenticeship Group 26",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4908,
            'title' => "BMB Psych Apprenticeship Group 27",
            'location' => "",
            'cohort' => "66",
            'parent' => "4881",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4909,
            'title' => "IPHE",
            'location' => "",
            'cohort' => "66",
            'children' => [
                '4915',
                '4916',
                '4917',
                '4918',
                '4919',
                '4920',
                '4921',
                '4922',
                '4923',
                '4924',
                '4925',
                '4926',
                '4927',
                '4928',
                '4929',
                '4930',
                '4931',
                '4932',
                '4933',
                '4934',
            ],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4915,
            'title' => "IPHE 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4916,
            'title' => "IPHE 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4917,
            'title' => "IPHE 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4918,
            'title' => "IPHE 4",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4919,
            'title' => "IPHE 5",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4920,
            'title' => "IPHE 6",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4921,
            'title' => "IPHE 7",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4922,
            'title' => "IPHE 8",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4923,
            'title' => "IPHE 9",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4924,
            'title' => "IPHE 10",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4925,
            'title' => "IPHE 11",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4926,
            'title' => "IPHE 12",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4927,
            'title' => "IPHE 13",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4928,
            'title' => "IPHE 14",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4929,
            'title' => "IPHE 15",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4930,
            'title' => "IPHE 16",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4931,
            'title' => "IPHE 17",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4932,
            'title' => "IPHE 18",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4933,
            'title' => "IPHE 19",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4934,
            'title' => "IPHE 20",
            'location' => "",
            'cohort' => "66",
            'parent' => "4909",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 4973,
            'title' => "Curriculum Ambassadors (2015)",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5006,
            'title' => "Miller",
            'location' => "",
            'cohort' => "66",
            'parent' => "4843",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11261'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5022,
            'title' => "PRIME class of 2018",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5225,
            'title' => "Organs Path Lab",
            'location' => "",
            'cohort' => "66",
            'children' => ['5226','5227','5228'],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5226,
            'title' => "Organs Path Lab 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "5225",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11257','11259','11260','11261','11263','11264','11265','11269'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5227,
            'title' => "Organs Path Lab 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "5225",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256','11258','11267','11268','11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5228,
            'title' => "Organs Path Lab 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "5225",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11262','11266'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5253,
            'title' => "Patient Simulator Groups",
            'location' => "",
            'cohort' => "66",
            'children' => ['5254','5255','5256'],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
                '11272',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5254,
            'title' => "Patient Simulator Group 1",
            'location' => "",
            'cohort' => "66",
            'parent' => "5253",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11258','11259','11260','11263','11268','11269','11270'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5255,
            'title' => "Patient Simulator Group 2",
            'location' => "",
            'cohort' => "66",
            'parent' => "5253",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11261','11262','11265','11266'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5256,
            'title' => "Patient Simulator Group 3",
            'location' => "",
            'cohort' => "66",
            'parent' => "5253",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['11256','11257','11264','11267','11272'],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5358,
            'title' => "Class of 2018 for UME",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
                '11272',
            ],
            'instructorUsers' => []
        );

        $arr[] = array(
            'id' => 5391,
            'title' => "UME Entire Class ",
            'location' => "",
            'cohort' => "66",
            'children' => [],
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
                '11272',
            ],
            'instructorUsers' => []
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
